<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//post

Route::apiResource('posts', PostController::class);

//auth

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//comment

Route::apiResource('posts.comments', CommentController::class)->middleware('auth:sanctum');;

// post statuses

Route::patch('/posts/{post}/status', [PostController::class, 'changeStatus'])
    ->middleware('auth:sanctum');

// flag 

Route::patch('/comments/{comment}/flag', [CommentController::class, 'flag'])
    ->middleware('auth:sanctum');