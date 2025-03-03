<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;

class UpdateUpcomingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-upcoming-command';

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
        $vehicles = Vehicle::where('is_upcoming', 1)->active()->get();
        foreach($vehicles as $vehicle){
            if($vehicle->launch_date == now()->toDateString()){
                $vehicle->update(['is_upcoming' => 2]);
                $this->info($vehicle->vehicle_model.' is updated to latest vehicle');
            }
        }
    }
}
