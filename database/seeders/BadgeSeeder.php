<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            ["name" => "Beginner: 0 Achievements", "threshold" => 0],
            ["name" => "Intermediate: 4 Achievements", "threshold" => 4],
            ["name" => "Advanced: 8 Achievements", "threshold" => 8],
            ["name" => "Master: 10 Achievements", "threshold" => 10],
        ];
        DB::table('badges')->insert($badges);
    }
}
