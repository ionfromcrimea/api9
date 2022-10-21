<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends AbstractAPIModel
{
    use HasFactory;

    protected $fillable = [
        'message',
    ];

    /**
     * @return string
     */
    public function type()
    {
        return 'comments';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->user();
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function books()
    {
        return $this->book();
    }
}
