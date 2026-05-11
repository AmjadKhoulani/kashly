<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equity extends Model
{
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
