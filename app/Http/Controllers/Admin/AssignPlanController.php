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
class AssignPlanController extends Controller
{
    use Traits;

    // public static function middleware(): array
    // {
    //     return [
    //         'auth',
    //         new Middleware(['permission:user-list|user-create|user-edit|user-delete'], only: ['index']),
    //         new Middleware(['permission:user-create'], only: ['create', 'store']),
    //         new Middleware(['permission:user-edit'], only: ['edit', 'update']),
    //         new Middleware(['permission:user-delete'], only: ['destroy']),
    //     ];
    // }
   
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {

                $query = AssignPlan::query();

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
                        return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
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

    public function create()
    {
        try {
            return view('admin.pages.assign_plan.create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.assign-plan.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'user_type' => 'required|in:new,old',
            'payment_method' => 'required|in:online,offline',
                'utr' => [
                'required_if:payment_method,online',
                'nullable',
                Rule::unique('assign_plans', 'utr')->ignore(null),
            ],

            'discount' => 'required|numeric',

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
            
            $assignPlan = AssignPlan::create($input);
            $assignPlan->user()->update(['start_date' => $startDate, 'end_date' => $endDate]);

            DB::commit();
        
            return redirect()->route('admin.assign-plan.index')->with('success', 'Plan assigned successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.assign-plan.index')
            ->with('error', $e->getMessage());
        }
    }
    
    public function show($id)
    {
        try {
            $user = User::find($id);
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