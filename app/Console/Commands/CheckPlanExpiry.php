<?php

namespace App\Console\Commands;

use App\Models\AssignPlan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckPlanExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-plan-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check expiry date of active plans and update status if expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = now();

        // Get all active plans
        $assignPlans = AssignPlan::where('membership_status', 'active')->get();

        foreach ($assignPlans as $assignPlan) {
            if ($currentDate->greaterThan($assignPlan->end_date)) {
                // Update plan status
                $assignPlan->update(['membership_status' => 'expired']);

                // Update user status if the relationship exists
                if ($assignPlan->user) {
                    $assignPlan->user()->update(['membership_status' => 'expired']);
                }

                // Log for debugging
                $this->info("Plan ID {$assignPlan->id} expired and status updated.");
            }
        }
    }
}
