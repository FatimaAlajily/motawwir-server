<?php

namespace App\Http\Controllers\Vote;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vote\StoreVoteRequest;
use App\Http\Resources\VoteResource;
use App\Models\Post;
use App\Models\Vote;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    private const POINTS = [
        'upvote'   => 1,
        'downvote' => -1,
        'ai'       => -2,
    ];

    private function errorResponse(string $message = "Error", int $code = 403)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    private function resourceResponse($resource = null, string $message = "Success", int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $resource,
        ], $code);
    }

    public function store(StoreVoteRequest $request)
    {
        $data = $request->validated();
        $userId = auth()->id();

        $target = $data['type'] === 'post'
            ? Post::findOrFail($data['post_id'])
            : Comment::findOrFail($data['comment_id']);

        $owner = $target->user;

        if (!$owner) {
            return $this->errorResponse('Owner not found', 404);
        }

        if ($owner->id === $userId) {
            return $this->errorResponse('You cannot vote on your own content', 403);
        }

        $column = $data['type'] === 'post' ? 'post_id' : 'comment_id';
        $existingVote = Vote::where('user_id', $userId)
            ->where($column, $target->id)
            ->first();

        DB::transaction(function () use ($existingVote, $data, $target, $owner, $userId, $column) {
            // نفس التصويت مرة تانية => إلغاء (toggle off)
            if ($existingVote && $existingVote->custom === $data['custom']) {
                $owner->decrement('votra', self::POINTS[$existingVote->custom]);
                $existingVote->delete();
                return;
            }

            // تصويت بنوع مختلف => نلغي القديم ونطبق الجديد
            if ($existingVote) {
                $owner->decrement('votra', self::POINTS[$existingVote->custom]);
                $existingVote->update(['custom' => $data['custom']]);
                $owner->increment('votra', self::POINTS[$data['custom']]);
                return;
            }

            // تصويت جديد بالكامل
            Vote::create([
                'custom'     => $data['custom'],
                'user_id'    => $userId,
                'post_id'    => $data['type'] === 'post' ? $target->id : null,
                'comment_id' => $data['type'] === 'comment' ? $target->id : null,
            ]);

            $owner->increment('votra', self::POINTS[$data['custom']]);
        });

        $target->loadCount([
            'votes as upvotes'   => fn($q) => $q->where('custom', 'upvote'),
            'votes as downvotes' => fn($q) => $q->where('custom', 'downvote'),
            'votes as ai_votes'  => fn($q) => $q->where('custom', 'ai'),
        ]);

        return $this->resourceResponse(
            new VoteResource($target),
            'Vote processed successfully',
            200
        );
    }
}
