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

    public function getUnlockedAchievements(User $user)
    {
        return $this->getNamesList($user->achievements()->orderBy('threshold', 'asc')->get()->toArray());
    }

    public function getNextAvailableAchievements(User $user)
    {
        $nextAvailableAchievement = [];

        $currentLessonAchievement = $this->getCurrentUserAchievement($user, 'lesson');
        $currentCommentAchievements = $this->getCurrentUserAchievement($user, 'comment');

        $nextLessonAchievement = $this->getNextAchievement($currentLessonAchievement->threshold ?? -1, 'lesson');
        $nextCommentAchievement = $this->getNextAchievement($currentCommentAchievements->threshold ?? -1, 'comment');

        $nextLessonAchievement != null ? array_push($nextAvailableAchievement, $nextLessonAchievement->name) : null;
        $nextCommentAchievement != null ? array_push($nextAvailableAchievement, $nextCommentAchievement->name) : null;

        return $nextAvailableAchievement;
        ;
    }
    public function getCurrentUserAchievement(User $user, string $type)
    {
        return $user->achievements()->where('type', $type)->orderBy('threshold', 'desc')->get()->first();
    }

    public function getNextAchievement(int $currentThreshold, string $type)
    {
        return Achievement::where('type', $type)
            ->where('threshold', '>', $currentThreshold)
            ->orderBy('threshold', 'asc')
            ->get()
            ->first();
    }
    public function getNamesList($items)
    {
        return array_map(function ($item) {
            return $item['name'] ?? '';
        }, $items);
    }
}
