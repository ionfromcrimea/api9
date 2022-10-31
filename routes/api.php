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

Route::get('user/{id}', function (Request $request, $id) { //dd('444');
    $user = \App\Models\User::find($id);
    if (!$user) return response('', 404);
    return $user;
});


Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Users
//    Route::get('/users/current', function (Request $request) {
//        return $request->user();
//    });
    Route::get('/users/current', '\App\Http\Controllers\CurrentAuthenticatedUserController@show');

    Route::apiResource('users', '\App\Http\Controllers\UsersController');

    Route::get('users/{user}/relationships/comments', '\App\Http\Controllers\UsersCommentsRelationshipsController@index')
        ->name('users.relationships.comments');
    Route::patch('users/{user}/relationships/comments', '\App\Http\Controllers\UsersCommentsRelationshipsController@update')
        ->name('users.relationships.comments');
    Route::get('users/{user}/comments', '\App\Http\Controllers\UsersCommentsRelatedController@index')
        ->name('users.comments');

    // Authors
//    Route::get('/authors', ['\App\Http\Controllers\AuthorsController', 'index']);
//    Route::get('/authors/{author}', ['\App\Http\Controllers\AuthorsController', 'show']);
    Route::apiResource('authors', '\App\Http\Controllers\AuthorsController');

    Route::get('authors/{author}/relationships/books', '\App\Http\Controllers\AuthorsBooksRelationshipsController@index')
        ->name('authors.relationships.books');
    Route::patch('authors/{author}/relationships/books', '\App\Http\Controllers\AuthorsBooksRelationshipsController@update')
        ->name('authors.relationships.books');
    Route::get('authors/{author}/books', '\App\Http\Controllers\AuthorsBooksRelatedController@index')
        ->name('authors.books');

    // Books
    Route::apiResource('books', '\App\Http\Controllers\BooksController');

    Route::get('books/{book}/relationships/authors', '\App\Http\Controllers\BooksAuthorsRelationshipsController@index')
        ->name('books.relationships.authors');
    Route::patch('books/{book}/relationships/authors', '\App\Http\Controllers\BooksAuthorsRelationshipsController@update')
        ->name('books.relationships.authors');
    Route::get('books/{book}/authors', '\App\Http\Controllers\BooksAuthorsRelatedController@index')
        ->name('books.authors');

    Route::get('books/{book}/relationships/comments', '\App\Http\Controllers\BooksCommentsRelationshipsController@index')
        ->name('books.relationships.comments');
    Route::patch('books/{book}/relationships/comments', '\App\Http\Controllers\BooksCommentsRelationshipsController@update')
        ->name('books.relationships.comments');
    Route::get('books/{book}/comments', '\App\Http\Controllers\BooksCommentsRelatedController@index')
        ->name('books.comments');

    //Comments
    Route::apiResource('comments', '\App\Http\Controllers\CommentsController');

    Route::get('comments/{comment}/relationships/users', '\App\Http\Controllers\CommentsUsersRelationshipsController@index')
        ->name('comments.relationships.users');
    Route::patch('comments/{comment}/relationships/users', '\App\Http\Controllers\CommentsUsersRelationshipsController@update')
        ->name('comments.relationships.users');
    Route::get('comments/{comment}/users', '\App\Http\Controllers\CommentsUsersRelatedController@index')
        ->name('comments.users');

    Route::get('comments/{comment}/relationships/books', '\App\Http\Controllers\CommentsBooksRelationshipsController@index')
        ->name('comments.relationships.books');
    Route::patch('comments/{comment}/relationships/books', '\App\Http\Controllers\CommentsBooksRelationshipsController@update')
        ->name('comments.relationships.books');
    Route::get('comments/{comment}/books', '\App\Http\Controllers\CommentsBooksRelatedController@index')
        ->name('comments.books');
});
