<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pocket extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'expenditure',
        'expenditure_date',
        'is_necessary',
        'comment',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
