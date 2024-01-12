<?php
namespace App\Services;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;


class BadgeService
{
    public function checkForBadgesToUnlock(User $user): ?Badge
    {
        $achievementsCount = $this->getAchievementsCount($user);
        $badgeToUnlock = $this->getBadgeToUnlock($achievementsCount);
        if ($badgeToUnlock != null && !$this->badgeIsUnlocked($user, $badgeToUnlock)) {
            return $badgeToUnlock;
        }
        return null;
    }
    public function unlockBadge(Badge $badge, User $user): void
    {
        $user->achievements()->attach($badge);
        BadgeUnlocked::dispatch($badge, $user);
    }
    public function badgeIsUnlocked(User $user, Badge $badge): bool
    {
        return $user->badges()->where('badge_id', $badge->id)->exists();
    }
    public function getBadgeToUnlock($number): ?Badge
    {
        return Badge::where('threshold', '<=', $number)
            ->orderBy('threshold', 'desc')
            ->get()
            ->first();
    }
    public function getAchievementsCount(User $user): int
    {
        return $user->achievements()->count();
    }

}