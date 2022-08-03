<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/name', function (Request $request) {
    if ($request->user()->tokenCan('1234')) {
        return response()->json(['name' => $request->user()->name . ' !!!']);
    }
    return response()->json(['name' => $request->user()->name]);
});

Route::get('user/{id}', function (Request $request, $id){ //dd('444');
    $user = \App\Models\User::find($id);
    if(!$user) return response('', 404);
    return $user;
});
