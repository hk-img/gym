<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Send OTP to the user.
     */
    public function sendOtp(Request $request)
    {
        // Validate input using Validator
        $validator = Validator::make($request->all(), [
            'gym_id' => 'required',
            'phone' => 'required|numeric',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::response(ApiResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation Error", $validator->errors());
        }

        try {
            DB::beginTransaction();

            // Generate OTP (use rand(1000, 9999) in production)
            $otp = 1234;
            $user = User::where('phone', $request->phone)->whereHas('addedBy', function($q)use($request){
                $q->where('gym_id', $request->gym_id);
            })->first();
            if(!$user){
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "Invalid credentials.");
            }

            $user->update(['otp' => $otp, 'otp_sent_at' => Carbon::now()]);

            // $this->smsService->sendSms($request->phone, "Your OTP is $otp");

            DB::commit();

            return ApiResponse::success("OTP has been sent to {$request->phone}");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    /**
     * Verify OTP and login user.
     */
    public function verifyOtp(Request $request)
    {
        // Validate input using Validator
        $validator = Validator::make($request->all(), [
            'gym_id' => 'required',
            'phone' => 'required|numeric',
            'otp' => 'required|numeric'
        ]);
        
        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::response(ApiResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation Error", $validator->errors());
        }

        try {
            DB::beginTransaction();

            $user = User::where('phone', $request->phone)->whereHas('addedBy', function($q)use($request){
                $q->where('gym_id', $request->gym_id);
            })->first();

            if (!$user || $user->otp !== $request->otp) {
                return ApiResponse::notFound('Invalid OTP.');
            }

            if (Carbon::parse($user->otp_sent_at)->addMinutes(5)->isPast()) {
                return ApiResponse::response(ApiResponse::HTTP_BAD_REQUEST, 'OTP expired.');
            }

            $user->update(['otp' => null]);

            // Generate API Token
            $token = $user->createToken('AuthToken')->accessToken;

            DB::commit();

            return ApiResponse::success('Login successful.', [
                'token' => $token,
                'user' => $user,
                'step' => $user->name == null
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        // Validate input using Validator
        $validator = Validator::make($request->all(), [
            'gym_id' => 'required',
            'phone' => 'required|numeric'
        ]);
        
        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::response(ApiResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation Error", $validator->errors());
        }
        try {
            DB::beginTransaction();
            $request->validate(['phone' => 'required|numeric']);

            $user = User::where('phone', $request->phone)->whereHas('addedBy', function($q)use($request){
                $q->where('gym_id', $request->gym_id);
            })->first();

            if (!$user) {
                return ApiResponse::notFound('Phone number not registered.');
            }

            // Generate OTP (use rand(1000, 9999) in production)
            $otp = 1234;
            $user->update(['otp' => $otp, 'otp_sent_at' => Carbon::now()]);

            DB::commit();

            // $this->smsService->sendSms($request->phone, "Your OTP is $otp");

            return ApiResponse::success("OTP has been resent to {$request->phone}");

        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Logout user.
     */
    public function logOut()
    {
        Auth::user()->token()->revoke();
        return ApiResponse::success('Logged out successfully.');
    }
}
