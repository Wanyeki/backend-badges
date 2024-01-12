<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AchievementService;
use App\Services\BadgeService;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    protected $achievementService;
    protected $badgeService;

    public function __construct(AchievementService $achievementService, BadgeService $badgeService)
    {
        $this->achievementService = $achievementService;
        $this->badgeService = $badgeService;
    }

    public function index(User $user)
    {

        return response()->json([
            'unlocked_achievements' => $this->achievementService->getUnlockedAchievements($user),
            'next_available_achievements' => $this->achievementService->getNextAvailableAchievements($user),
            'current_badge' => $this->badgeService->getCurrentBadge($user)->name ?? '',
            'next_badge' => $this->badgeService->getNextBadge($user)->name ?? '',
            'remaining_to_unlock_next_badge' => $this->badgeService->getRemainingToUnlockBadge($user)
        ]);
    }

}
