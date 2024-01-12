<?php

namespace Tests\Feature;

use App\Events\LessonWatched;
use App\Listeners\CheckAndUnlockAchievement;
use App\Listeners\CheckAndUnlockBadge;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LessonWatchedEventListenerTest extends TestCase
{
    use RefreshDatabase;
    public function test_unlock_achievement_listener_handles_watch_event(): void
    {
        $this->fireEvent();
        Event::assertListening(LessonWatched::class, CheckAndUnlockAchievement::class);
    }
    public function test_unlock_badges_listener_handles_watch_event(): void
    {
        $this->fireEvent();
        Event::assertListening(LessonWatched::class, CheckAndUnlockBadge::class);
    }
    private function fireEvent(): void
    {
        Event::fake();
        $lesson = Lesson::factory()->create();
        $user = User::factory()->hasAttached($lesson)->create();

        event(new LessonWatched($lesson, $user));
    }

}
