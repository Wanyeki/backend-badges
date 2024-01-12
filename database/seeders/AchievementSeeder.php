<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            ["name" => "First Lesson Watched", "threshold" => 1, "type" => "lesson"],
            ["name" => "5 Lessons Watched", "threshold" => 5, "type" => "lesson"],
            ["name" => "10 Lessons Watched", "threshold" => 10, "type" => "lesson"],
            ["name" => "25 Lessons Watched", "threshold" => 25, "type" => "lesson"],
            ["name" => "50 Lessons Watched", "threshold" => 50, "type" => "lesson"],

            ["name" => "First Comment Written", "threshold" => 1, "type" => "comment"],
            ["name" => "3 Comments Written", "threshold" => 5, "type" => "comment"],
            ["name" => "5 Comments Written", "threshold" => 10, "type" => "comment"],
            ["name" => "10 Comments Written", "threshold" => 25, "type" => "comment"],
            ["name" => "20 Comments Written", "threshold" => 50, "type" => "comment"],

        ];
        DB::table('achievements')->insert($achievements);
    }
}
