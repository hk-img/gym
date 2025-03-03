<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
        $data = array();
        $customer = User::whereHas('roles', function($q){
            $q->where('name','Customer');
        })->count();
        $vendor = User::whereHas('roles', function($q){
            $q->where('name','Vendor');
        })->count();
        $data['vendor'] = $vendor;
        $data['customer'] = $customer;
        return view('admin.pages.dashboard.index',compact('data'));
    }
}
