<?php

namespace App\Http\Controllers;

use App\Http\Resources\BooksCollection;
use App\Http\Resources\BooksResource;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return BooksCollection
     */
    public function index()
    {
//        $books = Book::all();
//        return new BooksCollection($books);
        $books = QueryBuilder::for(Book::class)
            ->allowedIncludes('authors')
            ->allowedSorts([
            'title',
            'publication_year',
            'created_at',
            'updated_at',
        ])->jsonPaginate();
        return new BooksCollection($books);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreBookRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create([
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'publication_year' => $request->input('data.attributes.publication_year'),
        ]);
        return (new BooksResource($book))
            ->response()
            ->header('Location', route('books.show', [
                'book' => $book,
            ]));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Book $book
     * @return BooksResource
     */
//    public function show(Book $book)
    public function show($book)
    {
        $query = QueryBuilder::for(Book::where('id', $book))
            ->allowedIncludes('authors')
            ->firstOrFail();
        return new BooksResource($query);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateBookRequest $request
     * @param \App\Models\Book $book
     * @return BooksResource
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->input('data.attributes'));
        return new BooksResource($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response(null, 204);
    }
}
