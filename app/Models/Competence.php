<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    protected $fillable = [
        'user_id', 'month', 'year', 'status',
        'total_income_planned', 'total_expense_planned',
        'total_income_realized', 'total_expense_realized'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function creditCardInvoices() { return $this->hasMany(CreditCardInvoice::class); }
}
