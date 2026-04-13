<?php

namespace App\Repositories;

use App\Models\CreditCard;
use Illuminate\Support\Facades\Auth;

class CreditCardRepository
{
    public function getAll()
    {
        return CreditCard::where('user_id', Auth::id())->orderBy('name')->get();
    }

    public function store(array $data)
    {
        $data['user_id'] = Auth::id();
        return CreditCard::create($data);
    }

    public function update(CreditCard $creditCard, array $data)
    {
        $creditCard->update($data);
        return $creditCard;
    }

    public function delete(CreditCard $creditCard)
    {
        return $creditCard->delete();
    }
}
