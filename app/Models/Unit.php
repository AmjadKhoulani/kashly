<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['property_id', 'name', 'type', 'rent_amount', 'status'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function leaseContracts()
    {
        return $this->hasMany(LeaseContract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(LeaseContract::class)->where('status', 'active');
    }
}
