<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $fillable = ['user_id', 'name', 'limit', 'closing_day', 'due_day', 'color'];

    public function user() { return $this->belongsTo(User::class); }
    public function invoices() { return $this->hasMany(CreditCardInvoice::class); }
}
