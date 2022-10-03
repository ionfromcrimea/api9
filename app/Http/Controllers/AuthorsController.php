<?php

namespace App\Http\Controllers;

use App\Http\Requests\JSONAPIRequest;
use App\Http\Resources\AuthorsCollection;
use App\Http\Resources\AuthorsResource;
use App\Http\Resources\JSONAPICollection;
use App\Http\Resources\JSONAPIResource;
use App\Models\Author;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AuthorsController extends Controller
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
//        $authors = Author::all();
//        return AuthorsResource::collection($authors);
//        $authors = QueryBuilder::for(Author::class)->allowedSorts([
//            'name',
//            'created_at',
//            'updated_at',
//        ])->jsonPaginate()->get();
//        ])->jsonPaginate();
        return $this->service->fetchResources(Author::class, 'authors');
        //        return new AuthorsCollection($authors);
//        return new JSONAPICollection($authors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\JSONAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
//    public function store(StoreAuthorRequest $request)
//    public function store(Request $request)
    public function store(JSONAPIRequest $request)
    {
//        $author = Author::create([
//            'name' => $request->input('data.attributes.name'),
//        ]);
//        return (new AuthorsResource($author))
//        return (new JSONAPIResource($author))
//            ->response()
//            ->header('Location', route('authors.show', ['author' => $author]));
        return $this->service->createResource(Author::class, $request->input('data.attributes'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Author $author
     * @return JSONAPIResource
     */
    public function show(Author $author)
    {
//        return new AuthorsResource($author);
//        return new JSONAPIResource($author);
        return $this->service->fetchResource($author);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\JSONAPIRequest $request
     * @param \App\Models\Author $author
     * @return JSONAPIResource
     */
    public function update(JSONAPIRequest $request, Author $author)
//    public function update(Request $request, Author $author)
    {
//        $author->update($request->input('data.attributes'));
//        return new AuthorsResource($author);
//        return new JSONAPIResource($author);
        return $this->service->updateResource($author, $request->input('data.attributes'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Author $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
//        $author->delete();
//        return response(null, 204);
        return $this->service->deleteResource($author);
    }
}
