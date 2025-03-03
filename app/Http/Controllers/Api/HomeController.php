<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Shortlist;
use App\Models\UsedVehicle;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\VehicleVariant;
use App\Traits\Traits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Enums\Status;
use App\Jobs\SendMailJob;

class HomeController extends Controller
{
    use Traits;

    protected $typeId;


    public function __construct(Request $request)
    {
        $this->typeId = 1;
    }

    /**
     * Get City List with Popular and Search Functionality
     */
    public function getCities(Request $request)
    {
        try {
            // Fetch all cities
            $allCities = City::orderBy('name')->orderBy('name')->get();

            // Fetch popular cities
            // $popularCities = City::where('is_popular', 1)->orderBy('name')->get();

            // Search city if search parameter is provided
            if ($request->has('search')) {
                $search = $request->input('search');
                $allCities = City::where('name', 'LIKE', "%$search%")
                    ->orderBy('name')
                    ->get();
            }

            // Auto-detect user's city using Google API
            $detectedCity = null;
            if ($request->has(['latitude', 'longitude'])) {
                $latitude = $request->input('latitude');
                $longitude = $request->input('longitude');
                
                $googleApiKey = env('GOOGLE_MAPS_API');
                $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                    'latlng' => "$latitude,$longitude",
                    'key' => 'AIzaSyAnrLQq4LPedUb4uI8MQQjyRU_23UvTfmQ',
                ]);

                $data = $response->json();
                if (!empty($data['results'])) {
                    foreach ($data['results'] as $result) {
                        foreach ($result['address_components'] as $component) {
                            if (in_array("locality", $component['types'])) {
                                $detectedCity = City::where('name', $component['long_name'])->first();
                                break 2;
                            }
                        }
                    }
                }
            }

