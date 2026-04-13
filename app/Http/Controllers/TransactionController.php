<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionRepo;

    public function __construct(TransactionRepository $transactionRepo)
    {
        $this->transactionRepo = $transactionRepo;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'competence_id'  => 'required|exists:competences,id',
            'category_id'    => 'required|exists:categories,id',
            'type'           => 'required|in:income,expense,transfer',
            'description'    => 'required|string|max:255',
            'due_date'       => 'required|date',
            'planned_amount' => 'required|numeric|min:0',
        ]);

        $this->transactionRepo->createPending($validated);
        return redirect()->back()->with('success', 'Lançamento planejado com sucesso!');
    }

    public function pay(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'realized_date'          => 'required|date',
            'realized_amount'        => 'required|numeric|min:0',
            'account_id'             => 'nullable|exists:accounts,id',
            'destination_account_id' => 'nullable|exists:accounts,id',
            'credit_card_id'         => 'nullable|exists:credit_cards,id',
        ]);

        try {
            $card = CreditCard::find($request->credit_card_id);
            if ($request->realized_amount > ($card->limit - $card->used_limit)) {
                return redirect()->back()->with('error', 'Limite do cartão excedido!');
            }

            $this->transactionRepo->pay($transaction, $validated);

            if(!empty($validated['credit_card_id'])){
                return redirect()->back()->with('success', 'Despesa alocada na fatura do cartão com sucesso!');
            }
            return redirect()->back()->with('success', 'Pagamento realizado!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function unpay(Transaction $transaction)
    {
        try {
            $this->transactionRepo->unpay($transaction);
            return redirect()->back()->with('success', 'Ação revertida para pendente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeInstallment(Request $request)
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'credit_card_id' => 'required|exists:credit_cards,id',
            'description'    => 'required|string|max:255',
            'total_amount'   => 'required|numeric|min:0.01',
            'installments'   => 'required|integer|min:1|max:360',
            'purchase_date'  => 'required|date',
        ]);

        $card = CreditCard::find($request->credit_card_id);
        if ($request->total_amount > ($card->limit - $card->used_limit)) {
            return redirect()->back()->with('error', 'Limite do cartão excedido!');
        }

        $this->transactionRepo->createInstallments($validated);
        return redirect()->back()->with('success', 'Compra processada no cartão com sucesso!');
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->competence->status === 'consolidated') {
            return redirect()->back()->with('error', 'Não é possível alterar um mês já consolidado.');
        }

        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'type'           => 'required|in:income,expense,transfer',
            'description'    => 'required|string|max:255',
            'due_date'       => 'required|date',
            'planned_amount' => 'required|numeric|min:0',
        ]);

        $transaction->update($validated);
        $this->transactionRepo->recalculateCompetenceTotals($transaction->competence);
        return redirect()->back()->with('success', 'Lançamento atualizado!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->competence->status === 'consolidated') {
            return redirect()->back()->with('error', 'Não é possível excluir um mês já consolidado.');
        }

        $competence = $transaction->competence;
        $transaction->delete();
        $this->transactionRepo->recalculateCompetenceTotals($competence);
        return redirect()->back()->with('success', 'Lançamento excluído com sucesso!');
    }

    public function destroyGroup($groupId)
    {
        $transactions = \App\Models\Transaction::where('installment_group_id', $groupId)->get();
        if ($transactions->isEmpty()) {
            return redirect()->back();
        }

        $competence = $transactions->first()->competence;
        \App\Models\Transaction::where('installment_group_id', $groupId)->where('status', 'pending')->delete();

        $repo = app(\App\Repositories\TransactionRepository::class);
        $repo->recalculateCompetenceTotals($competence);

        return redirect()->back()->with('success', 'Todas as parcelas pendentes desta compra foram excluídas!');
    }
}
