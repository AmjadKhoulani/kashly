<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentFund extends Model
{
    public function equities()
    {
        return $this->morphMany(Equity::class, 'equitable');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
