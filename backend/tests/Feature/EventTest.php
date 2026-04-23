<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Spot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_events(): void
    {
        $user = User::factory()->create();
        Event::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_user_can_create_event(): void
    {
        $user = User::factory()->create();
        $spot = Spot::factory()->create();
        
        $eventData = [
            'title' => 'Sunday Match',
            'sport' => 'Football',
            'date' => now()->addDays(2)->format('Y-m-d'),
            'time' => '10:00',
            'location' => 'Central Park',
            'max_participants' => 10,
            'spot_id' => $spot->id,
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/events', $eventData);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Sunday Match');

        $this->assertDatabaseHas('events', [
            'title' => 'Sunday Match',
            'organizer_id' => $user->id,
        ]);
    }

    public function test_user_can_filter_events_by_sport(): void
    {
        $user = User::factory()->create();
        Event::factory()->create(['sport' => 'Football']);
        Event::factory()->create(['sport' => 'Basketball']);

        $response = $this->actingAs($user)
            ->getJson('/api/events?sport=Football');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.sport', 'Football');
    }
}
