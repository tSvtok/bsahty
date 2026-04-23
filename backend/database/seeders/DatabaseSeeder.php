<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Spot;
use App\Models\Event;
use App\Models\Question;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => \App\Enums\Role::ADMIN,
            ]
        );

        // 2. Create Test User
        $testUser = User::firstOrCreate(
            ['email' => 'athlete@example.com'],
            [
                'name' => 'Athlete User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'bio' => 'Passionate about football and tennis. Looking for partners in Algiers!',
                'city' => 'Algiers',
                'sports' => ['Football', 'Tennis'],
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop',
            ]
        );

        // 3. Create 30 more users with avatars
        $users = User::factory(30)->create();
        foreach ($users as $user) {
            $user->update([
                'avatar' => 'https://i.pravatar.cc/150?u=' . $user->id,
            ]);
        }
        $allUsers = $users->concat([$testUser]);

        // 4. Create 25 spots with specific Algiers coordinates
        $algiersSpots = [
            ['name' => 'Stade du 5 Juillet', 'lat' => 36.7592, 'lng' => 2.9961, 'type' => 'stadium'],
            ['name' => 'Jardin d\'Essai du Hamma', 'lat' => 36.7483, 'lng' => 3.0647, 'type' => 'park'],
            ['name' => 'Promenade des Sablettes', 'lat' => 36.7424, 'lng' => 3.0858, 'type' => 'track'],
            ['name' => 'Club de Tennis Ben Aknoun', 'lat' => 36.7589, 'lng' => 3.0245, 'type' => 'court'],
            ['name' => 'Gym Hydra Center', 'lat' => 36.7431, 'lng' => 3.0345, 'type' => 'gym'],
            ['name' => 'Piscine Olympique El Biar', 'lat' => 36.7725, 'lng' => 3.0312, 'type' => 'pool'],
        ];

        foreach ($algiersSpots as $s) {
            Spot::create([
                'name' => $s['name'],
                'coordinates' => ['lat' => $s['lat'], 'lng' => $s['lng']],
                'type' => $s['type'],
                'status' => 'APPROVED',
            ]);
        }

        // Add 20 more random spots in Algiers
        Spot::factory(20)->create();

        $allSpots = Spot::all();

        // 5. Create 30 events
        $events = Event::factory(30)->recycle($allUsers)->recycle($allSpots)->create();

        // 6. Create 60 questions (posts)
        $questions = Question::factory(60)->recycle($allUsers)->create();

        // 7. Create 150 comments
        \App\Models\Comment::factory(150)->recycle($allUsers)->recycle($questions)->create();

        // 8. Create Friendships
        foreach ($users->take(15) as $user) {
            if ($user->id === $testUser->id) continue;
            \App\Models\Friendship::updateOrCreate(
                ['user_id' => $testUser->id, 'friend_id' => $user->id],
                ['status' => \App\Enums\FriendshipStatus::ACCEPTED]
            );
        }

        // 9. Create Conversations & Messages
        foreach ($users->take(8) as $user) {
            $conv = \App\Models\Conversation::create();
            $conv->users()->attach([$testUser->id, $user->id]);

            \App\Models\Message::factory(5)->create([
                'conversation_id' => $conv->id,
                'user_id' => $testUser->id,
            ]);

            \App\Models\Message::factory(5)->create([
                'conversation_id' => $conv->id,
                'user_id' => $user->id,
            ]);
        }

        // 10. Create Reactions
        foreach ($questions as $q) {
            $reactingUsers = $allUsers->random(rand(1, min(10, $allUsers->count())));
            foreach ($reactingUsers as $user) {
                \App\Models\Reaction::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'reactable_id' => $q->id,
                        'reactable_type' => \App\Models\Question::class,
                    ],
                    ['type' => 'LIKE']
                );
            }
        }
    }
}
