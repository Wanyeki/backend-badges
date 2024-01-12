<?php

namespace Tests\Feature;

use App\Events\CommentWritten;
use App\Listeners\CheckAndUnlockAchievement;
use App\Listeners\CheckAndUnlockBadge;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CommentWrittenEventTest extends TestCase
{

    use RefreshDatabase;
    public function test_unlock_achievement_listener_handles_comment_event(): void
    {
        $this->initAndFireEvent();
        Event::assertListening(CommentWritten::class, CheckAndUnlockAchievement::class);
    }
    private function initAndFireEvent(): void
    {
        Event::fake();
        $user = User::factory()->create();
        $comment = Comment::factory()->create(["user_id" => $user->id]);
        event(new CommentWritten($comment));
    }
}
