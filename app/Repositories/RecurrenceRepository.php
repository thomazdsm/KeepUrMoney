<?php

namespace App\Repositories;

use App\Models\Recurrence;
use App\Models\Competence;
use App\Models\Transaction;
use App\Traits\CalculatesCompetence;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecurrenceRepository
{
    use CalculatesCompetence;

    public function getAll()
    {
        return Recurrence::with('category')
            ->where('user_id', Auth::id())
            ->orderBy('description')
            ->get();
    }

    public function store(array $data)
    {
        $data['user_id'] = Auth::id();
        $recurrence = Recurrence::create($data);

        // Mágica: Sincroniza essa nova recorrência com todos os meses futuros abertos!
        $this->syncRecurrenceToFutureCompetences($recurrence);

        return $recurrence;
    }

    public function update(Recurrence $recurrence, array $data)
    {
        $recurrence->update($data);

        // Mágica: Atualiza o valor planejado nos meses futuros que ainda estão pendentes
        $this->updatePendingFutureTransactions($recurrence);

        return $recurrence;
    }

    public function delete(Recurrence $recurrence)
    {
        // Deleta as transações pendentes geradas por esta recorrência no futuro
        Transaction::where('description', $recurrence->description)
            ->where('is_fixed', true)
            ->where('status', 'pending')
            ->whereHas('competence', function($query) {
                $query->whereIn('status', ['current', 'future']);
            })->delete();

        return $recurrence->delete();
    }

    /**
     * Aplica UMA recorrência nova em todas as gavetas atuais e futuras.
     */
    protected function syncRecurrenceToFutureCompetences(Recurrence $recurrence)
    {
        $competences = Competence::where('user_id', $recurrence->user_id)
            ->whereIn('status', ['current', 'future'])
            ->get();

        foreach ($competences as $competence) {
            $dueDate = Carbon::create($competence->year, $competence->month, 10);

            Transaction::create([
                'competence_id' => $competence->id,
                'category_id' => $recurrence->category_id,
                'type' => $recurrence->type,
                'description' => $recurrence->description,
                'planned_amount' => $recurrence->base_amount,
                'due_date' => $dueDate,
                'status' => 'pending',
                'is_fixed' => true,
            ]);

            $this->recalculateCompetenceTotals($competence);
        }
    }

    /**
     * Atualiza o valor das projeções futuras se você tiver um aumento/mudança.
     */
    protected function updatePendingFutureTransactions(Recurrence $recurrence)
    {
        $transactions = Transaction::where('description', $recurrence->description)
            ->where('is_fixed', true)
            ->where('status', 'pending') // Só mexe no que não foi pago ainda
            ->whereHas('competence', function($q) {
                $q->whereIn('status', ['current', 'future']);
            })->get();

        foreach ($transactions as $transaction) {
            $transaction->update([
                'planned_amount' => $recurrence->base_amount,
                'category_id' => $recurrence->category_id
            ]);
            $this->recalculateCompetenceTotals($transaction->competence);
        }
    }

    // Mantém o método que já tínhamos feito antes para criar os 10 meses na virada de ano/mês
    public function applyAllRecurrencesToCompetence(Competence $competence)
    {
        $recurrences = Recurrence::where('user_id', $competence->user_id)->where('is_active', true)->get();
        foreach ($recurrences as $recurrence) {
            $exists = Transaction::where('competence_id', $competence->id)
                ->where('description', $recurrence->description)
                ->where('is_fixed', true)->exists();

            if (!$exists) {
                $dueDate = Carbon::create($competence->year, $competence->month, 10);
                Transaction::create([
                    'competence_id' => $competence->id,
                    'category_id' => $recurrence->category_id,
                    'type' => $recurrence->type,
                    'description' => $recurrence->description,
                    'planned_amount' => $recurrence->base_amount,
                    'due_date' => $dueDate,
                    'status' => 'pending',
                    'is_fixed' => true,
                ]);
            }
        }
    }

    public function applyRecurrencesToCompetence(Competence $competence)
    {
        $recurrences = Recurrence::where('user_id', $competence->user_id)->where('is_active', true)->get();

        foreach ($recurrences as $recurrence) {
            $exists = Transaction::where('competence_id', $competence->id)
                ->where('description', $recurrence->description)
                ->where('is_fixed', true)
                ->exists();

            if (!$exists) {
                $dueDate = Carbon::create($competence->year, $competence->month, 10);
                Transaction::create([
                    'competence_id' => $competence->id,
                    'category_id' => $recurrence->category_id,
                    'type' => $recurrence->type,
                    'description' => $recurrence->description,
                    'planned_amount' => $recurrence->base_amount,
                    'due_date' => $dueDate,
                    'status' => 'pending',
                    'is_fixed' => true,
                ]);
            }
        }
    }
}
