<?php

namespace App\Http\Controllers;

use App\Http\Requests\JSONAPIRequest;
use App\Http\Resources\BooksCollection;
use App\Http\Resources\BooksResource;
use App\Http\Resources\JSONAPICollection;
use App\Http\Resources\JSONAPIResource;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class BooksController extends Controller
{
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JSONAPICollection
     */
    public function index()
    {
//        $books = Book::all();
//        return new BooksCollection($books);
//        $books = QueryBuilder::for(Book::class)
//            ->allowedIncludes('authors')
//            ->allowedSorts([
//                'title',
//                'publication_year',
//                'created_at',
//                'updated_at',
//            ])->jsonPaginate();
//        return new JSONAPICollection($books);
        return $this->service->fetchResources(Book::class, 'books');
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
     * @param \App\Http\Requests\JSONAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(JSONAPIRequest $request)
    {
//        $book = Book::create([
//            'title' => $request->input('data.attributes.title'),
//            'description' => $request->input('data.attributes.description'),
//            'publication_year' => $request->input('data.attributes.publication_year'),
//        ]);
//        return (new JSONAPIResource($book))
//            ->response()
//            ->header('Location', route('books.show', [
//                'book' => $book,
//            ]));
//        return $this->service->createResource(Book::class, $request->input('data.attributes'));
        return $this->service->createResource(Book::class, $request->input('data.attributes'),
            $request->input('data.relationships'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Book $book
     * @return JSONAPIResource
     */
//    public function show(Book $book)
    public function show($book)
    {
//        $query = QueryBuilder::for(Book::where('id', $book))
//            ->allowedIncludes('authors')
//            ->firstOrFail();
//        return new BooksResource($query);
//        return new JSONAPIResource($query);
        return $this->service->fetchResource(Book::class, $book, 'books');
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
     * @param \App\Http\Requests\JSONAPIRequest $request
     * @param \App\Models\Book $book
     * @return JSONAPIResource
     */
//    public function update(JSONAPIRequest $request, Book $book)
//    {
//        $book->update($request->input('data.attributes'));
//        return new JSONAPIResource($book);
//return $this->service->updateResource($book, $request->input('data.attributes'));
//    }

    public function update(JSONAPIRequest $request, Book $book)
    {
        return $this->service->updateResource($book, $request->input('data.attributes'), $request->input('data.relationships'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
//        $book->delete();
//        return response(null, 204);
        return $this->service->deleteResource($book);
    }
}
