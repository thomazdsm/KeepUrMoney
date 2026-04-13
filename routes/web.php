<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecurrenceController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Rota Principal (Dashboard Financeiro)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Cadastros Base
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('accounts', AccountController::class)->except(['create', 'edit', 'show']);
    Route::resource('credit_cards', CreditCardController::class)->except(['create', 'edit', 'show']);
    Route::resource('recurrences', RecurrenceController::class)->except(['create', 'edit', 'show']);

    // Rotas de Transações
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/{transaction}/pay', [TransactionController::class, 'pay'])->name('transactions.pay');
    Route::post('/transactions/{transaction}/unpay', [TransactionController::class, 'unpay'])->name('transactions.unpay');
    Route::post('/transactions/installments', [TransactionController::class, 'storeInstallment'])->name('transactions.installments');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::delete('/transactions/group/{groupId}', [TransactionController::class, 'destroyGroup'])->name('transactions.destroyGroup');

    // Faturas de Cartão (Pagamento e Estorno)
    Route::post('/invoices/{invoice}/pay', [CreditCardController::class, 'payInvoice'])->name('invoices.pay');
    Route::post('/invoices/{invoice}/unpay', [CreditCardController::class, 'unpayInvoice'])->name('invoices.unpay');

    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
