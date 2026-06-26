<?php

namespace App\Http\Controllers\Authentication\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User as User;

class AuthController extends Controller
{
    public function login() {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        // $token = $user->createToken('teken')->plainTextToken;


        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully.',
            'data' => new UserResource($user),

            // 'token' => $token,
        ], 201);
    }


    public function logout() {}
}
