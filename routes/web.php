<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\PostController;

Route::get('/', function () {
    return view('welcome');
});

// User CRUD routes
Route::resource('users', UserController::class);

// Post CRUD routes
Route::resource('posts', PostController::class);
