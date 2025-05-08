<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct(protected PushNotificationService $pushNotificationService)
    {
        
    }
    public function sendForm(Request $request)
    {
        return view('admin.pages.notification.send');
    }

    public function sendNotification(Request $request)
    {
        try {
            $request->validate([
                'send_to' => 'required|in:all,specific',
                'user_id.*' => 'nullable|required_if:send_to,specific|exists:users,id',
                'type' => 'required|in:sms,in-app',
                'title' => 'required|string',
                'message' => 'required|string'
            ]);

            $users = ($request->send_to === 'all') ? User::where('added_by', auth()->user()->id)->get() : User::whereIn('id', $request->user_id)->get();

            foreach ($users as $user) {
                $user->notify(new UserNotification($request->title, $request->message, $request->type));

                // Send push notification to each user's device token (if available)
                if ($user->fcm_token) {
                    $this->pushNotificationService->sendPushNotification($request->title, $request->message, $user->fcm_token);
                } else {
                    // Handle the case where a user doesn't have an FCM token (e.g., log a warning)
                    Log::warning('User ' . $user->id . ' does not have an FCM token.');
                }
            }

            return back()->with('success', 'Notification sent successfully!');
        } catch (\Exception $e) {
            Log::error('Notification Error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Failed to send notification. Please try again.');
        }
    }
}
