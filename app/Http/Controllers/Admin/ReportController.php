<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
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
                if ($request->date_range) {
                    $dates = explode(' - ', $request->date_range);
                    // $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    // $endDate = \Carbon\Carbon::parse($dates[1])->endOfDay();
                    $startDate = \Carbon\Carbon::parse($dates[0]);
                    $endDate = \Carbon\Carbon::parse($dates[1]);

                    $query->whereBetween('end_date', [$startDate, $endDate]);
                }

                $data = $query->latest()->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('member_name', function($row){
                        return $row->user->name .' '.'('.($row->user->country_code ?? '+91').' '.$row->user->phone.')' ?? 'N/A';
                    }) 
                    ->addColumn('plan', function($row) { 
                        return $row->plan->name ?? 'N/A';
                    })
                    ->addColumn('start_date', function($row) {
                        return $row->start_date;
                    })
                    ->addColumn('end_date_formatted', function($row){
                        return  $row->end_date;
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

}
