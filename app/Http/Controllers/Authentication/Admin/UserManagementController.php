<?php

namespace App\Http\Controllers\Authentication\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BanUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
class UserManagementController extends Controller
{

 private function successResponse($resource = null, string $message = "Success", int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $resource,
        ], $code);
    }

    private function errorResponse(string $message = "Error", int $code = 403)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    public function index()
    {
    //     $users = User::latest()->paginate(20);
    //     return $this->successResponse(UserResource::collection($users));$users = User::latest()->paginate(20);

    // return $this->successResponse([
    //     'users' => UserResource::collection($users)->collection,
    //     'meta' => [
    //         'current_page' => $users->currentPage(),
    //         'last_page' => $users->lastPage(),
    //         'per_page' => $users->perPage(),
    //         'total' => $users->total(),
    //     ],
    // ]);

    $users = User::latest()->paginate(20);
        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return $this->successResponse(new UserResource($user));
    }
    
    public function ban(BanUserRequest $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return $this->errorResponse('You cannot ban yourself', 422);
        }

        if ($user->role === 'admin') {
            return $this->errorResponse('You cannot ban another admin', 403);
        }

        if ($user->is_banned) 
        {
        return $this->errorResponse('User is already banned', 422);
        }

        $data = $request->validated();

        $user->update([
            'is_banned' => true,
            'ban_reason' => $data['ban_reason'] ?? null,
            'banned_at' => now(),
        ]);

        $user->tokens()->delete();

        return $this->successResponse(new UserResource($user), 'User banned successfully');
    }

      public function unban(User $user)
    {
        if (!$user->is_banned) 
        {
        return $this->errorResponse('User is not banned', 422);
        }

        $user->update([
            'is_banned' => false,
            'ban_reason' => null,
            'banned_at' => null,
        ]);

        return $this->successResponse(new UserResource($user), 'User unbanned successfully');
    }
}
