<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundAsset extends Model
{
    protected $fillable = [
        'investment_fund_id', 'name', 'type', 'value', 'purchase_date', 'notes'
    ];

    public function fund()
    {
        return $this->belongsTo(InvestmentFund::class, 'investment_fund_id');
    }
}
