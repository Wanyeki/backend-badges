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
    public function test_endpoint_returns_expected_achievement_data(): void
    {
        $this->artisan('db:seed');

        $user = User::factory()->create();

        [$lessonAchievementsGiven, $lessonNextAchievement] = $this->giveUserAchievements('lesson', $user);
        [$commentAchievementsGiven, $commentNextAchievement] = $this->giveUserAchievements('comment', $user);
        $this->giveUserBadges($user);

        $achievementsUnlocked = [...$lessonAchievementsGiven, ...$commentAchievementsGiven];
        $nextAchievements = [...$lessonNextAchievement, ...$commentNextAchievement];

        $currentBadge = $user->badges()->orderBy('threshold', 'desc')->get()->first();
        $nextBadge = Badge::where('threshold', '>', $currentBadge->threshold ?? -1)->orderBy('threshold', 'asc')->get()->first();

        $expectedData = [
            'unlocked_achievements' => $this->getNamesList($achievementsUnlocked),
            'next_available_achievements' => $this->getNamesList($nextAchievements),
            'current_badge' => $currentBadge->name ?? null,
            'next_badge' => $nextBadge->name ?? null,
            'remaining_to_unlock_next_badge' => $this->getRemainingToUnlockBadge($currentBadge, $nextBadge)
        ];

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJsonFragment($expectedData);
    }
    public function getNamesList($items)
    {
        return array_map(function ($item) {
            return $item['name'] ?? '';
        }, $items);
    }
    public function getRemainingToUnlockBadge(?Badge $currentBadge, ?Badge $nextBadge)
    {
        $remaining = $currentBadge->threshold ?? 0 - $nextBadge->threshold ?? 0;
        return $remaining > 0 ? $remaining : 0;
    }
    public function giveUserAchievements(string $type, User $user)
    {
        $typeAchievements = Achievement::where('type', $type)->orderBy('threshold', 'asc')->get();
        $achievementsUnlocked = $typeAchievements->take(3)->toArray();
        $user->achievements()->attach($typeAchievements->take(3));
        $nextAchievements = $typeAchievements->slice(3, 1)->toArray();
        return [$achievementsUnlocked, $nextAchievements];
    }
    public function giveUserBadges(User $user)
    {
        $badges = Badge::orderBy('threshold', 'asc')->get();
        $user->badges()->attach($badges->take(2));
    }
}
