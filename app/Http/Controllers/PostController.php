<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware() {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        return Post::all();
    }

public function store(Request $request)
{
    $fields = $request->validate([
        'title' => 'required|max:255',
        'body' => 'required',
        'post_status_id' => 'exists:post_statuses,id'
    ]);

    $post = $request->user()->posts()->create($fields);

    return $post;
}

    public function show(Post $post)
    {
        return $post;
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        $post->update($fields);

        return $post;
    }

    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);
        $post->delete();
        return ['message' => "The post ($post->id) has been deleted"];
    }
    
public function changeStatus(Request $request, Post $post)
{
    if ($post->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $fields = $request->validate([
        'post_status_id' => 'required|exists:post_statuses,id'
    ]);

    $post->update($fields);

    return $post;
}

}
