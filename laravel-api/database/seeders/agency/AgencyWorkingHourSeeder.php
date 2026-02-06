<?php

namespace Database\Seeders\Agency;

use App\Models\Agency;
use App\Models\AgencyWorkingHour;
use Illuminate\Database\Seeder;

class AgencyWorkingHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            'Monday' => 'Thứ 2',
            'Tuesday' => 'Thứ 3',
            'Wednesday' => 'Thứ 4',
            'Thursday' => 'Thứ 5',
            'Friday' => 'Thứ 6',
        ];

        $workingHourIds = [];

        foreach ($days as $en => $vi) {
            $workingHour = AgencyWorkingHour::create([
                'day_of_week' => $vi,
                'open_time' => '08:00:00',
                'close_time' => '18:00:00',
                'publish' => 2,
            ]);
            $workingHourIds[] = $workingHour->id;
        }

        // Attach to all agencies
        $agencies = Agency::all();
        foreach ($agencies as $agency) {
            $agency->agency_working_hours()->sync($workingHourIds);
        }
    }
}
