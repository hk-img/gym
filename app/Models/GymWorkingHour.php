<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GymWorkingHour extends Model
{
    protected $fillable = ['gym_id', 'day', 'open_time', 'close_time', 'is_closed'];

    /**
     * Check if the gym is open based on current time.
     */
    public static function isGymOpen()
    {
        $today = Carbon::now()->format('l'); // Get current day name
        $currentTime = Carbon::now()->format('H:i:s');

        $workingHour = self::where('day', $today)->first();

        if (!$workingHour || $workingHour->is_closed) {
            return false; // Gym is closed
        }

        return ($currentTime >= $workingHour->open_time && $currentTime <= $workingHour->close_time);
    }
}
