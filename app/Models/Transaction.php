<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'amount', 'type', 'category', 'category_id', 'description', 'transactionable_type', 'transactionable_id', 'user_id', 'transaction_date',
        'currency', 'exchange_rate', 'original_amount', 'invoice_path', 'payment_method_id'
    ];

    protected $appends = ['amount_in_currency'];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'original_amount' => 'decimal:2',
    ];

    /**
     * Get the transaction amount in its original currency.
     */
    public function getAmountInCurrencyAttribute()
    {
        return $this->original_amount ?? $this->amount;
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
