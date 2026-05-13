<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    public function equities()
    {
        return $this->hasMany(Equity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
