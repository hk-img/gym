<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private function getFireBaseAccessToken()
    {
        try {
            $filepath = public_path(env('GOOGLE_CREDENTIALS_PATH'));
            $scopeUrl = env('FIREBASE_SCOPE_MESSAGE_URL');
            $client = new GoogleClient();
            $client->setAuthConfig($filepath);
            $client->addScope($scopeUrl);
            $response = $client->fetchAccessTokenWithAssertion();
            return $response['access_token'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error getting Firebase Access Token: ' . $e->getMessage());
            return false;
        }
    }

    // public function sendPushNotification(string $title, string $message, ?string $deviceToken = null)
    // {
    //     try {
    //         $token = $this->getFireBaseAccessToken();
    //         if (!$token) {
    //             Log::error('Firebase Access Token Error: Could not retrieve access token.');
    //             return;
    //         }
    //         $URL = env('FIREBASE_URL');
    //         $extraData = json_encode([]);
    //         $post_data = [
    //             "message" => [
    //                 "data" => [
    //                     "click_action" => "FLUTTER_NOTIFICATION_CLICK",
    //                     "status" => "done",
    //                     "extra_data" => $extraData,
    //                     "sound" => "notification_alert.mp3",
    //                     "vibrate" => "300",
    //                     "priority" => "high",
    //                 ],
    //                 "notification" => [
    //                     "title" => $title,
    //                     "body" => $message,
    //                 ],
    //             ]
    //         ];

    //         // Target a specific device if a token is provided
    //         if ($deviceToken) {
    //             $post_data['message']['token'] = $deviceToken;
    //             unset($post_data['message']['topic']); // Remove topic if targeting a specific device
    //         } else {
    //             // Fallback to topic if no device token is provided (you might want to handle this differently)
    //             $post_data['message']['topic'] = '12300';
    //         }

    //         $data = json_encode($post_data);
    //         $crl = curl_init();

    //         $headr = array();
    //         $headr[] = 'Content-type: application/json';
    //         $headr[] = 'Authorization: Bearer ' . $token;
    //         curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
    //         curl_setopt($crl, CURLOPT_URL, $URL);
    //         curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
    //         curl_setopt($crl, CURLOPT_POST, true);
    //         curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
    //         curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

    //         $rest = curl_exec($crl);
    //         if (curl_errno($crl)) {
    //             Log::error('Firebase Push Notification Error (cURL): ' . curl_error($crl));
    //         } else {
    //             // Optional: Log the Firebase response
    //             Log::info('Firebase Push Notification Response: ' . $rest);
    //         }
    //         curl_close($crl);

    //     } catch (\Exception $e) {
    //         Log::error('Firebase Push Notification Error: ' . $e->getMessage());
    //     }
    // }

    public function sendPushNotification(string $title, string $message, ?string $deviceToken = null)
    {
        try {
            $token = $this->getFireBaseAccessToken();
            if (!$token) {
                Log::error('Firebase Access Token Error: Could not retrieve access token.');
                return;
            }
            $URL = env('FIREBASE_URL');
            $extraData = json_encode([]);
            $post_data = [
                "message" => [
                    "data" => [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "status" => "done",
                        "extra_data" => $extraData,
                        "sound" => "notification_alert.mp3",
                        "vibrate" => "300",
                        "priority" => "high",
                    ],
                    "notification" => [
                        "title" => $title,
                        "body" => $message,
                    ],
                ]
            ];

            // Target a specific device if a token is provided
            if ($deviceToken) {
                $post_data['message']['token'] = $deviceToken;
                unset($post_data['message']['topic']); // Remove topic if targeting a specific device
            } else {
                // Fallback to topic if no device token is provided (you might want to handle this differently)
                $post_data['message']['topic'] = '12300';
            }

            $response = Http::acceptJson()->withToken($token)->withOptions([
                'verify' => false,
            ])->post($URL,$post_data);

            $body = (string) $response->getBody();

            Log::info('Firebase Push Notification Response: ' . $body);

        } catch (\Exception $e) {
            Log::error('Firebase Push Notification Error: ' . $e->getMessage());
        }
    }
}