<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorsCollection;
use App\Http\Resources\AuthorsResource;
use App\Models\Author;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AuthorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AuthorsCollection
     */
    public function index()
    {
//        $authors = Author::all();
//        return AuthorsResource::collection($authors);
        $authors = QueryBuilder::for(Author::class)->allowedSorts([
            'name',
            'created_at',
            'updated_at',
//        ])->jsonPaginate()->get();
        ])->jsonPaginate();
        return new AuthorsCollection($authors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreAuthorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreAuthorRequest $request)
//    public function store(Request $request)
    {
        $author = Author::create([
            'name' => $request->input('data.attributes.name'),
        ]);
        return (new AuthorsResource($author))
            ->response()
            ->header('Location', route('authors.show', ['author' => $author]));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Author $author
     * @return AuthorsResource
     */
    public function show(Author $author)
    {
        return new AuthorsResource($author);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateAuthorRequest $request
     * @param \App\Models\Author $author
     * @return AuthorsResource
     */
    public function update(UpdateAuthorRequest $request, Author $author)
//    public function update(Request $request, Author $author)
    {
        $author->update($request->input('data.attributes'));
        return new AuthorsResource($author);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Author $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
        $author->delete();
        return response(null, 204);
    }
}
