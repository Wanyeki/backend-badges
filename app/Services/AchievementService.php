<?php
namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;


class AchievementService
{
    public function checkForAchievementToUnlock(string $type, User $user): ?Achievement
    {
        $entityCount = $this->getLessonsWatchedOrCommentsCount($type, $user);
        $achievementToUnlock = $this->getAchievementToUnlock($type, $entityCount);
        if ($achievementToUnlock != null && !$this->achievementIsUnlocked($user, $achievementToUnlock)) {
            return $achievementToUnlock;
        }
        return null;

    }
    public function unlockAchievement(Achievement $achievement, User $user): void
    {
        $user->achievements()->attach($achievement);
        AchievementUnlocked::dispatch($achievement, $user);
    }
    public function achievementIsUnlocked(User $user, Achievement $achievement): bool
    {
        return $user->achievements()->where("achievement_id", $achievement->id)->exists();
    }
    public function getAchievementToUnlock($type, $number): ?Achievement
    {
        return Achievement::where("type", $type)
            ->where('threshold', '<=', $number)
            ->orderBy('threshold', 'desc')
            ->get()
            ->first();
    }
    public function getLessonsWatchedOrCommentsCount(string $type, User $user): int
    {
        return $type == 'comment' ?
            $user->comments->count() :
            $user->lessons->count();
    }

}
