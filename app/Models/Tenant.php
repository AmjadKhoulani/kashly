<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['user_id', 'name', 'phone', 'email', 'national_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaseContracts()
    {
        return $this->hasMany(LeaseContract::class);
    }
}
