<?php

namespace App\Http\Controllers\Authentication\Admin;

use App\Events\MessageDeletedEvent;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Post;
use Illuminate\Http\Request;

class ContentModerationController extends Controller
{
    private function ensureAdmin(Request $request)
{
    abort_unless($request->user()?->role === 'admin', 403, 'Admin access required');
}

     private function successResponse($resource = null, string $message = "Success", int $code = 200 )
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

    public function forceDeleteMessage(Request $request , Message $message)
    {
        $this->ensureAdmin($request);

        $messageId = $message->id;
        $message->delete();
        broadcast(new MessageDeletedEvent($messageId))->toOthers();

        return $this->successResponse
        (
            [
                'id' => $messageId,
            ],
                'Message removed by admin'
        );
    }

    public function forceDeletePost(Request $request , Post $post)
    {
        $this->ensureAdmin($request);

        $postId = $post->id;
        $post->delete();

        return $this->successResponse(
            ['id' => $postId],
            'Post removed by admin'
        );

    }

    public function forceDeleteComment(Request $request , Comment $comment)
    {
        $this->ensureAdmin($request);

        $commentId = $comment->id;
        $comment->delete();

        return $this->successResponse(
            ['id' => $commentId],
            'Comment removed by admin'
        );
    }
}
