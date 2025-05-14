<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignPlan;
use App\Models\AssignPT;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
        'auth',
            new Middleware(['permission:membership-renewal'], only: ['membershipRenewals']),
            new Middleware(['permission:membership-expired'], only: ['membershipExpired'])
        ];
    }

    /**
     * Membership Renewals
     */
    public function membershipRenewals(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = AssignPlan::with(['user', 'plan']);
                    // ->where(function ($q) {
                    //     $today = now();
                    //     $nextWeek = $today->copy()->addDays(7);

                    //     $q->whereBetween('end_date', [$today, $nextWeek]) // Expiring soon
                    //     ->orWhere('end_date', '<', $today); // Already expired
                    // });

                // Apply date range filter if provided
                if ($request->month) {
                    $month = $request->input('month');
                    $query->whereYear('created_at', substr($month, 0, 4))
                    ->whereMonth('created_at', substr($month, 5, 2));
                }
                
                $query->whereHas('user', function($q){
                    $q->where('added_by', auth()->user()->id);
                });

                $data = $query->latest()->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('member_name', function($row){
                        return $row->user->name .' '.'('.($row->user->country_code ?? '+91').' '.$row->user->phone.')' ?? 'N/A';
                    }) 
                    ->addColumn('plan', function($row) { 
                        return $row->plan->name.' ('.$row->plan->duration.' Days'.')' ?? 'N/A';
                    })
                    ->addColumn('price', function($row) { 
                        return '₹ '.number_format($row->plan->price) ?? 'N/A';
                    })

                    ->addColumn('netamount', function($row) { 
                        $price = $row->plan->price ?? 0;
                        $discount = $row->discount ?? 0;
                        $netAmount = max(0, $price - $discount);
                        return '₹ '.number_format($netAmount);
                    })

                    ->addColumn('start_date', function ($row) {
                        return Carbon::parse($row->start_date)->format('d M Y'); // Example: 03 Mar 2025
                    })
                    ->addColumn('end_date_formatted', function ($row) {
                        return Carbon::parse($row->end_date)->format('d M Y');
                    })
                    ->addColumn('days_remaining', function ($row) {
                        $remaining = (int) now()->diffInDays($row->end_date, false);
                        return $remaining > 0 ? "$remaining Days Left" : "Expired";
                    })
                    ->addColumn('status', function ($row) {
                        $statusClass = now()->greaterThan($row->end_date) ? 'danger' : 'success';
                        $status = now()->greaterThan($row->end_date) ? 'Expired' : 'Active';
            
                        return '<div class="action-label">
                                    <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                        <i class="fa-regular fa-circle-dot text-'.$statusClass.'"></i> '.$status.'
                                    </a>
                                </div>';
                    })
                    ->addColumn('created_at_formatted', function($row){
                        return \Carbon\Carbon::parse($row->created_at)->format('d D m, Y h:i:s');
                    })
                    ->rawColumns(['member_name','plan','start_date','end_date_formatted','days_remaining','status','created_at_formatted'])
                    ->make(true);
            }

            return view('admin.pages.reports.membership_renewal');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something went wrong');
        }
    }

    /**
     * Membership Expired
     */
    public function membershipExpired(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Subquery to get the latest plan ID for each user
                $latestPlanSubquery = AssignPlan::selectRaw('MAX(id) as id')
                ->whereHas('user', function ($q) {
                    $q->where('added_by', auth()->user()->id);
                })
                ->when($request->month, function ($query) use ($request) {
                    $month = $request->input('month');
                    $query->whereYear('created_at', substr($month, 0, 4))
                        ->whereMonth('created_at', substr($month, 5, 2));
                })
                ->groupBy('user_id');

                // Fetch full details for the latest record per user, but EXCLUDE active members
                $data = AssignPlan::whereIn('id', $latestPlanSubquery)
                ->with(['user', 'plan'])
                ->where('membership_status', '!=', 'active') // Exclude users with active membership
                ->orderBy('end_date', 'DESC')
                ->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('member_name', function($row){
                        return $row->user->name .' '.'('.($row->user->country_code ?? '+91').' '.$row->user->phone.')' ?? 'N/A';
                    }) 
                    ->addColumn('plan', function($row) { 
                        return $row->plan->name.' ('.$row->plan->duration.' Days'.')' ?? 'N/A';
                    })
                    ->addColumn('price', function($row) { 
                        return '₹ '.number_format($row->plan->price) ?? 'N/A';
                    })
                    ->addColumn('netamount', function($row) { 
                        $price = $row->plan->price ?? 0;
                        $discount = $row->discount ?? 0;
                        $netAmount = max(0, $price - $discount);
                        return '₹ '.number_format($netAmount);
                    })
                    ->addColumn('start_date', function ($row) {
                        return Carbon::parse($row->start_date)->format('d M Y'); 
                    })
                    ->addColumn('end_date_formatted', function ($row) {
                        return Carbon::parse($row->end_date)->format('d M Y');
                    })
                    ->addColumn('days_remaining', function ($row) {
                        $remaining = (int) now()->diffInDays($row->end_date, false);
                        return $remaining > 0 ? "$remaining Days Left" : "Expired";
                    })
                    ->addColumn('status', function ($row) {
                        $statusClass = now()->greaterThan($row->end_date) ? 'danger' : 'success';
                        $status = now()->greaterThan($row->end_date) ? 'Expired' : 'Active';

                        return '<div class="action-label">
                                    <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                        <i class="fa-regular fa-circle-dot text-'.$statusClass.'"></i> '.$status.'
                                    </a>
                                </div>';
                    })
                    ->addColumn('created_at_formatted', function($row){
                        return  \Carbon\Carbon::parse($row->created_at)->format('d D m, Y h:i:s');
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $planRoute = route('admin.assign-plan.create', $encodedId);

                        $planButton = ($row->membership_status == 'Pending' || $row->membership_status == 'Expired') ?
                            '<a href="javascript:void(0);" class="dropdown-item assign-plan-btn" data-user-id="' . base64_encode($row->user_id) . '">
                                            <i class="fa-solid fa-user-plus m-r-5"></i> Renew Plan
                                        </a>'
                            : '';

                        return '<div class="dropdown dropdown-action">
                                    <a href="javacript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $planButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['member_name','plan','start_date','end_date_formatted','days_remaining','status','created_at_formatted','action'])
                    ->make(true);
            }

            return view('admin.pages.reports.membership_expired');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something went wrong');
        }
    }

     /**
     * Calculating monthly revenue of gym
     */
    public function getMonthlyRevenue(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $revenue = AssignPlan::with('plan')
            ->whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2))
            ->whereHas('user', function($q){
                $q->where('added_by', auth()->user()->id);
            })
            ->get()
            ->sum(function ($assignPlan) {
                $price = $assignPlan->plan ? (float) $assignPlan->plan->price : 0;
                $discount = (float) $assignPlan->discount ?? 0;
                return max(0, $price - $discount);
            });

        return response()->json(['revenue' => $revenue]);
    }

    public function getMonthlyPTRevenue(Request $request)
    {
        $month = $request->input('month') ?? now()->format('Y-m');
        $trainerId = $request->input('trainerId');
        $userId = $request->input('user_id');

        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $assignments = AssignPT::with('trainer')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->when($trainerId, function ($query) use ($trainerId) {
                $query->where('trainer_id', $trainerId);
            })
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('user', function ($q) {
                $q->where('added_by', auth()->user()->id);
            })
            ->get();

        $revenue = $assignments->sum(function ($assignPt) {
            $price = ($assignPt->trainer->pt_fees ?? 0) * ($assignPt->months ?? 1);
            $discount = $assignPt->discount ?? 0;
            return max(0, $price - $discount);
        });

        return response()->json(['revenue' => round($revenue, 2)]);
    }



    public function personalTraining(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = AssignPT::with(['user', 'trainer']);
                    // ->where(function ($q) {
                    //     $today = now();
                    //     $nextWeek = $today->copy()->addDays(7);

                    //     $q->whereBetween('end_date', [$today, $nextWeek]) // Expiring soon
                    //     ->orWhere('end_date', '<', $today); // Already expired
                    // });

                // Apply date range filter if provided
                if ($request->month) {
                    $month = $request->input('month');
                    $query->whereYear('created_at', substr($month, 0, 4))
                    ->whereMonth('created_at', substr($month, 5, 2));
                }
                
                if ($request->trainerId) {
                    $trainerId = $request->input('trainerId');
                    $query->where('trainer_id', $trainerId);
                }
                
                if ($request->user_id) {
                    $userId = $request->input('user_id');
                    $query->where('user_id', $userId);
                }

                if($request->status){
                    $status = $request->input('status');

                    if($request->status == 'Active'){
                        $query->where('end_date', '>', now());

                    }else if($request->status == 'Expired'){
                        $query->where('end_date', '<', now());
                    }
                }
                
                $query->whereHas('user', function($q){
                    $q->where('added_by', auth()->user()->id);
                });

                $data = $query->latest()->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('member_name', function($row){
                        return $row->user->name .' '.'('.($row->user->country_code ?? '+91').' '.$row->user->phone.')' ?? 'N/A';
                    }) 
                    ->addColumn('trainer_name', function($row) { 
                        return $row->trainer->name.' ('.$row->months.' Months'.')' ?? 'N/A';
                    })
                    ->addColumn('price', function($row) { 
                        $price = ($row->trainer->pt_fees ?? 0) * ($row->months ?? 1);
                        return '₹ ' . number_format($price);
                    })
                     ->addColumn('discount', function($row) { 
                        return '₹ '.number_format($row->discount) ?? 'N/A';
                    })

                    ->addColumn('netamount', function($row) { 
                        $price = $row->trainer->pt_fees * $row->months?? 0;
                        $discount = $row->discount ?? 0;
                        $netAmount = max(0, $price - $discount);
                        return '₹ '.number_format($netAmount);
                    })

                    ->addColumn('start_date', function ($row) {
                        return Carbon::parse($row->start_date)->format('d M Y'); // Example: 03 Mar 2025
                    })
                    ->addColumn('end_date_formatted', function ($row) {
                        return Carbon::parse($row->end_date)->format('d M Y');
                    })
                    ->addColumn('days_remaining', function ($row) {
                        $remaining = (int) now()->diffInDays($row->end_date, false);
                        return $remaining > 0 ? "$remaining Days Left" : "Expired";
                    })
                    ->addColumn('status', function ($row) {
                        $statusClass = now()->greaterThan($row->end_date) ? 'danger' : 'success';
                        $status = now()->greaterThan($row->end_date) ? 'Expired' : 'Active';
            
                        return '<div class="action-label">
                                    <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                        <i class="fa-regular fa-circle-dot text-'.$statusClass.'"></i> '.$status.'
                                    </a>
                                </div>';
                    })
                    ->addColumn('created_at_formatted', function($row){
                        return  \Carbon\Carbon::parse($row->created_at)->format('d D m, Y h:i:s');
                    })
                    ->rawColumns(['member_name','trainer_name','price','netamount','start_date','end_date_formatted','days_remaining','status','created_at_formatted'])
                    ->make(true);
            }

            $trainer = \App\Models\User::where('added_by', auth()->user()->id)->where('salary','>',0)->where('status','1')->select('id','name')->get();
            $users = \App\Models\User::where('added_by', auth()->user()->id)->where('status','1')->select('id','name')->get();
            return view('admin.pages.reports.personal_training',compact('trainer','users'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something went wrong');
        }
    }
}
