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
                'name' => 'Monthly',
                'duration' => 30,
                'price' => 1000
            ],
            [
                'name' => 'Quarterly',
                'duration' => 90,
                'price' => 3000
            ],
            [
                'name' => 'Half Yearly',
                'duration' => 180,
                'price' => 4000
            ],
            [
                'name' => 'Yearly',
                'duration' => 365,
                'price' => 5000
            ]
        ]);
    }
}
