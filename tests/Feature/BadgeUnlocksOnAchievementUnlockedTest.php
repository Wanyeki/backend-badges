<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Listeners\CheckAndUnlockBadge;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BadgeUnlocksOnAchievementUnlockedTest extends TestCase
{
    use RefreshDatabase;

    public function test_badge_unlocks_on_achievements(): void
    {
        $this->artisan('db:seed');
        $badges = Achievement::where("type", "lesson")->get();

        foreach ($badges as $badge) {
            if ($badge->threshold > 0) {
                $user = $this->handleAchievementUnlockedEvent($badge->threshold);
                Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($badge, $user) {
                    $event->payload['badge_name'] == $badge->name && $event->payload['user']->id == $user->id;
                    return true;
                }, );
            }
        }
    }

    public function handleAchievementUnlockedEvent(int $numberOfAchievements): User
    {
        Event::fake();
        $achievements = Achievement::limit($numberOfAchievements)->get();
        $user = User::factory()->create();
        $user->achievements()->attach($achievements);

        $lastAchievement = $achievements->last();
        $event = new AchievementUnlocked($lastAchievement, $user);
        $listener = new CheckAndUnlockBadge();
        $listener->handle($event);
        return $user;
    }

    public function test_badge_for_new_user(): void
    {
    }
}
