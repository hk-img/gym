<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
        $data = array();
        $gym = User::whereHas('roles', function($q){
            $q->where('name','Gym');
        })->count();

        $member = User::whereHas('roles', function($q){
            $q->where('name','Member');
        })->where('added_by', auth()->user()->id)->count();
        
        $trainers = User::whereHas('roles', function($q){
            $q->where('name','Trainer');
        })->where('added_by', auth()->user()->id)->where('salary','>',0)->count();

        $plan = Plan::where('created_by', auth()->user()->id)->count();
        $activity = Activity::where('added_by', auth()->user()->id)->count();

        $data['gym'] = $gym;
        $data['member'] = $member;
        $data['plan'] = $plan;
        $data['trainers'] = $trainers;
        $data['activity'] = $activity;
        return view('admin.pages.dashboard.index',compact('data'));
    }
}
