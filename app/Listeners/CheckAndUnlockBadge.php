<?php

namespace App\Listeners;

use App\Services\BadgeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckAndUnlockBadge
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $badgeService = new BadgeService;
        $user = $event->user;

        $badgeToUnlock = $badgeService->checkForBadgesToUnlock($user);
        if ($badgeToUnlock != null) {
            $badgeService->unlockBadge($badgeToUnlock, $user);
        }
    }
}
