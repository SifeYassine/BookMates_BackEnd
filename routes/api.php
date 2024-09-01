<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\genres\GenreController;
use App\Http\Controllers\api\roles\RoleController;
use App\Http\Controllers\api\books\BookController;
use App\Http\Controllers\api\book_posts\BookPostController;
use App\Http\Controllers\api\bookmarks\BookmarkController;
use App\Http\Controllers\api\exchangerequests\ExchangeRequestController;

// Auth routes
Route::group(['middleware' => 'api'], function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::post('auth/me', [AuthController::class, 'me']);
});

// Protected routes
Route::group(['middleware' => 'auth:api'], function () {
    // Roles routes
    Route::post('roles/create', [RoleController::class, 'create']);
    Route::get('roles/index', [RoleController::class, 'index']);
    Route::put('roles/update/{id}', [RoleController::class, 'update']);
    Route::delete('roles/delete/{id}', [RoleController::class, 'delete']);

    // Genres routes
    Route::post('genres/create', [GenreController::class, 'create']);
    Route::get('genres/index', [GenreController::class, 'index']);
    Route::put('genres/update/{id}', [GenreController::class, 'update']);
    Route::delete('genres/delete/{id}', [GenreController::class, 'delete']);

    // Books routes
    Route::post('books/create', [BookController::class, 'create']);
    Route::get('books/index', [BookController::class, 'index']);
    Route::post('books/update/{id}', [BookController::class, 'update']);
    Route::delete('books/delete/{id}', [BookController::class, 'delete']);

    // Book posts routes
    Route::post('book_posts/create', [BookPostController::class, 'create']);
    Route::get('book_posts/index', [BookPostController::class, 'index']);
    Route::get('book_posts/show/{id}', [BookPostController::class, 'show']);
    Route::put('book_posts/update/{id}', [BookPostController::class, 'update']);
    Route::delete('book_posts/delete/{id}', [BookPostController::class, 'delete']);

    // Bookmarks routes
    Route::post('bookmarks/create', [BookmarkController::class, 'create']);
    Route::get('bookmarks/index', [BookmarkController::class, 'index']);
    Route::delete('bookmarks/delete/{id}', [BookmarkController::class, 'delete']);

    // Exchnage requests routes
    Route::post('exchange_requests/create', [ExchangeRequestController::class, 'create']);
    Route::get('exchange_requests/index', [ExchangeRequestController::class, 'index']);
    Route::put('exchange_requests/update/{id}', [ExchangeRequestController::class, 'update']);
    Route::delete('exchange_requests/delete/{id}', [ExchangeRequestController::class, 'delete']);
});

