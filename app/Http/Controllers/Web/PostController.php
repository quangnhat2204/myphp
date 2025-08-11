<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return view('posts.index');
    }

    public function create(Request $request)
    {
        $user_id = $request->query('user_id');
        return view('posts.create', compact('user_id'));
    } 

    public function store(Request $request)
    {
        // Remove for demo BE validation
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'content' => 'required|string',
        // ]);

        $author = \App\Models\User::find($request->author_id);

        if (!$author) {
            // For demo, we'll use the first user as author
            // In real app, you'd get the authenticated user
            $author = \App\Models\User::first();
        }
        
        if (!$author) {
            return redirect()->back()->with('error', 'No users found. Please create a user first.');
        }

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'author_id' => $author->id,
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $post->load('author');
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        // Remove for demo BE validation
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'content' => 'required|string',
        // ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}