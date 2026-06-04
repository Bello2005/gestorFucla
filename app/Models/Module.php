<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['slug', 'label', 'sort_order', 'read_only'];

    protected $casts = ['read_only' => 'boolean'];

    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }
}
