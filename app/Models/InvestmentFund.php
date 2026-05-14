<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentFund extends Model
{
    protected $fillable = [
        'user_id', 'name', 'capital', 'current_value', 'status', 'distribution_frequency', 'currency', 'icon'
    ];

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

    public function assets()
    {
        return $this->hasMany(FundAsset::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }
}
