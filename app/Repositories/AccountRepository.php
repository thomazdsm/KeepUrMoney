<?php

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountRepository
{
    public function getAll()
    {
        return Account::where('user_id', Auth::id())->orderBy('name')->get();
    }

    public function store(array $data)
    {
        $data['user_id'] = Auth::id();
        return Account::create($data);
    }

    public function update(Account $account, array $data)
    {
        $account->update($data);
        return $account;
    }

    public function delete(Account $account)
    {
        return $account->delete();
    }
}
