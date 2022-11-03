<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends AbstractAPIModel
{
    use HasFactory;

    protected $fillable = ['name'];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    public function type()
    {
        return 'authors';
    }
    public function scopeIntervalIDs(Builder $query, $limit1, $limit2): Builder
    {
//        return $query->where('status', '<=', $limit1)->where('status', '<=', $limit2);
        return $query->whereBetween('id', [$limit1, $limit2]);
    }
}
