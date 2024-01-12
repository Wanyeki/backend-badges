<?php

namespace App\Listeners;

use App\Models\Comment;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckAndUnlockAchievement
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }


    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $achievementService = new AchievementService;
        $type = $this->getEntityType($event);
        $user = $this->getUserFromEvent($event, $type);

        $achievementToUnlock = $achievementService->checkForAchievementToUnlock($type, $user);
        if ($achievementToUnlock != null) {
            $achievementService->unlockAchievement($achievementToUnlock, $user);
        }
    }

    /**
     * check if its a comment or a lesson watch that fired the event.
     */
    protected function getEntityType(object $event): string
    {
        if (isset($event->comment)) {
            return 'comment';
        } else if (isset($event->lesson)) {
            return 'lesson';
        }
        return '';
    }
    protected function getUserFromEvent(object $event, string $type): User
    {
        return $type == 'lesson' ? $event->user : $event->comment->user;
    }

}
