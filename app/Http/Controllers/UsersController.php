<?php

namespace App\Http\Controllers;

use App\Http\Requests\JSONAPIRequest;
use App\Models\User;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->fetchResources(User::class, 'users');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(JSONAPIRequest $request)
    {
        return $this->service->createResource(User::class, [
            'name' => $request->input('data.attributes.name'),
            'email' => $request->input('data.attributes.email'),
            'password' => Hash::make(($request->input('data.attributes.password'))),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \App\Http\Resources\JSONAPIResource
     */
    public function show(User $user)
    {
        return $this->service->fetchResource(User::class, $user, 'users');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \App\Http\Resources\JSONAPIResource
     */
    public function update(JSONAPIRequest $request, User $user)
    {
        $attributes = $request->input('data.attributes');
        if(isset($attributes['password'])){
            $attributes['password'] = Hash::make($attributes['password']);
        }
        return $this->service->updateResource($user, $attributes);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        return $this->service->deleteResource($user);
    }
}
