<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $repository;

    public function __construct(AccountRepository $repository) {
        $this->repository = $repository;
    }

    public function index() {
        $accounts = $this->repository->getAll();
        return view('accounts.index', compact('accounts'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
        ]);

        $this->repository->store($validated);
        return redirect()->route('accounts.index')->with('success', 'Conta cadastrada com sucesso!');
    }

    public function update(Request $request, Account $account) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
        ]);

        $this->repository->update($account, $validated);
        return redirect()->route('accounts.index')->with('success', 'Conta atualizada!');
    }

    public function destroy(Account $account) {
        $this->repository->delete($account);
        return redirect()->route('accounts.index')->with('success', 'Conta removida!');
    }
}
