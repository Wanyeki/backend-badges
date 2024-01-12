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

    public function getCurrentBadge(User $user)
    {
        return $user->badges()->orderBy('threshold', 'desc')->get()->first();
    }
    public function getNextBadge(User $user)
    {
        $currentBadge = $this->getCurrentBadge($user);
        return Badge::where('threshold', '>', $currentBadge->threshold ?? -1)->orderBy('threshold', 'asc')->get()->first();

    }

    public function getRemainingToUnlockBadge(User $user): int
    {
        $currentBadge = $this->getCurrentBadge($user);
        $nextBadge = $this->getNextBadge($user);

        $remaining = $currentBadge->threshold ?? 0 - $nextBadge->threshold ?? 0;
        return $remaining > 0 ? $remaining : 0;
    }

}