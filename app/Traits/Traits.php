<?php

namespace App\Traits;

use App\Enums\Status;
use App\Models\UsedVehicle;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Mail;

trait Traits
{
    Const CACHE_EXPIRY_TIME = 10;
    
    public function uploadMedia($file, $model, $collection){
        $filename = time() . '.' .  $file->extension();
        $model->addMedia($file)
        ->usingFileName($filename)
        ->toMediaCollection($collection);
    }

    public function sendWelcomeMail($user){
        Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
    }

    // Base query with common conditions
    public function getVehicleBaseQuery($typeId, $location)
    {
        $query = Vehicle::with([
            'brand',
            'variants' => function ($query) use ($location) {
                $query->with(['showRoomPrice' => function ($query) use ($location) {
                    if (isset($location)) {
                        $query->where('city_id', $location);
                    }
                }]);
            }
        ])
            ->active()
            ->where('type_id', $typeId);

        return $query;
    }

    // Used Vehicle Base query with common conditions
    public function getUsedVehicleBaseQuery($typeId, $location)
    {
        $verified = Status::VERIFIED->value;
        $query = UsedVehicle::where('verification_status', $verified)
            ->active()
            ->where('city_id', $location)
            ->where('type_id', $typeId);

        return $query;
    }

    public function ownership($ownership){
        if($ownership == 1){
            $ownership = '1st owner';
        }
        elseif($ownership == 2){
            $ownership = '2nd owner';
        }
        elseif($ownership == 3){
            $ownership = '3rd owner';
        }
        elseif($ownership == 4){
            $ownership = '4th owner';
        }
        elseif($ownership == 5){
            $ownership = '5th owner';
        }
        else{
            $ownership = $ownership . ' owner';
        }
        
        return $ownership;
    }

    public function setTransactions($val){

        $data = new \App\Models\Transaction();

        $data->gym_id = auth()->user()->id;
        $data->user_id = $val['user_id'];
        $data->table_id = $val['table_id'];
        $data->type = $val['type'];
        $data->received_amt = $val['received_amt'];
        $data->balance_amt = $val['balance_amt'];
        $data->total_amt = $val['total_amt'];
        $data->payment_type = $val['payment_type'];
        $data->status = $val['status'];
        $data->save();

        return true;
    }
}
