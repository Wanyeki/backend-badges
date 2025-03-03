<?php

namespace App\Providers;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Listeners\CheckAndUnlockAchievement;
use App\Listeners\CheckAndUnlockBadge;
use App\Listeners\saveComment;
use App\Listeners\saveLessonWatched;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */

    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CommentWritten::class => [
            CheckAndUnlockAchievement::class,

        ],
        LessonWatched::class => [
            CheckAndUnlockAchievement::class,

        ],
        AchievementUnlocked::class => [
            CheckAndUnlockBadge::class,
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
