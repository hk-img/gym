<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\User;
use \App\Models\AssignPT;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use App\Traits\Traits;

class AssignPTController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use Traits;
    public function index(Request $request)
    {
        try {

            if ($request->ajax()) {

                $query = AssignPT::query();
                $query->whereHas('user', function ($q) {
                    $q->where('added_by', auth()->user()->id);
                });
                // dd($query);
                // Apply date range filter if provided
                if ($request->date_range) {
                    $dates = explode(' - ', $request->date_range);
                    
                    // Ensure date parsing is correct
                    $startDate = \Carbon\Carbon::parse(trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::parse(trim($dates[1]))->endOfDay();
                
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }

                // Filter by user type (new or old)
                if ($request->user_type) {
                    $query->where('user_type', $request->user_type);    
                }

                // Filter by payment method (online or offline)
                if ($request->payment_method) {
                    $query->where('payment_method', $request->payment_method);    
                }

                // Filter by membership status
                if ($request->membership_status) {
                    $query->where('membership_status', $request->membership_status);    
                }

                $data = $query->latest()->get();
        
                return DataTables::of($data)
                    ->addIndexColumn() // Adds the iteration column
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('d D m, Y h:i:s');
                    })
                    ->addColumn('user_type', function ($row) {
                        $status = $row->user_type == 'new' ? 'success' : 'danger';
                        $text = $row->user_type;
            
                        return '<div class="action-label">
                                    <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                        <i class="fa-regular fa-circle-dot text-'.$status.'"></i> '.$text.'
                                    </a>
                                </div>';
                    })
                    ->addColumn('member_name', function ($row) {
                        return $row->user->name .' '.'('.($row->user->country_code ?? '+91').' '.$row->user->phone.')' ?? 'N/A';
                    })
                    ->addColumn('trainer', function ($row) {
                        return $row->trainer->name;
                    })
                    ->addColumn('start_date', function ($row) {
                        return Carbon::parse($row->start_date)->format('d M Y'); // Example: 03 Mar 2025
                    })
                    ->addColumn('end_date', function ($row) {
                        return Carbon::parse($row->end_date)->format('d M Y');
                    })
                    ->addColumn('payment_method', function ($row) {
                        $status = $row->payment_method == 'online' ? 'success' : 'danger';
                        $text = $row->payment_method;
            
                        return '<div class="action-label">
                                    <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                        <i class="fa-regular fa-circle-dot text-'.$status.'"></i> '.$text.'
                                    </a>
                                </div>';
                    })
                    ->addColumn('utr', function ($row) {            
                        return $row->utr ?? 'N/A';
                    })
                    ->rawColumns(['user_type','member_name', 'plan','status','payment_method'])
                    ->make(true);
            }
            return view('admin.pages.assign_pt.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {

            // $user = User::where('added_by',auth()->user()->id)->first();
            return view('admin.pages.assign_pt.create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.assign-pt.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'trainer_id' => 'required|exists:users,id',
            'months' => 'required',
            'payment_type' => 'required|in:full,partial',
            'payment_method' => 'required|in:online,offline',
                'utr' => [
                'required_if:payment_method,online',
                'nullable',
                Rule::unique('assign_p_t_s', 'utr')->ignore(null),
            ],
            'received_amt' => [
                'required_if:payment_type,partial',
                'nullable',
            ],

            'discount' => 'nullable|numeric',

        ]);

        DB::beginTransaction();
        // try {

            $user = User::where('id',$request->user_id)->where('membership_status','pending')->first();
            
            if($user){
                return redirect()->route('admin.assign-pt.create')->with('error', 'Membership expired successfully.');
            }

            $input = $request->all();
            $check = AssignPT::where('user_id',$request->user_id)->first();

            if($check){
                $user_type = 'old';
            }
            else{
                $user_type = 'new';
            }
            
           // Get the selected plan
            $trainer = User::findOrFail($validated['trainer_id']);
            $months = (int) $request->months;

            if($trainer && $request->payment_type == "partial"){
                
                if($request->received_amt > ($trainer->pt_fees * $months)){
                    return redirect()->back()->with('error', 'Received amount cannot be greater than PT price');
                }
            }
            
            $startDate = Carbon::parse($request->start_date);

            $endDate = $startDate->copy()->addMonths($months);

            $input['months'] = $months;
            $input['start_date'] = $startDate;
            $input['end_date'] = $endDate;
            $input['user_type'] = $user_type;
            $input['received_amt'] = $request->received_amt ?? (($trainer->pt_fees * $months) -$request->discount);

            $assignPlan = AssignPT::create($input);
            $assignPlan->user()->update(['pt_start_date' => $startDate, 'pt_end_date' => $endDate]);

            $dataArray = [
                'gym_id'=> auth()->user()->id,
                'user_id' => $assignPlan->user_id,
                'table_id' => $assignPlan->id,
                'type' => 'assign_pt',
                'received_amt' => $assignPlan->received_amt,
                'balance_amt' => (($trainer->pt_fees * $request->months)-$request->discount)  - $assignPlan->received_amt,
                'total_amt' => ($trainer->pt_fees * $request->months)-$request->discount,
                'payment_type' => $assignPlan->payment_type,
                'status' =>'cleared',
                'payment_status' => 'Cr',
            ];

            $this->setTransactions($dataArray);

            $dataArray = [
                'gym_id'=> auth()->user()->id,
                'user_id' => $assignPlan->user_id,
                'table_id' => $assignPlan->id,
                'type' => 'assign_pt',
                'received_amt' => $assignPlan->received_amt,
                'balance_amt' => (($trainer->pt_fees * $request->months)-$request->discount)  - $assignPlan->received_amt,
                'total_amt' => ($trainer->pt_fees * $request->months)-$request->discount,
                'payment_type' => $assignPlan->payment_type,
                'status' =>'cleared',
                'payment_status' => 'Dr',
            ];

            $this->setTransactions($dataArray);
            $this->setClosingAmt($assignPlan->user_id, auth()->user()->id);
            DB::commit();
        
            return redirect()->route('admin.assign-pt.index')->with('success', 'PT assigned successfully.');

        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     Log::error($e->getMessage());
        //     return redirect()->route('admin.assign-pt.index')
        //         ->with('error', 'Something went wrong');
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function trainerInfo(Request $request, $id)
    {
        try {

            $trainer = User::where('added_by', $id)->first();

            return response()->json([
                'name' => $trainer->name,
                'price' => '₹ '.$trainer->pt_fees,
            ]);

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong!']);
        }
    }
}
