<?php

namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Plan;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\Traits;

class PlanController extends Controller implements HasMiddleware
{
    use Traits;

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:brand-list|brand-create|brand-edit|brand-delete'], only: ['index']),
            new Middleware(['permission:brand-create'], only: ['create', 'store']),
            new Middleware(['permission:brand-edit'], only: ['edit', 'update']),
            new Middleware(['permission:brand-delete'], only: ['destroy']),
        ];
    }
   
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {

                $query = Plan::query();

                $data = $query->latest()->get();
        
                return DataTables::of($data)
                    ->addIndexColumn() // Adds the iteration column
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
                    })
                    ->addColumn('price', function($row) { 
                        return '₹ '.$row->price;
                    })
                    ->addColumn('status', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $status = $row->status == 1 ? 'success' : 'danger';
                        $text = $row->status == 1 ? 'Active' : 'Inactive';
                        $changeStatusActiveRoute = route('admin.plan.changeStatus', ['id' => $encodedId, 'status' => '1']);
                        $changeStatusInactiveRoute = route('admin.plan.changeStatus', ['id' => $encodedId, 'status' => '2']);

                        return '<div class="dropdown action-label">
                                    <a href="#" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false"><i
                                            class="fa-regular fa-circle-dot text-'.$status.'"></i> '.$text.' </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="'.$changeStatusActiveRoute.'"><i
                                                class="fa-regular fa-circle-dot text-success"></i> Active</a>
                                        <a class="dropdown-item" href="'.$changeStatusInactiveRoute.'"><i
                                                class="fa-regular fa-circle-dot text-danger"></i> Inactive</a>
                                    </div>
                                </div>';
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.plan.edit', $encodedId);
                        $deleteRoute = route('admin.plan.destroy', $encodedId); 
                      
                        // Edit button
                        $editButton = auth()->user()->can('brand-edit') ? 
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        // Delete button
                        $deleteButton = auth()->user()->can('brand-delete') ? 
                            "<a href='#' class='dropdown-item' onclick='confirmDelete(\"delete-brand-{$row->id}\")'><i class='fa-regular fa-trash-can m-r-5'></i> Delete</a>" : '';
                                        
                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                        ' . $deleteButton . '
                                    </div>
                                </div>
                                <form action="' . $deleteRoute . '" method="POST" id="delete-brand-' . $row->id . '" style="display: none;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                </form>';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('admin.pages.plan.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
            ->with('error', 'Something went wrong');
        }
    }

    public function create()
    {
        try {
            return view('admin.pages.plan.create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.plan.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:250|unique:plans,name,',
            'duration' => 'required',
            // 'status' => 'required',
            'price' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $input = $request->all();
            $brand = Plan::create($input);


            DB::commit();
        
            return redirect()->route('admin.plan.index')->with('success', 'Plan added successfully.');;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.plan.index')
            ->with('error', $e->getMessage());
        }
    }
    
    public function show($id)
    {
        try {
            $user = Plan::find($id);
            return view('admin.pages.plan.show',compact('user'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.plan.index')
            ->with('error', 'Something went wrong');
        }
    }
    
    public function edit($id)
    {
        try {
            $id = base64_decode($id);
            $data = Plan::findOrFail($id);
            
            return view('admin.pages.plan.edit',compact('data'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.plan.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:250|unique:plans,name,' . $id ,
            'duration' => 'required',
            // 'status' => 'required',
            'price' => 'required',
        ]);
        DB::beginTransaction();
        try {        
            $input = $request->all();
            $input = Arr::except($input,[ '_token','_method']);
            $brand = Plan::where('id', $id)->update($input);

            DB::commit();
        
            return redirect()->route('admin.plan.index')
                            ->with('success','Brand updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.plan.index')
            ->with('error', $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        try {
            $id = base64_decode($id);
            Plan::findOrFail($id)->delete();
            return redirect()->route('admin.plan.index')->with('success', 'Plan deleted successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.plan.index')
                ->with('error', 'Something went wrong');
        }
    }
    
    public function changeStatus($id,$status)
    {
        try {
            // Validate the status to ensure it's either 1 or 2
            if (!in_array($status, [1, 2])) {
                return redirect()->route('admin.plan.index')
                    ->with('error', 'Invalid status value. Status must be 1 or 2.');
            }

            // Find the vehicle and update its status
            $id = base64_decode($id);
            $brand = Plan::findOrFail($id);
            $brand->status = $status;
            $brand->save();
            
            return redirect()->route('admin.plan.index')->with('success', 'Status changed successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.plan.index')
                ->with('error', 'Something went wrong');
        }
    }

    public function planInfo(Request $request, $id)
    {
        try {
            $plan = Plan::find($id);
            return response()->json([
                'name' => $plan->name,
                'duration' => $plan->duration. ' Days',
                'price' => '₹ '.$plan->price,
            ]);

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong!']);
        }
    }

}