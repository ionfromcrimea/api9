<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorsCollection;
use App\Models\Book;
use Illuminate\Http\Request;

class BooksAuthorsRelatedController extends Controller
{
    public function index(Book $book)
    {
        return new AuthorsCollection($book->authors);
    }
}
