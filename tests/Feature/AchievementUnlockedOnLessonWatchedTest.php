<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Listeners\CheckAndUnlockAchievement;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementUnlockedOnLessonWatchedTest extends TestCase
{
    use RefreshDatabase;
    public function test_correct_achievement_is_unlocked_after_lesson_is_watched(): void
    {
        $this->artisan('db:seed');
        $achievements = Achievement::where("type", "lesson")->get();

        foreach ($achievements as $achievement) {
            $user = $this->handleLessonWatchedEvent($achievement->threshold);
            Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($achievement, $user) {
                return $event->payload['achievement_name'] == $achievement->name && $event->payload['user']->id == $user->id;
            }, );
        }
    }

    private function handleLessonWatchedEvent(int $numberOfLessonsWatched): User
    {
        Event::fake();
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count($numberOfLessonsWatched)->create();
        $user->lessons()->attach($lessons);
        $lastLesson = $lessons->last();
        $event = new LessonWatched($lastLesson, $user);
        $listener = new CheckAndUnlockAchievement();
        $listener->handle($event);

        return $user;
    }
}
