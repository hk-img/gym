<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\City;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class VehicleFilterService
{
    public static function applyFilters(Builder $query, Request $request, $data)
    {
        // Filter by brand
        if (!empty($data['brand'])) {
            $brand = str_replace("+", " ", $data['brand']);
            $brandId = Brand::where('name', $brand)->value('id');
            if ($brandId) {
                $query->where('brand_id', $brandId);
            }
        }

        // Filter by fuel
        if (!empty($data['fuel'])) {
            $fuelId = FuelType::where('name', $data['fuel'])->value('id');
            if ($fuelId) {
                $query->whereHas('fuelType', fn($q) => $q->where('fuel_types.id', $fuelId));
            }
        }

        // Filter by transmission
        if (!empty($request->get('transmission'))) {
            $transmissionId = TransmissionType::where('name', $request->get('transmission'))->value('id');
            if ($transmissionId) {
                $query->whereHas('transmissionType', fn($q) => $q->where('transmission_types.id', $transmissionId));
            }
        }

        // Filter by vehicle type
        if (!empty($data['vehicle_type'])) {
            $bodyTypeId = VehicleType::where('name', ($data['vehicle_type']))->value('id');
            if ($bodyTypeId) {
                $query->where('vehicle_type_id', $bodyTypeId);
            }
        }

        // Filter by budget (price range)
        if (!empty($data['budget'])) {
            $maxPrice = intVal($data['budget']);
        
            // Ensure maxPrice is valid
            if ($maxPrice > 0) {
                $query->where(function ($q) use ($maxPrice) {
                    $q->whereHas('variants', function ($variantQuery) use ($maxPrice) {
                        $variantQuery->where('price', '<=', $maxPrice);
                    })
                    ->orWhere(function ($vehicleQuery) use ($maxPrice) {
                        $vehicleQuery->doesntHave('variants')->where('price', '<=', $maxPrice);
                    });
                });

            }
        }
        
        // Sorting logic
        if ($request->has('sort')) {
            if ($request->sort == 'price_asc') {
                $query->withMin('variants', 'price')->orderByRaw('COALESCE(variants_min_price, vehicles.price) ASC');
            }

            if ($request->sort == 'price_desc') {
                $query->withMax('variants', 'price')->orderByRaw('COALESCE(variants_max_price, vehicles.price) DESC');
            }
        }

        return $query;
    }

    public static function applyFiltersForUsed(Builder $query, Request $request, $data)
    {
        // Filter by brand
        if (!empty($data['brand'])) {
            $brandId = Brand::where('name', $data['brand'])->value('id');
            if ($brandId) {
                $query->where('brand_id', $brandId);
            }
        }

        // Filter by fuel
        if (!empty($data['fuel'])) {
            $fuelId = FuelType::where('name', $data['fuel'])->value('id');
            if ($fuelId) {
                $query->whereHas('variant.fuelType', fn($q) => $q->where('id', $fuelId));
            }
        }

        // Filter by transmission
        if (!empty($request->get('transmission'))) {
            $transmissionId = TransmissionType::where('name', $request->get('transmission'))->value('id');
            if ($transmissionId) {
                $query->whereHas('variant.transmissionType', fn($q) => $q->where('id', $transmissionId));
            }
        }

        // Filter by vehicle type
        if (!empty($data['vehicle_type'])) {
            $bodyTypeId = VehicleType::where('name', ($data['vehicle_type']))->value('id');
            if ($bodyTypeId) {
                $query->where('vehicle_type_id', $bodyTypeId);
            }
        }

        // Filter by budget (price range)
        if (!empty($data['budget'])) {
            $maxPrice = intVal($data['budget']);
        
            // Ensure maxPrice is valid
            if ($maxPrice > 0) {
                $query->where('price', '<=', $maxPrice);
            }
        }
        
        // Sorting logic
        if ($request->has('sort')) {
            // Sort by price (low - high)
            if ($request->sort == 'price_asc') {
                $query->orderBy('price', 'ASC');
            }

            // Sort by price (high - low)
            if ($request->sort == 'price_desc') {
                $query->orderBy('price', 'DESC');
            }

            // Sort by kms (low - high)
            if ($request->sort == 'kms_asc') {
                $query->orderBy('kms_driven', 'ASC');
            }
        }

        return $query;
    }
}
