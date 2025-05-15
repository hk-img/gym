<?php

namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AssignPlan;
use App\Models\Plan;
use App\Models\User;
use App\Traits\Traits;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use PhpParser\Node\Expr\Assign;
use Throwable;
use Yajra\DataTables\Facades\DataTables;


// class AssignPlanController extends Controller implements HasMiddleware
class AssignPlanController extends Controller implements HasMiddleware
{
    use Traits;

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:assign-plan-list|assign-plan-create|assign-plan-edit|assign-plan-delete'], only: ['index']),
            new Middleware(['permission:assign-plan-create'], only: ['create', 'store']),
            new Middleware(['permission:assign-plan-edit'], only: ['edit', 'update']),
            new Middleware(['permission:assign-plan-delete'], only: ['destroy']),
        ];
    }
   
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {

                $query = AssignPlan::query();
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
                    ->addColumn('plan', function ($row) {
                        return $row->plan->name;
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
                    ->addColumn('membership_status', function ($row) {
                            $statusClass = $row->membership_status == 'Pending' ? 'primary' : ($row->membership_status == 'Active' ? 'success' :($row->membership_status == 'Expired' ? 'danger' :''));
                            $status = $row->membership_status;
                            $returnData = '<div class="action-label">
                                            <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                                <i class="fa-regular fa-circle-dot text-'.$statusClass.'"></i> '.$status.'
                                            </a>
                                        </div>';
                        
            
                        return $returnData;
                    })
                    ->rawColumns(['user_type','member_name', 'plan','status','payment_method','membership_status'])
                    ->make(true);
            }
            return view('admin.pages.assign_plan.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
            ->with('error', 'Something went wrong');
        }
    }

    // public function create()
    // {
    //     try {
    //         return view('admin.pages.assign_plan.create');
    //     } catch (\Throwable $e) {
    //         Log::error($e->getMessage());
    //         return redirect()->route('admin.assign-plan.index')
    //         ->with('error', 'Something went wrong');
    //     }
    // }

    public function create(Request $request)
    {
        try {
            $id = base64_decode($request->user);
            $user = User::find($id);
            return view('admin.pages.assign_plan.create', compact('user')); // Pass user data to the view
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.assign-plan.index')->with('error', 'Something went wrong');
        }
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'plan_id'        => 'required|exists:plans,id',
            'payment_type'   => 'required|in:full,partial',
            'payment_method' => 'required|in:online,offline',
            'utr'            => [
                'required_if:payment_method,online',
                'nullable',
                Rule::unique('assign_plans', 'utr')->ignore(null),
            ],
            'received_amt'   => 'required_if:payment_type,partial|nullable|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $userId         = $validated['user_id'];
            $planId         = $validated['plan_id'];
            $paymentType    = $validated['payment_type'];
            $paymentMethod  = $validated['payment_method'];
            $utr            = $validated['utr'] ?? null;
            $discount       = $validated['discount'] ?? 0;

            $isOldUser = AssignPlan::where('user_id', $userId)->exists();
            $userType  = $isOldUser ? 'old' : 'new';

            $plan       = Plan::findOrFail($planId);
            $planPrice  = $plan->price;

            if ($paymentType === 'partial' && ($planPrice*$plan->duration) < $discount) {
                return response()->json([
                    'message' => 'Discount amount cannot exceed the plan price.'
                ], 422);    
            }
            
            $duration   = intval($plan->duration);
            $totalAmt   = $planPrice - $discount;
            $receivedAmt = $request->filled('received_amt') ? $request->received_amt : $totalAmt;

            if ($paymentType === 'partial' && $receivedAmt > $totalAmt) {
                return response()->json([
                    'message' => 'Received amount cannot exceed the total payable amount.'
                ], 422);    
            }

            $startDate = now();
            $endDate   = $startDate->copy()->addDays($duration);

            $assignPlan = AssignPlan::create([
                'user_id'        => $userId,
                'plan_id'        => $planId,
                'payment_type'   => $paymentType,
                'payment_method' => $paymentMethod,
                'utr'            => $utr,
                'received_amt'   => $receivedAmt,
                'discount'       => $discount,
                'days'           => $duration,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'user_type'      => $userType,
            ]);

            optional($assignPlan->user())->update([
                'start_date'        => $startDate,
                'end_date'          => $endDate,
                'membership_status' => 'active',
            ]);

            $transactionBase = [
                'gym_id'       => auth()->id(),
                'user_id'      => $userId,
                'table_id'     => $assignPlan->id,
                'type'         => 'assign_plan',
                'received_amt' => $receivedAmt,
                'balance_amt'  => $totalAmt - $receivedAmt,
                'total_amt'    => $totalAmt,
                'payment_type' => $paymentType,
                'status'       => 'cleared',
            ];

            foreach (['Cr', 'Dr'] as $status) {
                $this->setTransactions([...$transactionBase, 'payment_status' => $status]);
            }

            $this->setClosingAmt($userId, auth()->id());

            DB::commit();

            return response()->json([
                    'message' => 'Plan assigned successfully.',
                    "url"=>url('admin/assign-plan')
            ], 200);    

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Plan assignment failed: ' . $e->getMessage());
            return redirect()->route('admin.assign-plan.index')->with('error', 'An error occurred. Please try again.');
        }
    }
    
    public function show($id)
    {
        try {
            $user = User::find($id);
            // $user->whereHas('user', function ($q) {
            //     $q->where('added_by', auth()->user()->id);
            // });
            return view('admin.pages.users.show',compact('user'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.users.index')
            ->with('error', 'Something went wrong');
        }
    }
    
    public function edit($id)
    {
        try {
            $id = base64_decode($id);
            $data = AssignPlan::findOrFail($id);
        
            return view('admin.pages.assign_plan.edit',compact('data'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.assign-plan.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'user_type' => 'required|in:new,old',
            'payment_method' => 'required|in:online,offline',
            'utr' => 'required_if:payment_method,online',
        ]);
        DB::beginTransaction();
        try {        
            $input = $request->all();

           // Get the selected plan
            $plan = Plan::findOrFail($validated['plan_id']);
            $days = intval($plan->duration);

            // Calculate start_date and end_date
            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addDays($days);

            $input['days'] = $days;
            $input['start_date'] = $startDate;
            $input['end_date'] = $endDate;
            
            $assignPlan = AssignPlan::findOrFail($id);
            $assignPlan->update($input);

            DB::commit();
        
            return redirect()->route('admin.assign-plan.index')->with('success', 'Assigned info updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.assign-plan.index')
            ->with('error', 'Something went wrong');
        }
    }
    
    public function destroy($id)
    {
        try {
            $id = base64_decode($id);
            User::excludeSuperAdmin()->findOrFail($id)->delete();
            
            return redirect()->route('admin.users.index')->with('success', 'Member deleted successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Something went wrong');
        }
    }
    
    public function changeStatus($id,$status)
    {
        try {
            // Validate the status to ensure it's either 1 or 2
            if (!in_array($status, [1, 2])) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Invalid status value. Status must be 1 or 2.');
            }

            // Find the vehicle and update its status
            $id = base64_decode($id);
            $user = User::excludeSuperAdmin()->findOrFail($id);
            $user->status = $status;
            $user->save();
            
            return redirect()->route('admin.users.index')->with('success', 'Status changed successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Something went wrong');
        }
    }
}