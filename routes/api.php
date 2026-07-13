<?php

use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Authentication\Member\AuthController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Content\PostController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Vote\VoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return 'Admin area';
    });
});


Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/posts', PostController::class);
    Route::post('/posts/{post}/save', [PostController::class, 'toggleSave']);
    Route::get('/saved-posts', [PostController::class, 'savedPosts']);
    Route::prefix('votes')->group(function () {
        Route::post('/', [VoteController::class, 'store']);
    });
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::post('/', [ProfileController::class, 'update']);
    });
    
    Route::prefix('chat')->group(function() {
        Route::get('messages', [MessageController::class, 'index']);
        Route::post('messages', [MessageController::class, 'store']);
        Route::delete('messages/{message}', [MessageController::class, 'destroy']);
    });
});




Route::get('comments', [CommentController::class, 'index']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
});
