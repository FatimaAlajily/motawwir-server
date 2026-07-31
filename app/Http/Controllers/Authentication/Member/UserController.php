<?php

namespace App\Http\Controllers\Authentication\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private function successResponse($resource = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $resource,
        ], $code);
    }

    // ----------------- Search --------------------

    private function searchUsers($query, Request $request)
    {
        if (! $request->filled('search')) {
            return;
        }

        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('user_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // ----------------- Controller Actions --------------------

    public function index(Request $request)
    {
        $query = User::query()->orderByDesc('votra'); // بدل latest()

        $this->searchUsers($query, $request);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20);

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return $this->successResponse(new UserResource($user));
    }
}
