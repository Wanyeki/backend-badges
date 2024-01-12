<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Listeners\CheckAndUnlockBadge;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementUnlockedEventTest extends TestCase
{
    use RefreshDatabase;
    public function test_unlock_achievement_listener_handles_comment_event(): void
    {
        $this->artisan('db:seed');

        $this->initAndFireEvent();
        Event::assertListening(AchievementUnlocked::class, CheckAndUnlockBadge::class);
    }
    private function initAndFireEvent(): void
    {
        Event::fake();
        $user = User::factory()->create();
        $achievement = Achievement::limit(1)->get()->first();

        $user->achievements()->attach($achievement);
        event(new AchievementUnlocked($achievement, $user));
    }
}
