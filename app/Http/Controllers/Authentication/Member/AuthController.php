<?php

namespace App\Http\Controllers\Authentication\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function successResponse($resource = null, string $message = "Success", int $code = 200, array $extra = [])
    {
        return response()->json(array_merge([
            'status' => 'success',
            'message' => $message,
            'data' => $resource,
        ], $extra), $code);
    }

    private function errorResponse(string $message = "Error", int $code = 403)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return $this->errorResponse('البريد الألكتروني غير موجود', 404);
        }

        if (!Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('البيانات غير صحيحة ', 401);
        }

        if ($user->is_banned) {
            return $this->errorResponse(
                'تم حظر حسابك ' . ($user->ban_reason ? ": {$user->ban_reason}" : ''),
                403
            );
        }

        $token = $user->createToken('token')->plainTextToken;

        return $this->successResponse(new UserResource($user), 'تم تسجيل الدخول بنجاح', 200, ['token' => $token]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        return $this->successResponse(new UserResource($user), 'تم إنشاء حسابك بنجاح', 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }
}