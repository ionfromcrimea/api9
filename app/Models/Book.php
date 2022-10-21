<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends AbstractAPIModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'publication_year'
    ];

    public function authors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return string
     */
    public function type()
    {
        return 'books';
    }
}
