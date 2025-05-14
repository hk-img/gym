<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AssignPlan;
use App\Models\DietPlan;
use App\Models\GymSocialLinks;
use App\Models\GymWorkingHour;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\AssignPT;
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

    /**
     * Get Workout
     */
    public function getWorkout(Request $request)
    {
        try {

            $workouts = Workout::with(['exercises'])->get();

            if($workouts->isEmpty()){
                return response()->json([
                    'error' => false,
                    'message' => 'No workout found.'
                ], 200);

            }
            
            return response()->json(['error'=>false,'message'=>"Workout details fetched successfully.", 'data'=>$dietPlan],200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Workout assignment error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again.'
            ], 200);
        }
    }

    /**
     * Get Diet Plan
     */
    public function getDietPlan(Request $request)
    {
        try {

            $dietPlan = DietPlan::with(['meals'])->get();

            // Return API response with fetched data
            return response()->json(['error'=>false,'message'=>"Diet Plan fetched successfully.", 'data'=>$dietPlan],200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Plan assignment error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again.'
            ], 200);
        }
    }

    /**
     * Get Assign Plan
     */
    public function getTrainers(Request $request)
    {
        try {

            if(!auth()->user()){
                return response()->json(['error'=>true,'message'=> 'Authenticated.'],200);
            }

            $getTrainers = User::where('salary','>',0)->where('added_by', auth()->user()->added_by)->get();

            if($getTrainers->isEmpty()){
                return response()->json(['error'=>false,'message'=> 'No Trainers Found.','data'=>[]],200);
            }

            return response()->json(['error'=>false,'message'=>"Trainers fetched successfully.", 'data'=>$getTrainers],200);
        } catch (\Throwable $e) {
            return response()->json(['error'=>true,'message'=> $e->getMessage()],200);
        }
    }


    public function assignPt(Request $request)
    {
        $rules = [
            'trainer_id' => 'required|exists:users,id',
            'months' => 'required|integer|min:1',
            'payment_method' => 'required|in:online,offline',
            'discount' => 'nullable|numeric',
            'utr' => [
                'required_if:payment_method,online',
                'nullable',
                Rule::unique('assign_p_t_s', 'utr'),
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();

        try {
            
            $user = User::where('id', $request->user()->id)
                ->where('membership_status', 'pending')
                ->first();

            if ($user) {
                return response()->json([
                    'error' => true,
                    'message' => 'Your membership is pending.'
                ], 200);
            }

            $existingAssignment = AssignPT::where('user_id', $request->user()->id)->first();
            $user_type = $existingAssignment ? 'old' : 'new';

            $startDate = Carbon::parse($request->start_date);
            $months = (int) $request->months;
            $endDate = $startDate->copy()->addMonths($months);

            $input = $request->all();
            $input['months'] = $months;
            $input['start_date'] = $startDate;
            $input['end_date'] = $endDate;
            $input['user_type'] = $user_type;
            $input['user_id'] = $request->user()->id;

            $assignPlan = AssignPT::create($input);

            $assignPlan->user()->update([
                'pt_start_date' => $startDate,
                'pt_end_date' => $endDate
            ]);

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => 'PT assigned successfully.',
                'data' => $assignPlan
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PT assignment error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again.'
            ], 200);
        }
    }

    public function getActivity(Request $request){

        try{

            $data = \App\Models\Activity::where('added_by', $request->user()->added_by)
                ->latest()->get();

            if($data->isEmpty()){
                return response()->json([
                    'error' => false,
                    'message' => 'No activities found.',
                    'data' => []
                ],200);

            }

            return response()->json([
                'error' => false,
                'message' => 'Activities fetched successfully.',
                'data' => $data
            ],200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PT assignment error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again.'
            ], 200);
        }
    }

    public function assignActivity(Request $request)
    {
        $rules = [
            'package_id' => 'required|exists:activities,id',
            'duration' => 'required',
            'payment_method' => 'required|in:online,offline',
            'discount' => 'nullable|numeric',
            'utr' => [
                'required_if:payment_method,online',
                'nullable',
                Rule::unique('assign_packages', 'utr'),
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();

        try {
            $user = User::where('id', $request->user()->id)
                ->where('membership_status', 'pending')
                ->first();

            if ($user) {
                return response()->json([
                    'error' => true,
                    'message' => 'Your membership is pending.'
                ], 200);
            }

            $existingAssignment = \App\Models\AssignPackage::where('user_id', $request->user()->id)->first();
            $user_type = $existingAssignment ? 'old' : 'new';

            $plan = \App\Models\Activity::findOrFail($request->package_id);
            
            $days = intval($plan->duration);

            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addDays($days);

            $input = $request->all();
            $input['duration'] = $request->duration;
            $input['start_date'] = $startDate;
            $input['end_date'] = $endDate;
            $input['user_type'] = $user_type;
            $input['user_id'] = $request->user()->id;

            $assignPlan = \App\Models\AssignPackage::create($input);

            $assignPlan->user()->update(['package_status' => 'active']);

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => 'Activity assigned successfully.',
                'data' => $assignPlan
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Activity assignment error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again.'
            ], 200);
        }
    }

    public function getPlan(Request $request){

        try{

            $data = \App\Models\Plan::where('created_by', auth()->user()->added_by)->get();

            if($data->isEmpty()){
                return response()->json([
                    'error' => false,
                    'message' => 'No plans found.',
                    'data' => []
                ],200);

            }

            return response()->json([
                'error' => false,
                'message' => 'Plans fetched successfully.',
                'data' => $data
            ],200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Plan assignment error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again.'
            ], 200);
        }
    }

    public function assignPlan(Request $request)
    {
        $rules = [
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:online,offline',
            'discount' => 'nullable|numeric',
            'utr' => [
                'required_if:payment_method,online',
                'nullable',
                Rule::unique('assign_plans', 'utr'),
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();

        try {
            $user = User::where('id', $request->user()->id)
                ->where('membership_status', 'pending')
                ->first();

            if ($user) {
                return response()->json([
                    'error' => true,
                    'message' => 'Your membership is pending.'
                ], 200);
            }

            $existingAssignment = \App\Models\AssignPlan::where('user_id', $request->user()->id)->first();
            $user_type = $existingAssignment ? 'old' : 'new';

            $plan = \App\Models\Plan::findOrFail($request->plan_id);
            
            $days = intval($plan->duration);

            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addDays($days);

            $input = $request->all();
            $input['days'] = $days;
            $input['start_date'] = $startDate;
            $input['end_date'] = $endDate;
            $input['user_type'] = $user_type;
            $input['user_id'] = $request->user()->id;

            $assignPlan = \App\Models\AssignPlan::create($input);

            $assignPlan->user()->update(['start_date' => $startDate, 'end_date' => $endDate, 'membership_status' => 'active']);

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => 'Plan assigned successfully.',
                'data' => $assignPlan
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PLan assignment error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again.'
            ], 200);
        }
    }

}
