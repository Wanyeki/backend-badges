<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProgressEndPointTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_it_returns_user_progress_data(): void
    {
        $this->artisan('db:seed');

        $user = User::factory()->create();


        $lessonAchievements = Achievement::where('type', 'lesson')->orderBy('threshold', 'asc')->get();
        $achievementsUnlocked = $lessonAchievements->take(3)->toArray();
        $user->achievements()->attach($lessonAchievements->take(3));
        $nextAchievements = $lessonAchievements->slice(2, 1)->toArray();

        $commentAchievements = Achievement::where('type', 'comment')->orderBy('threshold', 'asc')->get();
        $achievementsUnlocked = [...$achievementsUnlocked, ...$commentAchievements->take(3)->toArray()];
        $nextAchievements = [...$nextAchievements, ...$commentAchievements->slice(2, 1)->toArray()];
        $user->achievements()->attach($commentAchievements->take(3));


        $badges = Badge::orderBy('threshold', 'asc')->get();
        $user->badges()->attach($badges->take(2));
        $currentBadge = $user->badges()->orderBy('threshold', 'desc')->get()->first();

        $nextBadge = Badge::where('threshold', '>', $currentBadge->threshold ?? -1)->orderBy('threshold', 'asc')->get()->first();

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => $this->getNamesList($achievementsUnlocked),
                'next_available_achievements' => $this->getNamesList($nextAchievements),
                'current_badge' => $currentBadge->name ?? null,
                'next_badge' => $nextBadge->name ?? null,
                'remaining_to_unlock_next_badge' => $this->getRemainingToUnlockBadge($currentBadge, $nextBadge)
            ]);
    }
    public function getNamesList($items)
    {
        return array_map(function ($item) {
            return $item['name'] ?? '';
        }, $items);
    }
    public function getRemainingToUnlockBadge(Badge $currentBadge, Badge $nextBadge)
    {
        $remaining = $currentBadge->threshold ?? 0 - $nextBadge->threshold ?? 0;
        return $remaining > 0 ? $remaining : 0;
    }
}
