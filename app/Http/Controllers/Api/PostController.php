<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Http\Response;

class PostController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            $posts = Post::with('author')->paginate(10);
            return PostResource::collection($posts);
        } catch (Exception $e) {
            Log::error('Failed to retrieve posts: ' . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not retrieve posts.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'author_id' => 'required|exists:users,id',
            ]);

            $post = Post::create($validated);

            return new PostResource($post);
        } catch (ValidationException $e) {
            return $this->error(
                'The given data was invalid.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->errors()
            );
        } catch (Exception $e) {
            Log::error('Failed to create post: ' . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not create post.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show(Post $post)
    {
        try {
            return new PostResource($post->load('author'));
        } catch (Exception $e) {
            Log::error("Failed to retrieve post {$post->id}: " . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not retrieve post.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(Request $request, Post $post)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
            ]);

            $post->update($validated);

            return new PostResource($post);
        } catch (ValidationException $e) {
            return $this->error(
                'The given data was invalid.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->errors()
            );
        } catch (Exception $e) {
            Log::error("Failed to update post {$post->id}: " . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not update post.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy(Post $post)
    {
        try {
            $post->delete();
            return response()->noContent();
        } catch (Exception $e) {
            Log::error("Failed to delete post {$post->id}: " . $e->getMessage());
            return $this->error(
                'An unexpected error occurred. Could not delete post.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
