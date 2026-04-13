<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Models\CreditCardInvoice;
use App\Models\Account;
use App\Repositories\CreditCardRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    protected $repository;

    public function __construct(CreditCardRepository $repository) {
        $this->repository = $repository;
    }

    public function index() {
        $cards = $this->repository->getAll();
        return view('credit_cards.index', compact('cards'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'limit' => 'required|numeric|min:0',
            'closing_day' => 'required|integer|min:1|max:31',
            'due_day' => 'required|integer|min:1|max:31',
            'color' => 'nullable|string|max:20',
        ]);
        $this->repository->store($validated);
        return redirect()->route('credit_cards.index')->with('success', 'Cartão cadastrado com sucesso!');
    }

    public function update(Request $request, CreditCard $creditCard) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'limit' => 'required|numeric|min:0',
            'closing_day' => 'required|integer|min:1|max:31',
            'due_day' => 'required|integer|min:1|max:31',
            'color' => 'nullable|string|max:20',
        ]);
        $this->repository->update($creditCard, $validated);
        return redirect()->route('credit_cards.index')->with('success', 'Cartão atualizado!');
    }

    public function destroy(CreditCard $creditCard) {
        $this->repository->delete($creditCard);
        return redirect()->route('credit_cards.index')->with('success', 'Cartão removido!');
    }

    // =========================================================================
    // O DISPARO EM LOTE (A Mágica de Pagar Fatura)
    // =========================================================================
    public function payInvoice(Request $request, CreditCardInvoice $invoice)
    {
        $validated = $request->validate(['account_id' => 'required|exists:accounts,id']);

        $pendingTransactions = $invoice->transactions()->where('status', 'pending')->get();
        if ($pendingTransactions->isEmpty()) {
            return redirect()->back()->with('error', 'Não há transações pendentes nesta fatura.');
        }

        $repo = app(TransactionRepository::class);
        $totalAmount = 0;

        foreach ($pendingTransactions as $t) {
            // Dispara o "pay" de cada continha silenciosamente
            $repo->pay($t, [
                'realized_date' => now(),
                'account_id' => $validated['account_id'],
                'realized_amount' => $t->planned_amount
            ]);
            $totalAmount += $t->planned_amount;
        }

        // Fecha a fatura com o valor final
        $invoice->update(['status' => 'paid', 'closed_amount' => $totalAmount]);

        return redirect()->back()->with('success', 'Fatura Paga! ' . $pendingTransactions->count() . ' compras receberam baixa simultaneamente.');
    }

    public function unpayInvoice(CreditCardInvoice $invoice)
    {
        $paidTransactions = $invoice->transactions()->where('status', 'paid')->get();
        $repo = app(TransactionRepository::class);

        // Estorna todas as comprinhas
        foreach ($paidTransactions as $t) {
            $repo->unpay($t);
        }

        $invoice->update(['status' => 'open', 'closed_amount' => null]);

        return redirect()->back()->with('success', 'Fatura Reaberta! O dinheiro voltou para a conta bancária e as compras voltaram a ficar pendentes.');
    }
}
