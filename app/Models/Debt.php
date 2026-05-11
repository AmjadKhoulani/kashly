<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'user_id', 'type', 'contact_name', 'amount', 
        'remaining_amount', 'due_date', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
