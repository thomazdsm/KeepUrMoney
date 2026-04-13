<?php

namespace App\Repositories;

use App\Models\Competence;
use App\Traits\CalculatesCompetence;
use Carbon\Carbon;

class CompetenceRepository
{
    use CalculatesCompetence;

    protected $recurrenceRepository;

    public function __construct(RecurrenceRepository $recurrenceRepository)
    {
        $this->recurrenceRepository = $recurrenceRepository;
    }

    /**
     * O Motor do Futuro: Garante que existam X meses gerados à frente.
     */
    public function ensureFutureCompetencesExist(int $userId, int $monthsAhead = 10)
    {
        $currentDate = Carbon::now();

        for ($i = 0; $i <= $monthsAhead; $i++) {
            $targetDate = $currentDate->copy()->addMonths($i);

            // Determina o status (O mês atual é 'current', os próximos são 'future')
            $status = ($i === 0) ? 'current' : 'future';

            // Busca ou cria a competência (a "Gaveta" do mês)
            $competence = Competence::firstOrCreate(
                [
                    'user_id' => $userId,
                    'month'   => $targetDate->month,
                    'year'    => $targetDate->year,
                ],
                [
                    'status'  => $status,
                ]
            );

            // Sempre que criar uma nova competência que acabou de nascer no banco,
            // injetamos as recorrências (contas de luz, salário, etc) nela.
            if ($competence->wasRecentlyCreated) {
                $this->recurrenceRepository->applyRecurrencesToCompetence($competence);
                $this->recalculateCompetenceTotals($competence);
            }
        }
    }

    /**
     * O Fechamento: Congela o mês.
     */
    public function consolidate(Competence $competence)
    {
        // Trava a competência atual
        $competence->update(['status' => 'consolidated']);

        // Acha a próxima competência cronológica e transforma em 'current'
        $nextDate = Carbon::create($competence->year, $competence->month, 1)->addMonth();
        Competence::where('user_id', $competence->user_id)
            ->where('month', $nextDate->month)
            ->where('year', $nextDate->year)
            ->update(['status' => 'current']);

        // Roda o gerador para garantir que a janela de 10 meses continuou andando
        $this->ensureFutureCompetencesExist($competence->user_id);
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
