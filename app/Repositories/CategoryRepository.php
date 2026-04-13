<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryRepository
{
    public function getAll()
    {
        return Category::where('user_id', Auth::id())->orderBy('name')->get();
    }

    public function store(array $data)
    {
        $data['user_id'] = Auth::id();
        return Category::create($data);
    }

    public function update(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category)
    {
        // Aqui no futuro você pode colocar uma lógica para não deletar se tiver transações,
        // ou apenas desativar (is_active = false).
        return $category->delete();
    }
}

