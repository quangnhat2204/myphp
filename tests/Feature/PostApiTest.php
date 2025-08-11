<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user to be used as an author
        User::factory()->create();
    }

    #[Test]
    public function it_can_list_all_posts()
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function it_can_create_a_post()
    {
        $postData = [
            'title' => 'New Post',
            'content' => 'This is the content of the new post.',
            'author_id' => User::first()->id,
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'New Post']);
        
        $this->assertDatabaseHas('posts', ['title' => 'New Post']);
    }

    #[Test]
    public function it_can_show_a_post()
    {
        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $post->title]);
    }

    #[Test]
    public function it_can_update_a_post()
    {
        $post = Post::factory()->create();

        $updateData = ['title' => 'Updated Post Title'];

        $response = $this->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Post Title']);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Post Title']);
    }

    #[Test]
    public function it_can_delete_a_post()
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
