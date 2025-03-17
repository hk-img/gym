<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignPlan;
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
            new Middleware(['permission:membership-list|'], only: ['membershipRenewals'])
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
                        return  $row->created_at->format('Y-m-d H:i:s');
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

    // public function getMonthlyRevenue(Request $request)
    // {
    //     $month = $request->input('month', now()->format('Y-m'));

    //     $revenue = AssignPlan::with('plan') // Load related plan
    //         ->whereYear('created_at', substr($month, 0, 4)) // Filter by year
    //         ->whereMonth('created_at', substr($month, 5, 2)) // Filter by month
    //         ->get()
    //         ->sum(function ($assignPlan) {
    //             return $assignPlan->plan ? (float) $assignPlan->plan->price : 0; // Cast to float
    //         });

    //     return response()->json(['revenue' => $revenue]);
    // }

    public function getMonthlyRevenue(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $revenue = AssignPlan::with('plan')
            ->whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2))
            ->get()
            ->sum(function ($assignPlan) {
                $price = $assignPlan->plan ? (float) $assignPlan->plan->price : 0;
                $discount = (float) $assignPlan->discount ?? 0;
                return max(0, $price - $discount);
            });

        return response()->json(['revenue' => $revenue]);
    }




}
