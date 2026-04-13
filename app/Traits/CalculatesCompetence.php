<?php

namespace App\Traits;

use App\Models\Competence;

trait CalculatesCompetence
{
    /**
     * Recalcula os totais (planejado e realizado) de uma competência específica
     * a partir de todas as transações atreladas a ela.
     */
    public function recalculateCompetenceTotals(Competence $competence)
    {
        // 1. Calcula o Planejado (Todas as transações, pendentes ou não)
        $plannedIncome = $competence->transactions()->where('type', 'income')->sum('planned_amount');
        $plannedExpense = $competence->transactions()->where('type', 'expense')->sum('planned_amount');

        // 2. Calcula o Realizado (Apenas transações com status 'paid')
        $realizedIncome = $competence->transactions()
            ->where('type', 'income')
            ->where('status', 'paid')
            ->sum('realized_amount');

        $realizedExpense = $competence->transactions()
            ->where('type', 'expense')
            ->where('status', 'paid')
            ->sum('realized_amount');

        // 3. Atualiza a competência
        $competence->update([
            'total_income_planned' => $plannedIncome,
            'total_expense_planned' => $plannedExpense,
            'total_income_realized' => $realizedIncome,
            'total_expense_realized' => $realizedExpense,
        ]);
    }
}
