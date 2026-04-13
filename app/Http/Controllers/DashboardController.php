<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use App\Repositories\CompetenceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $competenceRepo;

    public function __construct(CompetenceRepository $competenceRepo)
    {
        $this->competenceRepo = $competenceRepo;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $this->competenceRepo->ensureFutureCompetencesExist($user->id);

        if ($request->has('month') && $request->has('year')) {
            $competence = Competence::where('user_id', $user->id)->where('month', $request->month)->where('year', $request->year)->firstOrFail();
        } else {
            $competence = Competence::where('user_id', $user->id)->where('status', 'current')->first();
        }

        $transactions = $competence->transactions()->with(['category', 'account', 'creditCardInvoice'])->orderBy('due_date')->get();
        $allCompetences = Competence::where('user_id', $user->id)->orderBy('year')->orderBy('month')->get();

        $categories = \App\Models\Category::where('user_id', $user->id)->orderBy('name')->get();
        $cards = \App\Models\CreditCard::where('user_id', $user->id)->orderBy('name')->get();
        $accounts = \App\Models\Account::where('user_id', $user->id)->orderBy('name')->get();

        // Busca as faturas Desta Competência
        $invoices = \App\Models\CreditCardInvoice::with('creditCard', 'transactions')->where('competence_id', $competence->id)->get();

        // Calcula o limite usado de cada cartão (Soma de tudo que está pendente vinculado a ele)
        foreach($cards as $card) {
            $used = \App\Models\Transaction::whereHas('creditCardInvoice', function($q) use($card) {
                $q->where('credit_card_id', $card->id);
            })->where('status', 'pending')->sum('planned_amount');

            $card->used_limit = $used;
            $card->available_limit = $card->limit - $used;
        }

        return view('dashboard.index', compact('competence', 'transactions', 'allCompetences', 'categories', 'cards', 'accounts', 'invoices'));
    }
}
