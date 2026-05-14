<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = [
        'investment_fund_id', 'gross_amount', 'net_amount', 'distribution_date', 'status'
    ];

    public function fund()
    {
        return $this->belongsTo(InvestmentFund::class, 'investment_fund_id');
    }
}
