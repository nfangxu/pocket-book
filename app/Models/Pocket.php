<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeDatepicker(Builder $builder, $start, $end)
    {
        return $builder->where('expenditure', '>=', trim($start))
            ->where('expenditure_date', '<=', trim($end));
    }
}
