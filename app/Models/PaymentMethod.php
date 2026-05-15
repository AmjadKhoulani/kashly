<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = ['user_id', 'fund_id', 'parent_id', 'name', 'type', 'balance', 'currency', 'icon'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(InvestmentFund::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PaymentMethod::class, 'parent_id');
    }
}
