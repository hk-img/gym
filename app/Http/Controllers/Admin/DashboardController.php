<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;
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

        $memberCurrentMonth = User::whereHas('roles', function($q){
            $q->where('name','Member');
        })->where('added_by', auth()->user()->id)->whereMonth('created_at', date('m'))->count();
        
        $trainers = User::whereHas('roles', function($q){
            $q->where('name','Trainer');
        })->where('added_by', auth()->user()->id)->where('salary','>',0)->count();

        $plan = Plan::where('created_by', auth()->user()->id)->count();
        $activity = Activity::where('added_by', auth()->user()->id)->count();
        $transactions = \App\Models\Transaction::where('gym_id', auth()->user()->id)->get();
        // Current month range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Previous month range
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Sum for current month
        $currentMonthPendingBalanceSum = $transactions
            ->where('payment_status', 'Cr')
            ->filter(function ($transaction) use ($currentMonthStart, $currentMonthEnd) {
                return Carbon::parse($transaction->created_at)->between($currentMonthStart, $currentMonthEnd);
            })
            ->sum('balance_amt');

        // Sum for previous month
        $previousMonthPendingBalanceSum = $transactions
            ->where('payment_status', 'Cr')
            ->filter(function ($transaction) use ($previousMonthStart, $previousMonthEnd) {
                return Carbon::parse($transaction->created_at)->between($previousMonthStart, $previousMonthEnd);
            })
            ->sum('balance_amt');
            
        $currentMonthEarning = $transactions
            ->where('payment_status', 'Cr')
            ->filter(function ($transaction) use ($currentMonthStart, $currentMonthEnd) {
                return Carbon::parse($transaction->created_at)->between($currentMonthStart, $currentMonthEnd);
            })
            ->sum('received_amt');

        // Sum for previous month
        $previousMonthEarning = $transactions
            ->where('payment_status', 'Cr')
            ->filter(function ($transaction) use ($previousMonthStart, $previousMonthEnd) {
                return Carbon::parse($transaction->created_at)->between($previousMonthStart, $previousMonthEnd);
            })
            ->sum('received_amt');

        $query = User::query()
            ->where('added_by', auth()->user()->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Member');
            })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);

        $MembersList = $query->with('media')->latest()->excludeSuperAdmin()->get();


        $data['gym'] = $gym;
        $data['member'] = $member;
        $data['plan'] = $plan;
        $data['trainers'] = $trainers;
        $data['activity'] = $activity;
        $data['member_current_month'] = $memberCurrentMonth;
        $data['current_month_income'] = $currentMonthPendingBalanceSum;
        $data['previous_month_income'] = $previousMonthPendingBalanceSum;
        $data['previous_month'] = $previousMonthEarning;
        $data['current_month'] = $currentMonthEarning;
        $data['members_list'] = $MembersList;
        return view('admin.pages.dashboard.index',compact('data'));
    }
}
