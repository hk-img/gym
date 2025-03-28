<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPlanExpirationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-plan-expiration-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysBeforeExpiration = 7;
        $today = Carbon::now();
        $reminderDate = $today->addDays($daysBeforeExpiration)->toDateString();

        $users = User::whereNotNull('end_date')->whereDate('end_date', $reminderDate)->get();

        foreach($users as $user){
            try{
                $this->info("Reminder send to {$user->phone}");
            }catch(\Exception $e){
                $this->error("Failed to send remainder to {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info('Plan expiration reminders sent.');
    }
}
