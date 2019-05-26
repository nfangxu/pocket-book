<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'ext_name',
        'comment'
    ];

    public function scopeExtCategory($builder)
    {
        return $builder->whereName('其他')->pluck('id');
    }
}
