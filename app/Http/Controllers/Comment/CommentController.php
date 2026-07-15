<?php

namespace App\Http\Controllers\Comment;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCommentRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{


    private function resourceResponse($resource = null, string $message = "Success", int $code = 200)
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

    /**
     * Display a listing of the resource.
     */
    public function index(IndexCommentRequest $request)
    {
        $data = $request->validated();

        $query = Comment::with(['user', 'votes']);

        if (($data['type'] ?? null) === 'post') {
            $query->where('post_id', $data['post_id']);
        }

        if (($data['type'] ?? null) === 'profile') {
            $query->where('profile_user_id',  $data['profile_user_id']);
        }

        $comments = $query->latest()->paginate(10);

        return $this->resourceResponse(
            CommentResource::collection($comments),
            'Comments retrieved successfully',
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

        return $this->resourceResponse(
            new CommentResource($comment),
            'Comment created successfully',
            201
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {

        if ($comment->user_id !== auth()->id()) {
            return $this->errorResponse('You are not authorized');
        }

        $data = $request->validated();
        $comment->update([
            'text' => $data['text'],
        ]);

        $comment->load(['user', 'votes']);

        return $this->resourceResponse(
            new CommentResource($comment),
            'Comment updated successfully',
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {

            return $this->errorResponse(
                'You are not authorized',
            );
        }

        $comment->delete();

        return $this->resourceResponse(
            null,
            'Comment deleted successfully',
            200
        );
    }
}
