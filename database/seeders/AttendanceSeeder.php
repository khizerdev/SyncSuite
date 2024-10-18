<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $startDate = '2024-10-01';

       $endDate = '2024-10-17';

       for ($date = $startDate; $date <= $endDate; $date++) {
            DB::table('attendances')->insert([
                ['code' => 1002, 'datetime' => $date . ' 08:00:00'],
                ['code' => 1002, 'datetime' => $date . ' 20:00:00'],
            ]);
       }
    }
}
