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

    /**
     * @return string
     */
    public function type()
    {
        return 'books';
    }
}
