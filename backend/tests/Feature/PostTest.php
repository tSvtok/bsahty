<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_feed(): void
    {
        $user = User::factory()->create();
        Question::factory()->count(5)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/questions');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data.data');
    }

    public function test_user_can_create_post(): void
    {
        $user = User::factory()->create();
        
        $postData = [
            'title' => 'New Sports Post',
            'content' => 'This is a test post about football.',
            'sport_category' => 'FOOTBALL',
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/questions', $postData);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Sports Post');

        $this->assertDatabaseHas('questions', [
            'title' => 'New Sports Post',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_delete_their_own_post(): void
    {
        $user = User::factory()->create();
        $post = Question::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/questions/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('questions', ['id' => $post->id]);
    }

    public function test_user_cannot_delete_others_post(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Question::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/questions/{$post->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('questions', ['id' => $post->id]);
    }
}
