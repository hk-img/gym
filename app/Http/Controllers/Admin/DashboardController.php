<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
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

        $plan = Plan::where('created_by', auth()->user()->id)->count();

        $data['gym'] = $gym;
        $data['member'] = $member;
        $data['plan'] = $plan;
        return view('admin.pages.dashboard.index',compact('data'));
    }
}
