<?php

use App\Http\Controllers\Authentication\Admin\ContentModerationController;
use App\Http\Controllers\Authentication\Admin\UserManagementController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Authentication\Member\AuthController;
use App\Http\Controllers\Authentication\Member\UserController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Communication\NotificationController;
use App\Http\Controllers\Content\PostController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Vote\VoteController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//------------- NO MIDDLEWARE USER AUTH LOGIN AND RESGISTER -------------
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:3,1');
});

//----------------------------------- GUEST BROWING -----------------------------------------
//------------- VIEW POST LIST -------------
Route::get('/posts', [PostController::class, 'index']);
//------------- VIEW A SINGLE POST (READ ONLY - GUEST) -------------
Route::get('/posts/{post}', [PostController::class, 'show']);
//------------- VIEW Message LIST -------------
Route::get('messages', [MessageController::class, 'index']);
//------------- VIEW COMMENTS LIST -------------
Route::get('comments', [CommentController::class, 'index']);
//------------- VIEW USERS LIST -------------
Route::get('/users', [UserController::class, 'index']);
//------------- VIEW A USER  -------------
Route::get('/users/{user}', [UserController::class, 'show']);
//------------- VIEW A PROFILE (READ ONLY - GUEST) -------------
Route::get('/profile/{user}', [ProfileController::class, 'show']);

//------------- NOT BANNED NEED MIDDLE WARE -------------
Route::middleware(['auth:sanctum', 'banned'])->group(function () {

    //------------- VIEW CURRENT AUTHENTICATED USER -------------
    Route::get('/user', function (Request $request) {
        return response()->json([
        'status' => 'success',
        'message' => 'تم جلب بيانات المستخدم بنجاح',
        'data' => new UserResource($request->user()),
    ]); 
    });
    //------------- LOG OUT USER -------------
    Route::post('/logout', [AuthController::class, 'logout']);

    //------------- PROFILE ACCOUNT -------------
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::post('/', [ProfileController::class, 'update']);
    });


    //------------- POST ROUTE CRUD -------------
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    //------------- SAVE POST ROUTE CRUD -------------
    Route::post('/posts/{post}/save', [PostController::class, 'toggleSave']);
    Route::get('/saved-posts', [PostController::class, 'savedPosts']);

    //------------- CHAT MESSAGES CREATE DELETE VIEW -------------
    Route::prefix('chat')->group(function () {
        Route::post('messages', [MessageController::class, 'store'])->middleware('throttle:20,1');
        Route::delete('messages/{message}', [MessageController::class, 'destroy']);
    });

    //------------- COMMENT CRUD CREATE UPDATE DELETE -------------
    Route::post('comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

    //------------- VOTE UPVOTE DOWNVOTE AI REPUTATION -------------
    Route::prefix('votes')->group(function () {
        Route::post('/', [VoteController::class, 'store']);
    });
    //------------- UNREAD READ MARK NOTIFICATIONS -------------
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unReadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    });


    //------------- ADMIN PREMISSIONS AREA  -------------
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return 'Admin area';
        });
        //------------- ADMIN PREMISSIONS BAN UNBAN USERS -------------
        Route::post('/users/{user}/ban', [UserManagementController::class, 'ban'])->middleware('throttle:10,1');
        Route::post('/users/{user}/unban', [UserManagementController::class, 'unban'])->middleware('throttle:10,1');

        //------------- CONTENT MODERATION -------------
        Route::prefix('moderation')->group(function () {
            Route::delete('/messages/{message}', [ContentModerationController::class, 'forceDeleteMessage']);
            Route::delete('/comments/{comment}', [ContentModerationController::class, 'forceDeleteComment']);
            Route::delete('/posts/{post}', [ContentModerationController::class, 'forceDeletePost']);
        });
    });
});
