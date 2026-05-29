<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerPayment extends Model
{
    protected $fillable = [
        'ledger_entry_id', 'user_id',
        'amount', 'currency',
        'original_amount', 'original_currency', 'exchange_rate',
        'payment_date', 'notes',
    ];

    protected $casts = [
        'payment_date'     => 'date',
        'amount'           => 'decimal:2',
        'original_amount'  => 'decimal:2',
        'exchange_rate'    => 'decimal:4',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(LedgerEntry::class, 'ledger_entry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
