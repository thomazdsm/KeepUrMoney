<?php

namespace App\Http\Controllers;

use App\Models\Recurrence;
use App\Models\Category;
use App\Repositories\RecurrenceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecurrenceController extends Controller
{
    protected $repository;

    public function __construct(RecurrenceRepository $repository) {
        $this->repository = $repository;
    }

    public function index() {
        $recurrences = $this->repository->getAll();
        $categories = Category::where('user_id', Auth::id())->orderBy('name')->get();
        return view('recurrences.index', compact('recurrences', 'categories'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'base_amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'recurrence_type' => 'required|in:fixed,variable',
        ]);

        $this->repository->store($validated);
        return redirect()->route('recurrences.index')->with('success', 'Recorrência cadastrada e injetada nos meses futuros!');
    }

    public function update(Request $request, Recurrence $recurrence) {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'base_amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'recurrence_type' => 'required|in:fixed,variable',
        ]);

        $this->repository->update($recurrence, $validated);
        return redirect()->route('recurrences.index')->with('success', 'Recorrência atualizada com sucesso!');
    }

    public function destroy(Recurrence $recurrence) {
        $this->repository->delete($recurrence);
        return redirect()->route('recurrences.index')->with('success', 'Recorrência removida!');
    }
}

