<?php

use App\Http\Controllers\Api\Account\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;

/** V1 API Routes  */
Route::prefix('v1')->group(function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
});

Route::middleware('auth:api')->group(function () {

    /** V1 API Routes  */
    Route::prefix('v1')->group(function(){
        Route::post('/logout', [AuthController::class, 'logOut']); 
    
        /** Profile Section API's */
        Route::get('/my-profile', [ProfileController::class, 'myProfile']); 
        Route::post('/update-profile', [ProfileController::class, 'updateProfile']); 
        
        Route::get('/get-working-hours', [HomeController::class, 'getWorkingHour']); 
        Route::get('/get-social-links', [HomeController::class, 'socialLinks']); 
    
        Route::get('membership-details', [HomeController::class, 'memberShipDetails']);

        Route::get('get-workout', [HomeController::class, 'getWorkout']);

        Route::get('get-dietplan', [HomeController::class, 'getDietPlan']);
    }); 
});
