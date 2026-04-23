<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $comment = Comment::create($validated);
        $comment->load('user');

        return response()->json(['data' => $comment], 201);
    }

    public function destroy(Request $request, Comment $comment)
    {
        // Check if user is the owner or an admin
        if ($comment->user_id !== $request->user()->id && $request->user()->role->value !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();
        return response()->json(null, 204);
    }
}
