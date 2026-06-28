<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeasePayment extends Model
{
    protected $fillable = [
        'lease_contract_id',
        'amount_due',
        'amount_paid',
        'due_date',
        'paid_date',
        'payment_method_id',
        'transaction_id',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function leaseContract()
    {
        return $this->belongsTo(LeaseContract::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
