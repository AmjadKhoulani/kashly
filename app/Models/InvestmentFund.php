<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestmentFund extends Model
{
    protected $fillable = [
        'user_id', 'name', 'capital', 'current_value', 'status', 'distribution_frequency', 'currency', 'icon'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($fund) {
            $fund->equities()->delete();
            $fund->transactions()->delete();
            $fund->assets()->delete();
            $fund->distributions()->delete();
        });
    }

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

    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class, 'fund_id');
    }

    public function getTotalInvestedCapitalAttribute()
    {
        $capitalExpenses = Transaction::where('transactionable_id', $this->id)
            ->where('transactionable_type', self::class)
            ->where('category', 'مصاريف رأس مال')
            ->sum('amount');
            
        return ($this->capital ?? 0) + $capitalExpenses;
    }
}
