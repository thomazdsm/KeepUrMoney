<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $repository;

    public function __construct(CategoryRepository $repository) {
        $this->repository = $repository;
    }

    public function index() {
        $categories = $this->repository->getAll();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:20',
        ]);

        $this->repository->store($validated);
        return redirect()->route('categories.index')->with('success', 'Categoria criada com sucesso!');
    }

    public function update(Request $request, Category $category) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:20',
        ]);

        $this->repository->update($category, $validated);
        return redirect()->route('categories.index')->with('success', 'Categoria atualizada!');
    }

    public function destroy(Category $category) {
        $this->repository->delete($category);
        return redirect()->route('categories.index')->with('success', 'Categoria removida!');
    }
}
