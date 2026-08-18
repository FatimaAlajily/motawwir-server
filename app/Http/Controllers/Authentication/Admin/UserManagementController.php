<?php

namespace App\Http\Controllers\Authentication\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BanUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserManagementController extends Controller
{

    private function successResponse($resource = null, string $message = "نجح", int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $resource,
        ], $code);
    }

    private function errorResponse(string $message = "خطأ", int $code = 403)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }


    public function ban(BanUserRequest $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return $this->errorResponse('لا يمكنك حظر نفسك', 422);
        }

        if ($user->role === 'admin') {
            return $this->errorResponse('لا يمكنك حظر مدير آخر', 403);
        }

        if ($user->is_banned) {
            return $this->errorResponse('المستخدم محظور بالفعل', 422);
        }

        $data = $request->validated();

        $user->update([
            'is_banned' => true,
            'ban_reason' => $data['ban_reason'] ?? null,
            'banned_at' => now(),
        ]);

        $user->tokens()->delete();

        return $this->successResponse(new UserResource($user), 'تم حظر المستخدم بنجاح');
    }

    public function unban(User $user)
    {
        if (!$user->is_banned) {
            return $this->errorResponse('المستخدم غير محظور', 422);
        }

        $user->update([
            'is_banned' => false,
            'ban_reason' => null,
            'banned_at' => null,
        ]);

        return $this->successResponse(new UserResource($user), 'تم إلغاء حظر المستخدم بنجاح');
    }
}
