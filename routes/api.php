<?php

use App\Http\Controllers\Api\Account\ProfileController;
use App\Http\Controllers\Api\Account\ShortlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FilterOptionController;
use App\Http\Controllers\Api\HomeController;

Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logOut']); 
    // Home Section API's
        Route::get('/get-cities', [HomeController::class, 'getCities']); 
        Route::get('/banner-list', [HomeController::class, 'bannerList']); 
        Route::get('/brand-list', [HomeController::class, 'brandList']);
        Route::get('/single-brand/{slug}', [HomeController::class, 'singleBrand']); 
        Route::get('/compare-variants', [HomeController::class, 'compareVariants']); 
        
        /** Recommendation */
        Route::get('/popular-by-budget', [HomeController::class, 'popularByBudget']); 
        Route::get('/popular-by-vehicle-type', [HomeController::class, 'popularByVehicleType']); 
        Route::get('/popular-by-fuel-type', [HomeController::class, 'popularByFuelType']); 
        
        /** Vehicle */
        Route::get('/vehicle-list', [HomeController::class, 'vehicleList']);
        Route::get('/vehicle-price/{modelSlug}', [HomeController::class, 'showPrice']);
        Route::get('/vehicle-detail/{modelSlug}/{variantSlug?}', [HomeController::class, 'vehicleDetail']); 
        
        /** Sell Vehicle */
        Route::get('/sell-vehicle-info', [HomeController::class, 'sellVehicleInfo']);
        Route::post('/sell-vehicle', [HomeController::class, 'sellVehicle']);

        /** Used Vehicle */
        Route::get('/used-vehicle-list', [HomeController::class, 'usedVehicle']);
        Route::get('/used-vehicle-detail', [HomeController::class, 'usedVehicleDetail']);

        /** Term and Condtion and Privacy Policy */
        Route::get('/info/{slug?}', [HomeController::class, 'info']);
        
        Route::post('/submit-contact', [HomeController::class, 'submitContact']);

    /** Filter  Options */
    // Route::get('/by-budget', [FilterOptionController::class, 'byBudegt']); 
    Route::get('/by-vehicle-type', [FilterOptionController::class, 'byVehicleType']); 
    Route::get('/by-fuel', [FilterOptionController::class, 'byFuel']); 
    Route::get('/by-transmission', [FilterOptionController::class, 'byTransmission']); 
    
    /** Blog API's */
    Route::get('/blog-list', [HomeController::class, 'blogs']); 
    Route::get('/single-blog/{slug}', [HomeController::class, 'blogSingle']); 
    
    /** Profile Section API's */
    Route::get('/my-profile', [ProfileController::class, 'myProfile']); 
    Route::get('/update-profile', [ProfileController::class, 'updateProfile']); 

    /** Shortlisted Vehicle API's */
    Route::get('/get-shortlisted-vehicles', [ShortlistController::class, 'fetchShortlist']); 
    Route::get('/toggle-shortlisted-vehicles', [ShortlistController::class, 'toggleShortlist']); 
    Route::get('/delete-shortlisted-vehicles', [ShortlistController::class, 'deleteShortlist']); 

    /** Address API's */
    Route::get('/address-list', [ProfileController::class, 'addressList']); 
    Route::get('/add-address', [ProfileController::class, 'addAddress']);  
    Route::post('/update-default-address', [ProfileController::class, 'updateDefaultAddress']); 
    Route::post('/update-address', [ProfileController::class, 'updateAddress']); 
    Route::delete('/delete-address', [ProfileController::class, 'deleteAddress']); 
    Route::get('/edit-address', [ProfileController::class, 'editAddress']);

});
