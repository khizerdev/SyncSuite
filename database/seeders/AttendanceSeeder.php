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
       // Start date for August 2024
       $startDate = '2024-08-01';

       // End date for August 2024
       $endDate = '2024-08-31';

       // Iterate through each day of August
       for ($date = $startDate; $date <= $endDate; $date++) {
           // Check if the day is not a Sunday
           if (date('N', strtotime($date)) !== 7) {
               // Create morning and evening attendance records
               DB::table('attendances')->insert([
                   ['code' => 1002, 'datetime' => $date . ' 08:00:00'],
                   ['code' => 1002, 'datetime' => $date . ' 20:00:00'],
               ]);
           }
       }
    }
}
