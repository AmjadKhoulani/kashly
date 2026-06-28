<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemModule extends Model
{
    protected $fillable = [
        'id',
        'name_ar',
        'name_en',
        'icon',
        'description_ar',
        'description_en',
        'is_free',
        'status',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_modules', 'module_id', 'user_id')
                    ->withPivot('activated_at');
    }
}
