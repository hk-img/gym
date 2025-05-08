<?php

namespace App\Http\Controllers\Api\Account;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\Traits;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use Traits;

    public function myProfile()
    {
        try {
            $user = auth()->user(); // Get the authenticated user

            if (!$user) {
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "User not authenticated.");
            }

            // Return API response with profile details
            return ApiResponse::success("Profile details fetched successfully.", [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'profile_image' => $user->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/user.jpg')
                ],
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {   
        try {
            $user = auth()->user(); // Get the authenticated user

            if (!$user) {
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "User not authenticated.");
            }

            // Validate request data using Validator
            $validator = Validator::make($request->all(), [
                'phone' => 'required|numeric',
                'name' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::response(ApiResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation failed.", [
                    'errors' => $validator->errors()
                ]);
            }

            DB::beginTransaction();

            // Update user details
            $user->update([
                'name' => $request->name,
                'phone' => $request->phone
            ]);

            // Handle profile image update
            if ($user) {
                if ($request->hasFile('image')) {

                    if ($user->hasMedia('images')) {
                        $user->clearMediaCollection('images'); // Deletes all media in the 'images' collection
                    }

                    $this->uploadMedia($request->file('image'), $user, 'images');
                }
            }

            DB::commit();

            // Return updated user data
            return ApiResponse::success("Profile updated successfully.", [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'profile_image' => $user->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/user.jpg')
                ]
            ]);
            
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}
