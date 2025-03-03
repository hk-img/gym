<?php

namespace App\Services;

// use Twilio\Rest\Client;

class SmsService
{
    // protected $twilio;

    public function __construct()
    {
        // $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    /**
     * Send an SMS.
     *
     * @param string $to
     * @param string $message
     * @return void
     */
    public function sendSms(string $to, string $message): void
    {
        // $this->twilio->messages->create(
        //     $to,
        //     [
        //         'from' => env('TWILIO_PHONE_NUMBER'),
        //         'body' => $message,
        //     ]
        // );
    }
}
