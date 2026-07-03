<?php

use App\Http\Controllers\Authentication\Member\AuthController;
use App\Http\Controllers\Content\PostController;
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

    Route::apiResource('/posts' , PostController::class);
});
