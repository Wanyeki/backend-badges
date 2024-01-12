<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Listeners\CheckAndUnlockAchievement;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementUnlockOnCommentTest extends TestCase
{
    use RefreshDatabase;
    public function test_correct_achievement_is_unlocked_after_writing_a_comment(): void
    {
        $this->artisan('db:seed');
        $achievements = Achievement::where("type", "comment")->get();

        foreach ($achievements as $achievement) {
            $user = $this->handleCommentWrittenEvent($achievement->threshold);
            Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($achievement, $user) {
                return $event->payload['achievement_name'] == $achievement->name && $event->payload['user']->id == $user->id;
            }, );
        }
    }

    private function handleCommentWrittenEvent(int $numberOfComments): User
    {
        Event::fake();
        $user = User::factory()->create();
        $comments = Comment::factory()->count($numberOfComments)->create(["user_id" => $user->id]);
        $lastComment = $comments->last();
        $event = new CommentWritten($lastComment);
        $listener = new CheckAndUnlockAchievement();
        $listener->handle($event);

        return $user;
    }
}
