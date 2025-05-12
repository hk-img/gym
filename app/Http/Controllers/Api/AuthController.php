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

    public function login(Request $request) {
        try {
            $userResult = \App\Models\User::where('username', $request->input('username'))->first();
    
            if ($userResult) {
                if (!\Hash::check($request->password, $userResult->password)) {
                    return response()->json(["error" => true, "message" => "Invalid Password."], 200);
                }
    
                if ($userResult->status != 1) {
                    return response()->json(["error" => true, "message" => "User not Verified Yet."], 200);
                }
    
                $token = $userResult->createToken($request->input('username'));
                $accessToken = $token->accessToken;
    
                // Select only required fields
                $userData = [
                    'name' => $userResult->name,
                    'mobile' => $userResult->mobile,
                    'address' => $userResult->address,
                    'username' => $userResult->username,
                    'time_slot' => $userResult->time_slot,
                    'membership_status' => $userResult->membership_status,
                ];
    
                $response = [
                    'error' => false,
                    "message" => 'User Login Successfully.',
                    'user' => $userData,
                    'token' => $accessToken
                ];
    
                return response($response, 200);
            } else {
                return response()->json(['error' => true, 'message' => 'Invalid Username.'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["error" => true, "message" => $e->getMessage()], 200);
        }
    }

    public function myProfile()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => true, 'message' => 'User not authenticated.'], 401);
            }

            $user = \App\Models\User::with([
                'assignActivity.activity',
                'assignPT.trainer',
                'assignPlan.plan'
            ])->find($user->id);

            // Format assignActivity
            $assignActivity = $user->assignActivity->transform(function ($val) {
                return [
                    'id' => $val->id,
                    'user_id' => $val->user_id,
                    'package_id' => $val->package_id,
                    'duration' => $val->duration,
                    'discount' => $val->discount,
                    'start_date' => $val->start_date,
                    'end_date' => $val->end_date,
                    'user_type' => $val->user_type,
                    'payment_method' => $val->payment_method,
                    'utr' => $val->utr,
                    'package_name' => optional($val->activity)->title,
                    'package_charges' => optional($val->activity)->charges,
                    'package_duration' => optional($val->activity)->duration,
                ];
            });

            // Format assignPT
            $assignPt = $user->assignPT->transform(function ($val) {
                return [
                    'id' => $val->id,
                    'user_id' => $val->user_id,
                    'trainer_id' => $val->trainer_id,
                    'months' => $val->months,
                    'discount' => $val->discount,
                    'start_date' => $val->start_date,
                    'end_date' => $val->end_date,
                    'user_type' => $val->user_type,
                    'payment_method' => $val->payment_method,
                    'utr' => $val->utr,
                    'trainer_name' => optional($val->trainer)->name,
                    'trainer_phone' => optional($val->trainer)->phone,
                    'trainer_pt_fees' => optional($val->trainer)->pt_fees,
                ];
            });

            // Format assignPlan
            $assignPlan = $user->assignPlan->transform(function ($val) {
                return [
                    'id' => $val->id,
                    'user_id' => $val->user_id,
                    'plan_id' => $val->plan_id,
                    'days' => $val->days,
                    'discount' => $val->discount,
                    'start_date' => $val->start_date,
                    'end_date' => $val->end_date,
                    'user_type' => $val->user_type,
                    'membership_status' => $val->membership_status,
                    'payment_method' => $val->payment_method,
                    'utr' => $val->utr,
                    'plan_name' => optional($val->plan)->name,
                    'plan_duration' => optional($val->plan)->duration,
                    'plan_price' => optional($val->plan)->price,
                ];
            });

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'profile_image' => $user->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/user.jpg'),
                'assign_activity' => $assignActivity,
                'assign_p_t' => $assignPt,
                'assign_plan' => $assignPlan,
            ];

            return response()->json([
                'error' => false,
                'message' => 'Profile details fetched successfully.',
                'user' => $userData
            ], 200);
            
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
    /**
     * Logout user.
     */
    public function logOut()
    {
        Auth::user()->token()->revoke();
        return response()->json(['error' => false, 'message' => 'Logged out successfully.'], 200);

    }
}
