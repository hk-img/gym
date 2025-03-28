<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AssignPlan;
use App\Models\DietPlan;
use App\Models\GymSocialLinks;
use App\Models\GymWorkingHour;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Get Gym Working Hours
     */
    public function getWorkingHour(Request $request)
    {
        try {
            $gymId = User::where('id', auth()->user()->id)->first()->added_by ?? '';

            $hours  = GymWorkingHour::where('gym_id', $gymId)->get();

            // Return API response with fetched data
            return ApiResponse::success("Working Hour fetched successfully.", $hours);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Get Social Links
     */
    public function socialLinks(Request $request)
    {
        try {
            $gymId = User::where('id', auth()->user()->id)->first()->added_by ?? '';

            $socialLinks  = GymSocialLinks::where('gym_id', $gymId)->get();

            // Return API response with fetched data
            return ApiResponse::success("Social link fetched successfully.", $socialLinks);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Get Workout
     */
    public function getWorkout(Request $request)
    {
        try {

            $workouts = Workout::with(['exercises'])->get();

            // Return API response with fetched data
            return ApiResponse::success("Workout details fetched successfully.", $workouts);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Get Diet Plan
     */
    public function getDietPlan(Request $request)
    {
        try {

            $dietPlan = DietPlan::with(['meals'])->get();

            // Return API response with fetched data
            return ApiResponse::success("Diet Plan details fetched successfully.", $dietPlan);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
