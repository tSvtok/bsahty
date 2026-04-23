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
            ]
        );

        // 3. Create 30 more users
        $users = User::factory(30)->create();
        $allUsers = $users->concat([$testUser]);

        // 4. Create 20 spots
        $spots = Spot::factory(20)->create();

        // 5. Create 15 events
        $events = Event::factory(15)->recycle($allUsers)->recycle($spots)->create();

        // 6. Create 50 questions (posts)
        $questions = Question::factory(50)->recycle($allUsers)->create();

        // 7. Create 100 comments
        \App\Models\Comment::factory(100)->recycle($allUsers)->recycle($questions)->create();

        // 8. Create Friendships
        foreach ($users as $user) {
            \App\Models\Friendship::create([
                'user_id' => $testUser->id,
                'friend_id' => $user->id,
                'status' => \App\Enums\FriendshipStatus::ACCEPTED,
            ]);
        }

        // 9. Create Conversations & Messages
        foreach ($users->take(5) as $user) {
            $conv = \App\Models\Conversation::create();
            $conv->users()->attach([$testUser->id, $user->id]);

            \App\Models\Message::factory(10)->create([
                'conversation_id' => $conv->id,
                'user_id' => $testUser->id,
            ]);

            \App\Models\Message::factory(10)->create([
                'conversation_id' => $conv->id,
                'user_id' => $user->id,
            ]);
        }

        // 10. Create Reactions
        foreach ($questions as $q) {
            \App\Models\Reaction::create([
                'user_id' => $allUsers->random()->id,
                'reactable_id' => $q->id,
                'reactable_type' => \App\Models\Question::class,
                'type' => 'LIKE',
            ]);
        }
    }
}
