<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;

class CommentController extends Controller implements HasMiddleware
{
     public static function middleware() {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
    // List all comments for a specific post
public function index($postId)
{
    $user = auth()->user();

    $comments = Comment::where('post_id', $postId)
        ->where(function($q) use ($user) {
            $q->whereNull('flagged_at');
            if ($user) {
                $q->orWhereHas('post', fn($q2) => $q2->where('user_id', $user->id));
            }
        })
        ->get();

    return response()->json($comments);
}

    // Store a new comment for a specific post
    public function store(Request $request, Post $post)
    {
        // Validate input
        $fields = $request->validate([
            'content' => 'required|string',
        ]);

        // Create comment linked to post and logged-in user
        $comment = $post->comments()->create([
            'content' => $fields['content'],
            'user_id' => $request->user()->id, 
        ]);

        return response()->json($comment, 201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post, Comment $comment)
    {
        return $comment;
    }

   public function update(Request $request, Post $post, Comment $comment)
    {
        // Ensure comment belongs to post
        if ($comment->post_id !== $post->id) {
            return response()->json(['message' => 'Comment does not belong to this post'], 400);
        }

        // Ensure owner
        if ($request->user()->id !== $comment->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $fields = $request->validate(['content' => 'required|string']);
        $comment->update($fields);

        return $comment;
    }

    public function destroy(Request $request, Post $post, Comment $comment)
    {
        if ($comment->post_id !== $post->id) {
            return response()->json(['message' => 'Comment does not belong to this post'], 400);
        }

        if ($request->user()->id !== $comment->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }

    public function flag(Comment $comment)
{
    $post = $comment->post;

    if ($post->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $comment->update([
        'flagged_at' => now(),
    ]);

    return $comment;
}

}