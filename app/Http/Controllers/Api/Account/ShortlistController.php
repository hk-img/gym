<?php

namespace App\Http\Controllers\Api\Account;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Shortlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\Traits;

class ShortlistController extends Controller
{
    use Traits;

    /**
     * Fetch ShortListed Vehicles
     */
    public function fetchShortlist(Request $request)
    {
        try {
            $shortlists = Shortlist::with([
                'vehicle.brand', 
                'vehicle.fuelType', 
                'vehicle.transmissionType', 
                'vehicleVariant', 
                'usedVehicle'
            ])->where('user_id', auth()->id())->get();

            foreach ($shortlists as $shortlist) {
                $shortlist->vehicle->image_url = $shortlist->vehicle->getFirstMediaUrl('images', 'webp') 
                    ?: asset('front_assets/images/resource/no-image.webp');
                $shortlist->vehicle->price_range = callPriceRange($shortlist->vehicle);
            }

            // Return API response with fetched data
            return ApiResponse::success("Shortlisted vehicles fetched successfully.", $shortlists);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Toggle ShortListed Vehicles
     */
    public function toggleShortlist(Request $request)
    {
        try {
            $userId = Auth::id();
            $vehicleId = $request->id;

            $shortlist = Shortlist::where('user_id', $userId)->where('vehicle_id', $vehicleId)->first();

            if ($shortlist) {
                // Remove from shortlist
                $shortlist->delete();
                return ApiResponse::success("Vehicle removed from shortlist.", ['shortlisted' => false]);
            } else {
                // Add to shortlist
                Shortlist::create([
                    'user_id' => $userId,
                    'vehicle_id' => $vehicleId
                ]);
                return ApiResponse::success("Vehicle added to shortlist.", ['shortlisted' => true]);
            }
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Delete ShortListed Vehicles
     */
    public function deleteShortlist($id)
    {
        try {
            $shortlist = Shortlist::findOrFail($id); // Find the shortlist entry by ID

            // Ensure the shortlist entry belongs to the authenticated user
            if ($shortlist->user_id !== auth()->id()) {
                return ApiResponse::response(ApiResponse::HTTP_FORBIDDEN, "Unauthorized");
            }

            $shortlist->delete(); // Delete the shortlist entry

            return ApiResponse::success("Vehicle removed from shortlist.");
        } catch (\Throwable $e) {
            Log::error('Error deleting shortlist: ' . $e->getMessage());
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Failed to delete shortlist.");
        }
    }
}
