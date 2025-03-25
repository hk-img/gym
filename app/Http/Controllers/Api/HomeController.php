<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\GymSocialLinks;
use App\Models\GymWorkingHour;
use App\Models\User;
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
}
