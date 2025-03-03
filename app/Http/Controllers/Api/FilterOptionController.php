<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\VehicleType;
use Illuminate\Http\Request;


class FilterOptionController extends Controller
{
    /**
     * Fetch Budget List
     */
    // public function byBudegt(Request $request)
    // {
    //     try {
    //         $budgetRanges = [
    //             '1-5 Lakh' => [100000, 500000],
    //             '5-10 Lakh' => [500001, 1000000],
    //             '10-20 Lakh' => [1000001, 2000000],
    //             '20-50 Lakh' => [2000001, 5000000],
    //             '50 Lakh - 1 Cr' => [5000001, 10000000],
    //             'Above 1 Cr' => [10000001, PHP_INT_MAX]
    //         ];

    //         foreach($budgetRanges as $range){
    //             $brand->image = $brand->getFirstMediaUrl('images');
    //         }

    //         // Return API response with fetched data
    //         return ApiResponse::success("Brand list fetched successfully.", $brands);
    //     } catch (\Throwable $e) {
    //         return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
    //     }
    // }

    /**
     * Fetch Vehicle Type List
     */
    public function byVehicleType(Request $request)
    {
        try {
            $vehicleTypes  = VehicleType::where('status', 1)->get();

            foreach($vehicleTypes as $vehicleType){
                $vehicleType->image = $vehicleType->getFirstMediaUrl('images');
            }

            // Return API response with fetched data
            return ApiResponse::success("Vehicle type list", $vehicleTypes);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    
    /**
     * Fetch Fuel Type List
     */
    public function byFuel(Request $request)
    {
        try {
            $fuelTypes  = FuelType::where('status', 1)->get();

            // Return API response with fetched data
            return ApiResponse::success("Fuel type list", $fuelTypes);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Fetch Transmission Type List
     */
    public function byTransmission(Request $request)
    {
        try {
            $tranmissions  = TransmissionType::where('status', 1)->get();

            // Return API response with fetched data
            return ApiResponse::success("Transmission type list", $tranmissions);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
