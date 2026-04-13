<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCardInvoice extends Model
{
    protected $fillable = ['credit_card_id', 'competence_id', 'status', 'closed_amount'];

    public function creditCard() { return $this->belongsTo(CreditCard::class); }
    public function competence() { return $this->belongsTo(Competence::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
}