            return ApiResponse::success("City list fetched successfully.", [
                // 'popular_cities' => $popularCities,
                'all_cities' => $allCities,
                'detected_city' => $detectedCity,
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Show Banner Slider
     */
    public function bannerList(Request $request)
    {
        try {
            $banners  = Banner::where('status', 1)->get();

            foreach($banners as $banner){
                $banner->image = $banner->getFirstMediaUrl('images');
            }

            // Return API response with fetched data
            return ApiResponse::success("Banner list fetched successfully.", $banners);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * All Brand Listing
     */
    public function brandList(Request $request)
    {
        try {
            $brands  = Brand::where('status', 1)->get();

            foreach($brands as $brand){
                $brand->image = $brand->getFirstMediaUrl('images');
            }

            // Return API response with fetched data
            return ApiResponse::success("Brand list fetched successfully.", $brands);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Single Brand Page
     */
    public function singleBrand(Request $request, $slug)
    {
        try {                                                                                                      
            $brand  = Brand::with(['vehicle.vehicleType', 'vehicle.fuelType', 'vehicle.transmissionType'])->where('slug', $slug)->first();
            if(!$brand){
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, 'Brand not found.');
            }
            
            $vehicles = [];
            foreach($brand->vehicle as $item){
               $vehicles[] = [
                'name' => $item->brand->name.' '.$item->vehicle_model,
                'price' => callPriceRange($item),
                'image' => $item->getFirstMediaUrl('images')
               ];
            }

            $result = [
                'id' => 1,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'description' => $brand->description,
                'image' => $brand->getFirstMediaUrl('images'),
                'vehicle' => $vehicles
            ];
            
            // Return API response with fetched data
            return ApiResponse::success("Brand Detail", $result);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Vehicle List
     */
    public function vehicleList(Request $request)
    {
        try {

            // Filter options
            $brand = $request->get('brand_id') ?? null;
            $budget = $request->get('budget') ?? null;
            $fuelType = $request->get('fuel_type') ?? null;
            $vehicleType = $request->get('vehicle_type') ?? null;

            $location = $request->location ?? 0;
            $typeId = $this->typeId;

            $query =$this->getVehicleBaseQuery($typeId, $location);

            // Filter by brand
            $query->when($brand, function($q) use($brand){
                $q->where('brand_id', $brand);
            });

            // Filter by vehicle type
            $query->when($vehicleType, function($q) use($vehicleType){
                $q->where('vehicle_type_id', $vehicleType );
            });

            // Filter by Fuel type
            $query->when($fuelType, function($q) use($fuelType){
                $q->whereHas('model.fuelType', function ($q) use ($fuelType) {
                    $q->where('fuel_types.id', $fuelType);
                });
            });

            $vehicles = $query->latest()->paginate(30);

            if ($vehicles->isEmpty()) {
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, 'No vehicles found.');
            }

            $transformedBlogs = $vehicles->map(function ($vehicle) {
                $isShortlisted = $vehicle->shortlists()->where('user_id', auth()->user()->id)->first();

                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->brand->name.' '.$vehicle->vehicle_model,
                    'image' =>$vehicle->getFirstMediaUrl('images'),
                    'price_range' =>callPriceRange($vehicle),
                    'slug' =>$vehicle->slug,
                    'is_shortlisted' => $isShortlisted ? true : false,
                ];
            });

            return ApiResponse::success('Vehicle List', [
                'vehicles' => $transformedBlogs,
                'pagination' => [
                    'current_page' => $vehicles->currentPage(),
                    'total_pages' => $vehicles->lastPage(),
                    'total_items' => $vehicles->total(),
                    'per_page' => $vehicles->perPage(),
                ]
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    } 
    /**
     * Popular by Budget
     */
    public function popularByBudget(Request $request)
    {
        try {
            $budgetRanges = [
                '1-5 Lakh' => [100000, 500000],
                '5-10 Lakh' => [500001, 1000000],
                '10-20 Lakh' => [1000001, 2000000],
                '20-50 Lakh' => [2000001, 5000000],
                '50 Lakh - 1 Cr' => [5000001, 10000000],
                'Above 1 Cr' => [10000001, PHP_INT_MAX]
            ];

            $location = $request->location ?? 0;
            $typeId = $this->typeId;

            $result = [];
            

            foreach ($budgetRanges as $label => $range) {
                $data = [];
                $query = $this->getVehicleBaseQuery($typeId, $location);
                // Fetch vehicles that either fall within the price range or have variants within the range
                $vehicles = Vehicle::whereHas('variants', function ($query) use ($range) {
                        $query->whereBetween('price', [$range[0], $range[1]]);
                    })
                    ->orWhereBetween('price', [$range[0], $range[1]])
                    ->where('status', 1)
                    ->with(['variants' => function ($query) {
                        $query->select('id', 'vehicle_id', 'name', 'price'); // Select necessary fields
                    }])
                    ->get();

                foreach ($vehicles as $vehicle) {
                    $isShortlisted = $vehicle->shortlists()->where('user_id', auth()->user()->id)->first();

                    $data[] = [
                        'name' =>$vehicle->vehicle_model,
                        'image' =>$vehicle->getFirstMediaUrl('images'),
                        'price_range' =>callPriceRange($vehicle),
                        'slug' =>$vehicle->slug,
                        'is_shortlisted' => $isShortlisted ? true : false,
                    ];
                }

                $result[] = [
                    'budget_range' => $label,
                    'vehicles' => $data
                ];
            }

            return ApiResponse::success("Popular vehicles by budget fetched successfully.", $result);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Popular by VehicleType
     */
    public function popularByVehicleType(Request $request)
    {
        try {
            // Fetch all available vehicle types dynamically if no filter is applied
            $vehicleTypes = VehicleType::where('status', 1)
                ->get(['id','name'])
                ->unique();

            $result = [];

            $location = $request->location ?? 0;
            $typeId = $this->typeId;

            foreach ($vehicleTypes as $vehicleType) {
                // Fetch vehicles of this type
                $query = $this->getVehicleBaseQuery($typeId, $location);
                $vehicles = $query->active()
                    ->where('vehicle_type_id', $vehicleType->id)
                    ->get();

                $data = [];

                foreach ($vehicles as $vehicle) {
                    $isShortlisted = $vehicle->shortlists()->where('user_id', auth()->user()->id)->first();

                    $data[] = [
                        'name' => $vehicle->vehicle_model,
                        'image' => $vehicle->getFirstMediaUrl('images'),
                        'price_range' => callPriceRange($vehicle),
                        'slug' => $vehicle->slug,
                        'is_shortlisted' => $isShortlisted ? true : false,
                    ];
                }

                // Add to result only if there are vehicles in this type
                if (!empty($data)) {
                    $result[] = [
                        'vehicle_type' => $vehicleType,
                        'vehicles' => $data
                    ];
                }
            }

            return ApiResponse::success("Popular vehicles grouped by type fetched successfully.", $result);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Popular by VehicleType
     */
    public function popularByFuelType(Request $request)
    {
        try {
            // Fetch all available vehicle types dynamically if no filter is applied
            $fuelTypes = FuelType::where('status', 1)
                ->get(['id','name'])
                ->unique();

            $result = [];

            $location = $request->location ?? 0;
            $typeId = $this->typeId;

            foreach ($fuelTypes as $fuelType) {
                    $query = $this->getVehicleBaseQuery($typeId, $location);
                    $vehicles = $query->active()
                        ->whereHas('fuelType', function ($q) use ($fuelType) {
                            $q->where('fuel_types.id', $fuelType->id); // Correct reference inside the relation
                        })
                        ->get();

                $data = [];

                foreach ($vehicles as $vehicle) {
                    $isShortlisted = $vehicle->shortlists()->where('user_id', auth()->user()->id)->first();

                    $data[] = [
                        'name' => $vehicle->vehicle_model,
                        'image' => $vehicle->getFirstMediaUrl('images'),
                        'price_range' => callPriceRange($vehicle),
                        'slug' => $vehicle->slug,
                        'is_shortlisted' => $isShortlisted ? true : false,
                    ];
                }

                // Add to result only if there are vehicles in this type
                if (!empty($data)) {
                    $result[] = [
                        'vehicle_type' => $fuelType,
                        'vehicles' => $data
                    ];
                }
            }

            return ApiResponse::success("Popular vehicles grouped by type fetched successfully.", $result);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Vehicle Details
     */
    public function vehicleDetail(Request $request, $modelSlug, $variantSlug = null)
    {
        try {
            
            $location = $request->location ?? 0;
            $typeId = $this->typeId;

            // Base Query
            $query = $this->getVehicleBaseQuery($typeId, $location);

            $model = $query->with('faqs')->where('slug', $modelSlug)->first();
            $variants = [];

            foreach($model->variants as $item){
                //Getting vehicle attributes
                $info = '';
                foreach ($item->attributes as $attribute){
                    if($attribute->attribute->name == 'Displacement'){
                    $info .= $attribute->value." ".$attribute->attribute->unit.", ";
                    }
                    if($attribute->attribute->name == 'Transmission Type'){
                        $info .= $attribute->value.", ";
                    }
                    if($attribute->attribute->name == 'Fuel Type'){
                        $info .= $attribute->value.", ";
                    }
                    if($attribute->attribute->name == 'Petrol Mileage ARAI'){
                        $info .= $attribute->value." ".$attribute->attribute->unit;
                    }
                }

                $variants[] = [
                    'name' => $model->vehicle_model.' '. $item->name,
                    'fuel' => $item->fuelType->name,
                    'transmission' => $item->transmissionType->name,
                    'is_base_model' => $item->is_base_model,
                    'is_top_model' => $item->is_top_model,
                    'info' => $info,
                    'price' => formatPrice($item->price),
                    'slug' => $item->slug
                ];
            }

            // Result for Vehicle Model
            $result = [
                'name' => $model->brand->name.' '.$model->vehicle_model,
                'price' => callPriceRange($model),
                'images' => $model->getMedia('images'),
                'variants' => $variants,
                'colors' => $model->colors,
                'faq' => $model->faqs
            ];

            //For Variants
            if($variantSlug){
                $variant = $model->variants()->with('vehicle')->where('slug', $variantSlug)->first();
                // Features for a variant
                $features = $variant->attributes->where('attribute.parent.attribute_type','feature')->groupBy('attribute.parent.name');

                // Specifications for a variant
                $specifications = $variant->attributes->where('attribute.parent.attribute_type','specification')->groupBy('attribute.parent.name');

                // Result for vehicle variant
                $result = [
                    'name'=> $model->brand->name.' '.$model->vehicle_model.' '.$variant->name,
                    'images' => $model->getMedia('images'),
                    'price' => formatPrice($variant->price),
                    'price_info' => [
                        'price' => "₹ " . number_format($variant->price, 2),
                        'rto' => "₹ " . number_format($variant->rto, 2),
                        'insurance' => "₹ " . number_format($variant->insurance, 2),
                        'other' => "₹ " . number_format($variant->other, 2),
                        'on_road' => "₹ " . number_format($variant->on_road, 2),
                    ],
                    'specifications' => $specifications,
                    'features' => $features,
                ];
            }

            return ApiResponse::success("Detail of Vehicle Fetched.", $result);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Vehicle Price
     */
    public function showPrice(Request $request, $modelSlug)
    {
        try {
            $location = $request->location ?? 0;
            $typeId = $this->typeId;

            // Base Query
            $query = $this->getVehicleBaseQuery($typeId, $location);
            // $checkCity = City::where('name', $city)->first();

            $model = $query->with(['faqs'])->where('slug', $modelSlug)->first();
            $variants = [];
            
            foreach($model->variants as $item){
                $variants[] = [
                    'name' => $model->vehicle_model.' '. $item->name,
                    'fuel' => $item->fuelType->name,
                    'transmission' => $item->transmissionType->name,
                    'price' => numberFormat($item->showRoomPrice->isNotEmpty() ? $item->showRoomPrice[0]->price : $item->price),
                    'rto' => numberFormat($item->showRoomPrice->isNotEmpty() ? $item->showRoomPrice[0]->rto : $item->rto),
                    'insurance' => numberFormat($item->showRoomPrice->isNotEmpty() ? $item->showRoomPrice[0]->insurance : $item->insurance),
                    'other' => numberFormat($item->showRoomPrice->isNotEmpty() ? $item->showRoomPrice[0]->other : $item->other),
                    'on_road' => numberFormat($item->showRoomPrice->isNotEmpty() ? $item->showRoomPrice[0]->on_road : $item->on_road),
                ];
            }

            $result = [
                'name' => $model->brand->name.' '.$model->vehicle_model,
                'images' => $model->getFirstMediaUrl('images','webp') ?: asset('front_assets/images/resource/no-image.webp'),
                'price' => callPriceRange($model),
                'variants' => $variants,
            ];


            return ApiResponse::success("Detail of Vehicle Fethced.", $result);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Comparison between vehicle variants
     */
    public function compareVariants(Request $request)
    {
        try {
            $variantIds = $request->input('variants');

            if (empty($variantIds) || count($variantIds) < 2) {
                return ApiResponse::response(ApiResponse::HTTP_BAD_REQUEST, 'Please select at least two variants.');
            }

            $variants = VehicleVariant::with(['attributes.attribute.parent', 'vehicle.brand'])
                ->whereIn('id', $variantIds)
                ->active()
                ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $variantIds)) . ')')
                ->get();

            if ($variants->isEmpty()) {
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, 'No variants found.');
            }

            $sortedVariants = collect($variantIds)->map(fn($id) => $variants->firstWhere('id', $id))->filter();

            $transformedData = [];
            $titleArr = [];

            foreach ($sortedVariants as $vehicle) {
                $vehicleModel = $vehicle->vehicle;
                $brandName = $vehicleModel->brand->name ?? 'Unknown';
                $modelName = $vehicleModel->vehicle_model ?? 'Unknown';

                $titleArr[] = "{$brandName} {$modelName}";

                $vehicleVariants = [];

                foreach ($vehicle->attributes as $attributeData) {
                    $attribute = $attributeData->attribute;
                    $category = $attribute->parent->name ?? 'Uncategorized';
                    $categoryType = $attribute->parent->attribute_type ?? 'N/A';

                    $vehicleVariants[] = [
                        'category' => $category,
                        'category_type' => $categoryType,
                        'attribute' => $attribute->name,
                        'value' => $attributeData->value
                    ];
                }

                $transformedData[] = [
                    'vehicle_name' => $vehicle->name,
                    'vehicle_price' => formatPrice($vehicle->price),
                    'vehicle_variant' => $vehicleVariants,
                ];
            }

            $dynamicTitle = implode(' vs ', $titleArr);

            return ApiResponse::success('Comparison Data', [
                'title' => $dynamicTitle,
                'variants' => $transformedData,
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * List of Blogs
     */
    public function blogs(Request $request)
    {
        try {
            $blogs = Blog::active()->latest()->paginate(30);

            if ($blogs->isEmpty()) {
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, 'No blogs found.');
            }

            $transformedBlogs = $blogs->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'image' => $blog->getFirstMediaUrl('images/thumbnail','webp_medium') ?: asset('front_assets/images/resource/no-image.webp'),
                    'published_at' => Carbon::parse($blog->created_at)->format('F d, Y'), 
                    'author' => $blog->author_id == 1 ? 'IMG' : 'Car Dekho',
                ];
            });

            return ApiResponse::success('Blog List', [
                'blogs' => $transformedBlogs,
                'pagination' => [
                    'current_page' => $blogs->currentPage(),
                    'total_pages' => $blogs->lastPage(),
                    'total_items' => $blogs->total(),
                    'per_page' => $blogs->perPage(),
                ]
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Blog Details
     */
    public function blogSingle(Request $request, $slug)
    {
        try {
            $blog = Blog::where('slug', $slug)->firstOrFail();

            $latestBlogs = Blog::where('id', '!=', $blog->id)->active()->latest()->limit(3)->get();

            $blogData = [
                'id' => $blog->id,
                'title' => $blog->title,
                'content' => $blog->description,
                'image' => $blog->getFirstMediaUrl('images/blog','webp_medium') ?: asset('front_assets/images/resource/no-image.webp'),
                'published_at' => Carbon::parse($blog->created_at)->format('F d, Y'), 
                'author' => $blog->author_id == 1 ? 'IMG' : 'Car Dekho',
            ];

            $relatedBlogs = $latestBlogs->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'image' => $item->getFirstMediaUrl('images/thumbnail','webp_medium') ?: asset('front_assets/images/resource/no-image.webp'),
                    'published_at' => Carbon::parse($item->created_at)->format('F d, Y'), 
                    'author' => $item->author_id == 1 ? 'IMG' : 'Car Dekho',
                ];
            });

            return ApiResponse::success('Blog Detail', [
                'blog' => $blogData,
                'latest_blogs' => $relatedBlogs,
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Sell Your Vehicle (Info)
     */
    public function sellVehicleInfo(Request $request)
    {   
        try {
            $user = auth()->user(); // Get the authenticated user

            if (!$user) {
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "User not authenticated.");
            }
            
            $pending = Status::PENDING->value;
            $vehicle = UsedVehicle::where('user_id', $user->id)->where('verification_status',$pending)->first();
            $images = [];
            foreach($vehicle->getMedia('images') as $image){
                $images[] = $image->getUrl();
            }

            $result = [
                'name' => $vehicle->reg_year.' '. $vehicle->model->brand->name.' '.$vehicle->model->vehicle_model.' '.$vehicle->variant->name,
                'kms_driven' => number_format($vehicle->kms_driven).' kms',
                'transmission' => $vehicle->variant->transmissionType->name,
                'fuel' => $vehicle->variant->fuelType->name,
                'ownership' => $this->ownership($vehicle->ownership),
                'expected_price' => number_format($vehicle->price, 2),
                'images' => $images,

                // Overview of vehicle
                'vehicle_details' => [
                    'body_type' => $vehicle->model->vehicleType->name,
                    'city' => $vehicle->city->name,
                    'odometer' => $vehicle->kms_driven,
                    'fuel_type' => $vehicle->variant->fuelType->name,
                    'make' => $vehicle->model->brand->name,
                    'model' => $vehicle->model->vehicle_model,
                    'make_year' =>  $vehicle->make_year,
                    'owner_count' => $vehicle->ownership,
                    'reg_year' => $vehicle->reg_year,
                    'transmission' => $vehicle->variant->transmissionType->name,
                    'variant' => $vehicle->variant->name,
                ]
            ];

            // Return the success message
            return ApiResponse::success("Vehicle info.", $result);
            
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            // return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Sell Your Vehicle (Save)
     */
    public function sellVehicle(Request $request)
    {   
        try {
            $user = auth()->user(); // Get the authenticated user

            if (!$user) {
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "User not authenticated.");
            }

            // Validate request data using Validator
            $validator = Validator::make($request->all(), [
                'brand_id' => 'required',
                'reg_year' => 'required',
                'vehicle_id' => 'required',
                'vehicle_variant_id' => 'required',
                'ownership' => 'required',
                'kms_driven' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'expected_price' => 'required',
                'images' => 'required', 
                'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::response(ApiResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation failed.", [
                    'errors' => $validator->errors()
                ]);
            }

            DB::beginTransaction();
            
            // Saving info of the vehicle
            $vehicle = UsedVehicle::create([
                'type_id' => $this->typeId,
                'user_id' => $user->id,
                'brand_id' => $request->brand_id,
                'reg_year' => $request->reg_year,
                'make_year' => $request->reg_year,
                'vehicle_id' => $request->vehicle_id,
                'vehicle_variant_id' => $request->vehicle_variant_id,
                'slug' => 'slug',
                'ownership' => $request->ownership,
                'kms_driven' => $request->kms_driven,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'price' => $request->expected_price
            ]);

            if($vehicle){
                if($request->hasFile('images')){
                    foreach($request->file('images') as $image){
                        $this->uploadMedia($image, $vehicle, 'images');
                    }
                }
            }

            DB::commit();

            // Return the success message
            return ApiResponse::success("Vehicle info saved successfully.");
            
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
    /**
     * Used vehicle list
     */
    public function usedVehicle(Request $request)
    {
        try {

            // Filter options
            $brand = $request->get('brand_id') ?? null;
            $location = $request->get('location') ?? null;
            $budget = $request->get('budget') ?? null;
            $vehicleType = $request->get('vehicle_type') ?? null;

            $typeId = $this->typeId;

            $query =$this->getUsedVehicleBaseQuery($typeId, $location);

            // Filter by brand
            $query->when($brand, function($q) use($brand){
                $q->whereHas('model', function($q) use($brand){
                    $q->where('brand_id', $brand);
                });
            });

            // Filter by vehicle type
            $query->when($vehicleType, function($q) use($vehicleType){
                $q->whereHas('model', function($q) use($vehicleType){
                    $q->where('vehicle_type_id', $vehicleType );
                });
            });

            $vehicles = $query->latest()->paginate(30);

            if ($vehicles->isEmpty()) {
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, 'No vehicles found.');
            }

            $transformedBlogs = $vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->reg_year.' '. $vehicle->model->brand->name.' '.$vehicle->model->vehicle_model.' '.$vehicle->variant->name,
                    'kms_driven' => number_format($vehicle->kms_driven).' kms',
                    'transmission' => $vehicle->variant->transmissionType->name,
                    'fuel' => $vehicle->variant->fuelType->name,
                    'ownership' => $this->ownership($vehicle->ownership),
                    'price' => formatPrice($vehicle->price),
                    'slug' => $vehicle->slug,
                    'location' => $vehicle->city->name,
                    'vehicle_type' => $vehicle->model->vehicleType->name,
                    'images' => $vehicle->getFirstMediaUrl('images') ?: asset('front_assets/images/resource/no-image.webp'),
                ];
            });

            return ApiResponse::success('Vehicle List', [
                'vehicles' => $transformedBlogs,
                'pagination' => [
                    'current_page' => $vehicles->currentPage(),
                    'total_pages' => $vehicles->lastPage(),
                    'total_items' => $vehicles->total(),
                    'per_page' => $vehicles->perPage(),
                ]
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
    /**
     * Used vehicle detail
     */
    public function usedVehicleDetail(Request $request)
    {
        try {

            // Filter options
            $location = $request->get('location') ?? null;
            $typeId = $this->typeId;
            $vehicleId = $request->get('vehicle_id');

            $query =$this->getUsedVehicleBaseQuery($typeId, $location);

            $vehicle = $query->clone()->where('id', $vehicleId)->first();

            if(!$vehicle){
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, 'Vehicle not found.');
            }

            // Vehicle images
            $images = [];
            foreach($vehicle->getMedia('images') as $image){
                $images[] = $image->getUrl();
            }

            // Overview
            $vehicleDetail = [
                'body_type' => $vehicle->model->vehicleType->name,
                'city' => $vehicle->city->name,
                'odometer' => $vehicle->kms_driven,
                'fuel_type' => $vehicle->variant->fuelType->name,
                'make' => $vehicle->model->brand->name,
                'model' => $vehicle->model->vehicle_model,
                'make_year' =>  $vehicle->make_year,
                'owner_count' => $vehicle->ownership,
                'reg_year' => $vehicle->reg_year,
                'transmission' => $vehicle->variant->transmissionType->name,
                'variant' => $vehicle->variant->name,
            ];

            // Features of vehicle
            $features = $vehicle->variant->attributes->where('attribute.parent.attribute_type','feature')->groupBy('attribute.parent.name');

            // Specifications of vehicle
            $specifications = $vehicle->variant->attributes->where('attribute.parent.attribute_type','specification')->groupBy('attribute.parent.name');

            // Recommended vehicles
            $recommended = $query->clone()->where('id', '!=', $vehicle->id)->latest()->limit(10)->get();

            $recommendedResult = $recommended->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->reg_year.' '. $vehicle->model->brand->name.' '.$vehicle->model->vehicle_model.' '.$vehicle->variant->name,
                    'kms_driven' => number_format($vehicle->kms_driven).' kms',
                    'transmission' => $vehicle->variant->transmissionType->name,
                    'fuel' => $vehicle->variant->fuelType->name,
                    'ownership' => $this->ownership($vehicle->ownership),
                    'price' => formatPrice($vehicle->price),
                    'slug' => $vehicle->slug,
                    'location' => $vehicle->city->name,
                    'vehicle_type' => $vehicle->model->vehicleType->name,
                    'images' => $vehicle->getFirstMediaUrl('images') ?: asset('front_assets/images/resource/no-image.webp'),
                ];
            });

            // Result
            $result = [
                'name' => $vehicle->reg_year.' '. $vehicle->model->brand->name.' '.$vehicle->model->vehicle_model.' '.$vehicle->variant->name,
                'kms_driven' => number_format($vehicle->kms_driven).' kms',
                'transmission' => $vehicle->variant->transmissionType->name,
                'fuel' => $vehicle->variant->fuelType->name,
                'ownership' => $this->ownership($vehicle->ownership),
                'expected_price' => number_format($vehicle->price, 2),
                'images' => $images,

                'vehicle_details' => $vehicleDetail,
                'features' => $features,
                'specifications' => $specifications,
                'recommended' => $recommendedResult,
            ];

            // Return the success message with data
            return ApiResponse::success("Vehicle info.", $result);

        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    
    /**
     * Terms and Condition
     */
    public function info($slug)
    {
        $data = \App\Models\Info::where('key', $slug)->first();

        if (!$data) {
            return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, 'No info.');
        }

        return ApiResponse::success('Info', $data);
    }

    /**
     * Submit contact info
     */
    public function submitContact(Request $request)
    {
        // Validate request data using Validator
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'message' => 'required'
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::response(ApiResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation failed.", [
                'errors' => $validator->errors()
            ]);
        }

        try{
            $details['contact'] = $request->all();
            $details['subject'] = 'Contact Us';
            $details['email'] = env('MAIL_FROM_ADDRESS');
            $details['view'] = 'emails.contact_us';

            // Sending the mail
            // Mail::to($details['email'])->send(new SendMail($details));

            // Dispatch send mail job
            SendMailJob::dispatch($details);

            return ApiResponse::success("Message sent successfully.");

        }catch(\Throwable $e){
            Log::error($e->getMessage());

            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}
