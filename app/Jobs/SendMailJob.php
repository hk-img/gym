<?php

namespace App\Jobs;

use App\Mail\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Queueable;

    public $detail;

    /**
     * Create a new job instance.
     */
    public function __construct($detail)
    {
        $this->detail = $detail;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->detail['email'])->send(new SendMail($this->detail));
    }
}
