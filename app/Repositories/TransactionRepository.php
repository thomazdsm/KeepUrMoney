<?php

namespace App\Repositories;

use App\Models\Competence;
use App\Models\Transaction;
use App\Models\CreditCard;
use App\Models\CreditCardInvoice;
use App\Models\Account;
use App\Traits\CalculatesCompetence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionRepository
{
    use CalculatesCompetence;

    // Calcula matematicamente a data do vencimento da fatura com base na compra
    private function calculateInvoiceDate(CreditCard $card, Carbon $purchaseDate)
    {
        $dueDate = $purchaseDate->copy()->setDay($card->due_day);

        // Se comprou no dia ou após o fechamento, cai no próximo ciclo
        if ($purchaseDate->day >= $card->closing_day) {
            $dueDate->addMonth();
        }

        // Se o vencimento é número menor que o fechamento (ex: fecha dia 25, vence dia 05 do MÊS SEGUINTE)
        if ($card->due_day < $card->closing_day) {
            $dueDate->addMonth();
        }

        return $dueDate;
    }

    public function createPending(array $data)
    {
        $transaction = Transaction::create(array_merge($data, [
            'status' => 'pending',
            'is_fixed' => false
        ]));

        $this->recalculateCompetenceTotals($transaction->competence);
        return $transaction;
    }

    public function pay(Transaction $transaction, array $paymentData)
    {
        if ($transaction->competence->status === 'consolidated') {
            throw new \Exception('Você não pode alterar uma transação de um mês fechado.');
        }

        // ==========================================================
        // CASO 1: O "Sushi no Crédito" (Movendo pra Fatura)
        // ==========================================================
        if (!empty($paymentData['credit_card_id'])) {
            $card = CreditCard::findOrFail($paymentData['credit_card_id']);
            $date = Carbon::parse($paymentData['realized_date'] ?? now());

            // Descobre em qual fatura futura isso vai cair
            $invoiceDueDate = $this->calculateInvoiceDate($card, $date);

            // Acha ou cria a gaveta daquela fatura futura
            $invoiceCompetence = Competence::firstOrCreate(
                ['user_id' => $transaction->competence->user_id, 'month' => $invoiceDueDate->month, 'year' => $invoiceDueDate->year],
                ['status' => 'future']
            );

            // Acha ou cria a Fatura correspondente
            $invoice = CreditCardInvoice::firstOrCreate(
                ['credit_card_id' => $card->id, 'competence_id' => $invoiceCompetence->id],
                ['status' => 'open']
            );

            // MÁGICA: Vincula à fatura, atualiza o valor, mas DEIXA PENDENTE!
            $transaction->update([
                'credit_card_invoice_id' => $invoice->id,
                'planned_amount'         => $paymentData['realized_amount'] ?? $transaction->planned_amount,
                'status'                 => 'pending', // Fundamental para consumir o limite!
                'realized_date'          => null,
                'realized_amount'        => null,
                'account_id'             => null,
                'destination_account_id' => null,
            ]);

            $this->recalculateCompetenceTotals($transaction->competence);
            return $transaction;
        }

        // ==========================================================
        // CASO 2: Pagamento Normal (Dinheiro, Débito, Pix, etc)
        // ==========================================================
        $transaction->update([
            'status'                 => 'paid',
            'realized_date'          => $paymentData['realized_date'] ?? Carbon::now(),
            'realized_amount'        => $paymentData['realized_amount'] ?? $transaction->planned_amount,
            'account_id'             => $paymentData['account_id'] ?? null,
            'destination_account_id' => $paymentData['destination_account_id'] ?? null,
        ]);

        // Desconta da conta origem
        if ($transaction->account_id) {
            $account = Account::find($transaction->account_id);
            if ($account) {
                if ($transaction->type === 'income') {
                    $account->balance += $transaction->realized_amount;
                } elseif ($transaction->type === 'expense' || $transaction->type === 'transfer') {
                    $account->balance -= $transaction->realized_amount;
                }
                $account->save();
            }
        }

        // Adiciona na conta destino (Apenas Transferências)
        if ($transaction->type === 'transfer' && $transaction->destination_account_id) {
            $destAccount = Account::find($transaction->destination_account_id);
            if ($destAccount) {
                $destAccount->balance += $transaction->realized_amount;
                $destAccount->save();
            }
        }

        $this->recalculateCompetenceTotals($transaction->competence);
        return $transaction;
    }

    public function unpay(Transaction $transaction)
    {
        if ($transaction->competence->status === 'consolidated') {
            throw new \Exception('Você não pode alterar um mês fechado.');
        }

        // Devolve o dinheiro para as contas bancárias (Estorno)
        if ($transaction->account_id && $transaction->realized_amount) {
            $account = Account::find($transaction->account_id);
            if ($account) {
                if ($transaction->type === 'income') {
                    $account->balance -= $transaction->realized_amount;
                } elseif ($transaction->type === 'expense' || $transaction->type === 'transfer') {
                    $account->balance += $transaction->realized_amount;
                }
                $account->save();
            }
        }

        if ($transaction->type === 'transfer' && $transaction->destination_account_id && $transaction->realized_amount) {
            $destAccount = Account::find($transaction->destination_account_id);
            if ($destAccount) {
                $destAccount->balance -= $transaction->realized_amount;
                $destAccount->save();
            }
        }

        // Volta pra pendente, mas SE estivesse na fatura, CONTINUA na fatura!
        $transaction->update([
            'status'                 => 'pending',
            'realized_date'          => null,
            'realized_amount'        => null,
            'account_id'             => null,
            'destination_account_id' => null,
        ]);

        // Se fazia parte de uma fatura e a fatura estava paga, reabre a fatura!
        if ($transaction->credit_card_invoice_id) {
            $invoice = CreditCardInvoice::find($transaction->credit_card_invoice_id);
            if ($invoice && $invoice->status === 'paid') {
                $invoice->update(['status' => 'open', 'closed_amount' => null]);
            }
        }

        $this->recalculateCompetenceTotals($transaction->competence);
        return $transaction;
    }

    public function createInstallments(array $data)
    {
        $installments = (int) $data['installments'];
        $amountPerInstallment = ((float) $data['total_amount']) / $installments;
        $purchaseDate = Carbon::parse($data['purchase_date']);
        $card = CreditCard::findOrFail($data['credit_card_id']);
        $userId = Auth::id();
        $groupId = (string) Str::uuid();

        $firstDueDate = $this->calculateInvoiceDate($card, $purchaseDate);

        for ($i = 1; $i <= $installments; $i++) {
            $dueDate = $firstDueDate->copy()->addMonths($i - 1);

            $competence = Competence::firstOrCreate(
                ['user_id' => $userId, 'month' => $dueDate->month, 'year' => $dueDate->year],
                ['status' => 'future']
            );

            $invoice = CreditCardInvoice::firstOrCreate(
                ['credit_card_id' => $card->id, 'competence_id' => $competence->id],
                ['status' => 'open']
            );

            Transaction::create([
                'installment_group_id'   => $groupId,
                'competence_id'          => $competence->id,
                'category_id'            => $data['category_id'],
                'credit_card_invoice_id' => $invoice->id,
                'type'                   => 'expense',
                'description'            => $data['description'],
                'planned_amount'         => $amountPerInstallment,
                'due_date'               => $dueDate,
                'status'                 => 'pending',
                'is_fixed'               => false,
                'installment_current'    => $installments > 1 ? $i : null,
                'installment_total'      => $installments > 1 ? $installments : null,
            ]);

            $this->recalculateCompetenceTotals($competence);
        }
    }
}
