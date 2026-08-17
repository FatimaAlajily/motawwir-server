<?php

namespace App\Http\Controllers\Comment;

use App\Events\NotificationSentEvent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCommentRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private function resourceResponse($resource = null, string $message = "نجح", int $code = 200)
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

    /**
     * Display a listing of the resource.
     */
    public function index(IndexCommentRequest $request)
    {
        $data = $request->validated();

        $query = Comment::with(['user', 'votes'])
            ->withCount([
                'votes as upvotes' => fn($q) => $q->where('custom', 'upvote'),
                'votes as downvotes' => fn($q) => $q->where('custom', 'downvote'),
                'votes as ai_votes' => fn($q) => $q->where('custom', 'ai'),
            ])
            ->orderByRaw('(upvotes - downvotes - (ai_votes * 2)) DESC'); // الترتيب حسب صافي النقاط للتعليقات

        if (($data['type'] ?? null) === 'post') {
            $query->where('post_id', $data['post_id']);
        }

        if (($data['type'] ?? null) === 'profile') {
            $query->where('profile_user_id', $data['profile_user_id']);
        }

        $comments = $query->paginate(10);

        return $this->resourceResponse(
            [
                'data' => CommentResource::collection($comments->items()),
                'meta' => [
                    'current_page' => $comments->currentPage(),
                    'last_page'    => $comments->lastPage(),
                    'per_page'     => $comments->perPage(),
                    'total'        => $comments->total(),
                ],
            ],
            'تم جلب التعليقات بنجاح',
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $data = $request->validated();

        $comment = Comment::create([
            'text' => $data['text'],
            'type' => $data['type'],
            'post_id' => $data['post_id'] ?? null,
            'profile_user_id' => $data['profile_user_id'] ?? null,
            'user_id' => auth()->id(),
        ]);

        $comment->load(['user', 'votes']);

        $targetUserId = null;

        if ($data['type'] === 'post') {
            $targetUserId = $comment->post?->user_id;
        } elseif ($data['type'] === 'profile') {
            $targetUserId = $data['profile_user_id'];
        }
        if ($targetUserId && $targetUserId !== auth()->id()) {
            $notification = Notification::create([
                'type' => 'comment',
                'user_id' => $targetUserId,
                'from_user_id' => auth()->id(),
                'comment_id' => $comment->id,
            ]);

            $notification->load(['fromUser', 'vote', 'comment']);

            try {
                broadcast(new NotificationSentEvent($notification))->toOthers();
            } catch (\Throwable $e) {
                \Log::warning('Broadcast failed for comment notification: ' . $e->getMessage());
            }
        }

        return $this->resourceResponse(
            new CommentResource($comment),
            'تم إنشاء التعليق بنجاح',
            201
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return $this->errorResponse('لست مخولاً لتحديث هذا التعليق.');
        }

        $data = $request->validated();
        $comment->update([
            'text' => $data['text'],
        ]);

        $comment->load(['user', 'votes']);

        return $this->resourceResponse(
            new CommentResource($comment),
            'تم تحديث التعليق بنجاح',
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return $this->errorResponse('لست مخولاً لحذف هذا التعليق.');
        }

        $comment->delete();

        return $this->resourceResponse(
            null,
            'تم حذف التعليق بنجاح',
            200
        );
    }
}
