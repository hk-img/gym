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

                $data = $query->latest()->get();
        
                return DataTables::of($data)
                    ->addIndexColumn() // Adds the iteration column
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
                    })
                    ->addColumn('user_type', function ($row) {
                        return ucfirst($row->user_type);
                    })
                    ->addColumn('member_name', function ($row) {
                        return $row->user->name;
                    })
                    ->addColumn('plan', function ($row) {
                        return $row->plan->name;
                    })
                    // ->addColumn('status', function ($row) {
                    //     $encodedId = base64_encode($row->id);
                    //     $status = $row->status == 1 ? 'success' : 'danger';
                    //     $text = $row->status == 1 ? 'Active' : 'Inactive';
                    //     $changeStatusActiveRoute = route('admin.assign-plan.changeStatus', ['id' => $encodedId, 'status' => '1']);
                    //     $changeStatusInactiveRoute = route('admin.assign-plan.changeStatus', ['id' => $encodedId, 'status' => '2']);

                    //     return '<div class="dropdown action-label">
                    //                 <a href="#" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                    //                     data-bs-toggle="dropdown" aria-expanded="false"><i
                    //                         class="fa-regular fa-circle-dot text-'.$status.'"></i> '.$text.' </a>
                    //                 <div class="dropdown-menu">
                    //                     <a class="dropdown-item" href="'.$changeStatusActiveRoute.'"><i
                    //                             class="fa-regular fa-circle-dot text-success"></i> Active</a>
                    //                     <a class="dropdown-item" href="'.$changeStatusInactiveRoute.'"><i
                    //                             class="fa-regular fa-circle-dot text-danger"></i> Inactive</a>
                    //                 </div>
                    //             </div>';
                    //     })
                    // ->addColumn('action', function ($row) {
                    //     $encodedId = base64_encode($row->id);
                    //     $editRoute = route('admin.assign-plan.edit', $encodedId);
                    //     $deleteRoute = route('admin.assign-plan.destroy', $encodedId);  // Assume the delete route
                    
                    //     // Edit button
                    //     $editButton = auth()->user()->can('user-edit') ? 
                    //         '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';
                    
                    //     // Delete button
                    //     $deleteButton = auth()->user()->can('user-delete') ? 
                    //         "<a href='#' class='dropdown-item' onclick='confirmDelete(\"delete-user-{$row->id}\")'><i class='fa-regular fa-trash-can m-r-5'></i> Delete</a>" : '';
                    
                    //     // Return action buttons with form for deletion
                    //     return '<div class="dropdown dropdown-action">
                    //                 <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                    //                     aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    //                 <div class="dropdown-menu dropdown-menu-right">
                    //                     ' . $editButton . '
                    //                     ' . $deleteButton . '
                    //                 </div>
                    //             </div>
                    //             <form action="' . $deleteRoute . '" method="POST" id="delete-user-' . $row->id . '" style="display: none;">
                    //                 ' . csrf_field() . '
                    //                 ' . method_field('DELETE') . '
                    //             </form>';
                    // })
                    ->rawColumns(['user_type','member_name', 'plan','status', 'action'])
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