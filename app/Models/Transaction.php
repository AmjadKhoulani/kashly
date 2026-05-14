<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'amount', 'type', 'category', 'description', 'transactionable_type', 'transactionable_id', 'user_id', 'transaction_date',
        'currency', 'exchange_rate', 'original_amount', 'invoice_path'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'original_amount' => 'decimal:2',
    ];

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
