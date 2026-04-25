<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_message(): void
    {
        Event::fake([MessageSent::class]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::create();
        $conversation->users()->attach([$user1->id, $user2->id]);

        $response = $this->actingAs($user1)
            ->postJson('/api/messages', [
                'conversation_id' => $conversation->id,
                'content' => 'Hello athlete!',
            ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('messages', [
            'content' => 'Hello athlete!',
            'user_id' => $user1->id,
            'conversation_id' => $conversation->id,
        ]);

        Event::assertDispatched(MessageSent::class);
    }

    public function test_user_can_list_conversations(): void
    {
        $user = User::factory()->create();
        $conversation = Conversation::create();
        $conversation->users()->attach([$user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/conversations');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_user_cannot_view_others_conversation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::create();
        $conversation->users()->attach([$user2->id]);

        $response = $this->actingAs($user1)
            ->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(403);
    }
}
