<?php

namespace App\Http\Controllers\Api\Account;

use App\Api\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            // Get the user's default address
            $defaultAddress = $user->addresses()->where('is_default', true)->first();

            // Get user documents
            $documents = $user->documents()->get();

            // Return API response with profile details
            return ApiResponse::success("Profile details fetched successfully.", [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'profile_image' => $user->getFirstMediaUrl('profile_pictures') 
                        ?: asset('front_assets/images/resource/default-profile.webp')
                ],
                'default_address' => $defaultAddress,
                'documents' => $documents
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
                'email' => 'required|email|unique:users,email,' . $user->id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Increased image size limit
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
                'email' => $request->email,
                'phone' => $request->phone
            ]);

            // Handle profile image update
            if ($request->hasFile('image')) {
                $user->clearMediaCollection('profile_pictures'); // Clear old image
                $user->addMedia($request->file('image'))->toMediaCollection('profile_pictures'); // Upload new image
            }

            DB::commit();

            // Return updated user data
            return ApiResponse::success("Profile updated successfully.", [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'profile_image' => $user->getFirstMediaUrl('profile_pictures') 
                        ?: asset('front_assets/images/resource/default-profile.webp')
                ]
            ]);
            
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    public function addressList(){
        try{
            $addresses = Address::with(['city','state'])->where('user_id', Auth::id())->get();
            return ApiResponse::success("Address list.", $addresses );
        }catch(\Throwable $e){
            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function updateDefaultAddress(Request $request)
    {
        try {
            $user = auth()->user(); // Get authenticated user

            if (!$user) {
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "User not authenticated.");
            }

            // Validate request data
            $validator = Validator::make($request->all(), [
                'address_id' => 'required|exists:addresses,id'
            ]);

            if ($validator->fails()) {
                return ApiResponse::response(ApiResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation failed.", [
                    'errors' => $validator->errors()
                ]);
            }

            DB::beginTransaction();

            // Reset all addresses to non-default
            $user->addresses()->update(['is_default' => false]);

            // Set the selected address as default
            $address = $user->addresses()->where('id', $request->address_id)->first();

            if (!$address) {
                DB::rollBack();
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, "Address not found.");
            }

            $address->update(['is_default' => true]);

            DB::commit();

            return ApiResponse::success("Address set as default successfully.", [
                'address' => $address
            ]);
            
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    public function updateAddress(Request $request)
    {
        try {
            $user = auth()->user(); // Get the authenticated user

            if (!$user) {
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "User not authenticated.");
            }

            // Validate request data
            $validator = Validator::make($request->all(), [
                'address_line1' => 'required|string|min:3|max:50',
                'address_line2' => 'nullable|string|max:50',
                'landmark' => 'nullable|string|max:50',
                'pincode' => 'required|digits:6',
                'city_id' => 'required|string',
                'state_id' => 'required|string',
                'address_type' => 'required|in:home,office,other',
                'address_id' => 'nullable|exists:addresses,id' // Optional but must exist if provided
            ]);

            if ($validator->fails()) {
                return ApiResponse::response(ApiResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation failed.", [
                    'errors' => $validator->errors()
                ]);
            }

            DB::beginTransaction();

            // Check if updating an existing address
            $addressId = $request->address_id;
            if ($addressId) {
                // Find the existing address for the user
                $address = $user->addresses()->where('id', $addressId)->first();

                if (!$address) {
                    return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, "Address not found.");
                }

                // Update the address
                $address->update($request->only([
                    'address_line1', 'address_line2', 'landmark', 'pincode',
                    'city_id', 'state_id', 'address_type'
                ]));

                $message = "Address updated successfully.";
            } else {
                // Create a new address and mark it as default
                $address = $user->addresses()->create(array_merge(
                    $request->only([
                        'address_line1', 'address_line2', 'landmark', 'pincode',
                        'city_id', 'state_id', 'address_type'
                    ]),
                    ['is_default' => true] // New address is default
                ));

                $message = "Address added successfully.";
            }

            // Ensure only one default address
            if ($address->is_default) {
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            DB::commit();

            return ApiResponse::success($message, [
                'address' => $address
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    public function deleteAddress($id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "User not authenticated.");
            }

            // Find the address by ID and ensure it belongs to the authenticated user
            $address = $user->addresses()->where('id', $id)->first();

            if (!$address) {
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, "Address not found.");
            }

            DB::beginTransaction();

            // Check if the address is the default one
            $isDefault = $address->is_default;

            // Delete the address
            $address->delete();

            // If the deleted address was the default, set another address as default
            if ($isDefault) {
                $newDefault = $user->addresses()->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            DB::commit();

            return ApiResponse::success("Address deleted successfully.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error deleting address: " . $e->getMessage());

            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Failed to delete address.");
        }
    }

    public function editAddress($id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return ApiResponse::response(ApiResponse::HTTP_UNAUTHORIZED, "User not authenticated.");
            }

            $decodedAddressId = base64_decode($id);
            $address = $user->addresses()->where('id', $decodedAddressId)->first();

            if (!$address) {
                return ApiResponse::response(ApiResponse::HTTP_NOT_FOUND, "Address not found.");
            }

            $states = State::all();

            return ApiResponse::success("Address details fetched successfully.", [
                'address' => $address,
                'states' => $states
            ]);

        } catch (\Throwable $e) {
            Log::error("Error fetching address details: " . $e->getMessage());

            return ApiResponse::response(ApiResponse::HTTP_INTERNAL_SERVER_ERROR, "Failed to fetch address details.");
        }
    }

    public function updateDocument(Request $request)
    {
        $userId = Auth::id();

        // Validate request
        $request->validate([
            'document_type' => 'required|string|max:50',
            'name' => 'required|string|max:50',
            'document_number' => 'required',
            'file' => 'required|file|mimes:jpg,jpeg,pdf|max:10240' // Max 10MB
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($userId);

            // Check if the document type already exists for the user
            $existingDocument = $user->documents()->where('document_type', $request->document_type)->first();

            if ($existingDocument) {
                // Update the existing document record
                $existingDocument->update([
                    'name_in_document' => $request->name,
                    'document_number' => $request->document_number
                ]);
            } else {
                // Store new document details in DB
                $user->documents()->create([
                    'document_type' => $request->document_type,
                    'name_in_document' => $request->name,
                    'document_number' => $request->document_number
                ]);
            }

            // Upload file using Spatie Media Library
            if ($request->hasFile('file')) {
                // If there's an existing media, remove it before uploading the new one
                if ($user->hasMedia('documents')) {
                    $user->clearMediaCollection('documents');
                }

                // Add the new file to the media collection
                $user->addMedia($request->file('file'))->toMediaCollection('documents');
            }

            DB::commit();

            return response()->json(['message' => 'Document uploaded successfully.']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }
}
