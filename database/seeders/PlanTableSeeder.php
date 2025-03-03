<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'monthly',
                'duration' => 30,
            ],
            [
                'name' => 'quarterly',
                'duration' => 90
            ],
            [
                'name' => 'halfyearly',
                'duration' => 180
            ],
            [
                'name' => 'yearly',
                'duration' => 365
            ]
        ]);
    }
}
