<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Models\GymSocialLinks;
use App\Models\GymWorkingHour;
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
        $workingHours = GymWorkingHour::where('gym_id', auth()->user()->id)->get();
        $socialLinks = GymSocialLinks::where('gym_id', auth()->user()->id)->first();
        return view('admin.pages.profile.edit', [
            'user' => $user,
            'workingHours' => $workingHours,
            'socialLinks' => $socialLinks,
        ]);
    }

    /**
     * Update Profile
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
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
        $user->address = $request->address;
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

    /**
     * Update Gym Working Hours
     */
    public function updateGymHours(Request $request)
    {
        $request->validate([
            'working_hours' => 'required|array',
            'working_hours.*.open_time' => 'required_without:working_hours.*.is_closed|date_format:H:i',
            'working_hours.*.close_time' => 'required_without:working_hours.*.is_closed|date_format:H:i|after:working_hours.*.open_time',
        ], [
            'working_hours.*.open_time.required_without' => 'The opening time is required unless the gym is marked as closed.',
            'working_hours.*.close_time.required_without' => 'The closing time is required unless the gym is marked as closed.',
            'working_hours.*.close_time.after' => 'The closing time must be after the opening time.',
        ]);

        $gymId = auth()->user()->id;
        foreach ($request->working_hours as $day => $data) {
            GymWorkingHour::updateOrCreate(
                ['day' => $day, 'gym_id' => $gymId],
                [
                    'open_time' => $data['open_time'] ?? null,
                    'close_time' => $data['close_time'] ?? null,
                    'is_closed' => isset($data['is_closed']) ? true : false,
                ]
            );
        }

        return redirect()->back()->with('success', 'Gym working hours updated successfully!');
    }

    /**
     * Update Gym Working Hours
     */
    public function updateSocialLinks(Request $request)
    {
        $request->validate([
            'facebook'  => 'nullable|url',
            'twitter'   => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin'  => 'nullable|url',
            'youtube'   => 'nullable|url',
        ]);

        // Get the gym_id of the logged-in admin
        $gymId = Auth::user()->id;

        // Update or create social links for the gym
        GymSocialLinks::updateOrCreate(
            ['gym_id' => $gymId], 
            $request->only(['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'])
        );

        return back()->with('success', 'Social links updated successfully!');
    }
}
