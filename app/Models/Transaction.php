<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'competence_id', 'category_id', 'account_id', 'destination_account_id', 'credit_card_invoice_id',
        'type', 'description', 'due_date', 'planned_amount',
        'realized_date', 'realized_amount', 'status', 'is_fixed',
        'installment_group_id','installment_current', 'installment_total'
    ];

    protected $casts = [
        'due_date' => 'date',
        'realized_date' => 'date',
        'is_fixed' => 'boolean',
    ];

    public function competence() { return $this->belongsTo(Competence::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function account() { return $this->belongsTo(Account::class); }
    public function destinationAccount() { return $this->belongsTo(Account::class, 'destination_account_id'); }
    public function creditCardInvoice() { return $this->belongsTo(CreditCardInvoice::class); }
}
