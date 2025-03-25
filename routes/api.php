<?php

use App\Http\Controllers\Api\Account\ProfileController;
use App\Http\Controllers\Api\Account\ShortlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FilterOptionController;
use App\Http\Controllers\Api\HomeController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logOut']); 
 
    /** Profile Section API's */
    Route::get('/my-profile', [ProfileController::class, 'myProfile']); 
    Route::post('/update-profile', [ProfileController::class, 'updateProfile']); 
    
    Route::get('/get-working-hours', [HomeController::class, 'getWorkingHour']); 
    Route::get('/get-social-links', [HomeController::class, 'socialLinks']); 
});
