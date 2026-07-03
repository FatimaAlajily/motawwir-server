<?php

namespace App\Http\Controllers\Comment;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'votes']);

        if ($request->type == 'post') {
            $query->where('post_id', $request->post_id);
        }

        if ($request->type == 'profile') {
            $query->where('profile_user_id', $request->profile_user_id);
        }

        $comments = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => CommentResource::collection($comments),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {

        $user = Auth::user();

        $comment = Comment::create([

            'text' => $request->text,

            'type' => $request->type,

            'post_id' => $request->post_id,

            'profile_user_id' => $request->profile_user_id,

            'user_id' => $user->id,

        ]);

        $comment->load(['user', 'votes']);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment created successfully',
            'data' => new CommentResource($comment),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        if ($comment->user_id != Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update this comment.'
            ], 403);
        }

        $comment->update([
            'text' => $request->text,
        ]);

        $comment->load(['user', 'votes']);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment updated successfully.',
            'data' => new CommentResource($comment),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        if ($comment->user_id != Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to delete this comment.'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully.'
        ], 200);
    }
}
