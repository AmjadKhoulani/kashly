<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'notes', 'user_id', 'linked_user_id'];

    public function equities()
    {
        return $this->hasMany(Equity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function linkedUser()
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }
}
