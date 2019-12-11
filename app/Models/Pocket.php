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

    protected $appends = [
        'category_name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeDatepicker(Builder $builder, $start, $end)
    {
        return $builder->where('expenditure_date', '>=', trim($start))
            ->where('expenditure_date', '<=', trim($end));
    }

    public function scopeSearch(Builder $builder, $search)
    {
        empty($search['category'] ?? null)
            ?: $builder->whereCategoryId($search['category']);

        empty($search['user'] ?? null)
            ?: $builder->whereUserId($search['user']);

        return $builder;
    }

    public function getCategoryNameAttribute()
    {
        return $this->category->ext_name ?: $this->category->name;
    }
}
