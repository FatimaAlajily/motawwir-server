<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_banned) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been banned' . ($user->ban_reason ? ": {$user->ban_reason}" : ''),
            ], 403);
        }

        return $next($request);
    }
}
