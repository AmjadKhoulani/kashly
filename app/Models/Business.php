<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = ['name', 'owner_id', 'description'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
