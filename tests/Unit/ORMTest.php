<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ORMTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_insert_a_user_record()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $this->assertDatabaseCount('users', 1);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
    }

    #[Test]
    public function a_user_can_have_many_posts()
    {
        $user = User::factory()->create();

        Post::factory()->count(3)->create(['author_id' => $user->id]);

        $this->assertCount(3, $user->posts);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->posts);
        $this->assertInstanceOf(Post::class, $user->posts->first());
    }

    #[Test]
    public function a_post_belongs_to_an_author()
    {
        $author = User::factory()->create();

        $post = Post::factory()->create(['author_id' => $author->id]);

        $this->assertInstanceOf(User::class, $post->author);

        $this->assertEquals($author->id, $post->author->id);
    }
}
