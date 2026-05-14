<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equity extends Model
{
    protected $fillable = [
        'partner_id', 'equitable_id', 'equitable_type', 'percentage', 'amount', 'equity_type'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function equitable()
    {
        return $this->morphTo();
    }
}
