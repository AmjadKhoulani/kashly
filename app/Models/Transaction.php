<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'wallet_id', 'business_id', 'partner_id', 'amount', 
        'type', 'category', 'description', 'transaction_date'
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
