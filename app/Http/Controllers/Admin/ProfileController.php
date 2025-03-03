<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {   
        $user = User::with('media')->where('id',$request->user()->id)->first();
        return view('admin.pages.profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    // public function update(Request $request): RedirectResponse
    // {
    //     $valiator = $request->validate([
    //         'name' => 'required',
    //         'image' => 'image|mimes:jpg,png,jpeg|max:2048',
    //     ]);
        
    //     $user = User::where('id', Auth::user()->id)->first();

    //     // Delete the old image if it exists    
    //     if ($user->hasMedia('images')) {
    //         $user->clearMediaCollection('images'); // Deletes all media in the 'images' collection
    //     }

    //     if($request->hasFile('image') && $request->file('image')->isValid()){
    //         $user->addMedia($request->file('image'))
    //         ->usingFileName(time() . '.' . $request->file('image')->extension())
    //         ->toMediaCollection('images');
    //     }

    //     $user->name = $request->name;
    //     $user->save();

    //     return Redirect::route('admin.profile')->with('status', 'profile-updated')->with('success','Profile updated successfully.');
    // }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('id', Auth::user()->id)->first();

        // Remove old image if a new one is uploaded
        if ($request->hasFile('image')) {
            $user->clearMediaCollection('images');
            $user->addMedia($request->file('image'))
                ->usingFileName(time() . '.' . $request->file('image')->extension())
                ->toMediaCollection('images');
        }

        $user->name = $request->name;
        $user->save();

        return response()->json(['success' => 'Profile updated successfully!']);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
