<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    protected $fillable = [
        'user_id', 'type', 'party_name', 'party_phone', 'description',
        'total_amount', 'paid_amount', 'currency',
        'installment_count', 'installment_amount', 'start_date', 'due_date',
        'status', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date'   => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'installment_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LedgerPayment::class)->where('type', 'payment');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(LedgerPayment::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(LedgerPayment::class)->where('type', 'charge');
    }

    // المبلغ المتبقي
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    // نسبة الإنجاز
    public function getProgressPercentAttribute(): float
    {
        if ($this->total_amount <= 0) return 0;
        return min(100, round(($this->paid_amount / $this->total_amount) * 100, 1));
    }

    // تسمية النوع بالعربي
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'receivable'  => 'مديني',
            'payable'     => 'أنا المدين',
            'installment' => 'تقسيط',
            'loan'        => 'قرض',
        };
    }

    // أيقونة النوع
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'receivable'  => '💸',
            'payable'     => '🏦',
            'installment' => '🛒',
            'loan'        => '📋',
        };
    }

    // لون النوع
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'receivable'  => 'emerald',
            'payable'     => 'rose',
            'installment' => 'amber',
            'loan'        => 'violet',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active'   => 'نشط',
            'settled'  => 'مسدّد',
            'overdue'  => 'متأخر',
            'partial'  => 'مدفوع جزئياً',
        };
    }

    // تحديث الحالة تلقائياً بناءً على المدفوع
    public function syncStatus(): void
    {
        if ($this->total_amount > 0 && $this->paid_amount >= $this->total_amount) {
            $this->status = 'settled';
        } elseif ($this->due_date && $this->due_date->isPast() && $this->paid_amount < $this->total_amount) {
            $this->status = 'overdue';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'active';
        }
        $this->save();
    }
}
