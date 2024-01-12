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
            ["name" => "Beginner", "threshold" => 0],
            ["name" => "Intermediate", "threshold" => 4],
            ["name" => "Advanced", "threshold" => 8],
            ["name" => "Master", "threshold" => 10],
        ];
        DB::table('badges')->insert($badges);
    }
}
