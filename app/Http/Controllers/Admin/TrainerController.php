<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class TrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:trainer-list|trainer-create|trainer-edit|trainer-delete|trainer-view'], only: ['index']),
            new Middleware(['permission:trainer-create'], only: ['create', 'store']),
            new Middleware(['permission:trainer-edit'], only: ['edit', 'update']),
            new Middleware(['permission:trainer-view'], only: ['show']),
            new Middleware(['permission:trainer-delete'], only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = User::query()->where('added_by',auth()->user()->id)->where('salary','>',0);
                
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'Trainer');
                });

                // Filter by membership status
                if (isset($request->status)) {
                    $query->where('status', $request->status);
                }
                
                $data = $query->latest()->excludeSuperAdmin()->get();

                return DataTables::of($data)
                    ->addIndexColumn() // Adds the iteration column
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
                    })
                    ->editColumn('name', function ($row) {
                        $name = '<h2 class="table-avatar">
                            <a>
                                <span>' . htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8') . '</span>
                            </a>
                        </h2>';

                        return $name;
                    })
                    ->addColumn('joining_date', function ($row) {
                        return $row->start_date != null ? Carbon::parse($row->start_date)->format('d M Y') : 'N/A';
                    })
                    ->addColumn('phone', function ($row) {
                        return $row->country_code ?? '+91' . ' ' . $row->phone;
                    })
                    ->addColumn('salary', function ($row) {
                        return $row->salary ?? 'N/A';
                    })
                    ->addColumn('experience', function ($row) {
                        return $row->experience ?? "N/A";
                    })

                    ->addColumn('pt_fees', function ($row) {
                        return $row->pt_fees ?? "N/A";
                    })
                    ->addColumn('status', function ($row) {
                        $statusClass = $row->status == '1' ? 'Active' : 'Inactive';
                        $status = $row->status;
                        $returnData = '<div class="action-label">
                                            <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                                <i class="fa-regular fa-circle-dot text-' . $status . '"></i> ' . $statusClass . '
                                            </a>
                                        </div>';


                        return $returnData;
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.trainers.edit', $encodedId);
                        $viewRoute = route('admin.trainers.show', $encodedId);

                        // Edit button
                        $editButton = '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>';

                        // View button
                        $viewButton =  '<a href="' . $viewRoute . '" class="dropdown-item"><i class="fa-solid fa-eye m-r-5"></i> View</a>';


                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="javacript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['name', 'start_date', 'end_date',  'status', 'action'])
                    ->make(true);
            }
            return view('admin.pages.trainers.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name')->all();
            return view('admin.pages.trainers.create', compact('roles'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.trainers.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:250',
            'email' => 'nullable|email|max:250',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/|unique:users,phone',
            'salary' => 'required',
            'pt_fees' => 'required',
            'experience' => 'required',
            'start_date' => 'required',
        ]);

        try {    
            
            $input = $request->all();
            $input['added_by'] = auth()->user()->id;
            
            $check = User::where('phone', $request->phone)->where('added_by', auth()->user()->id)->first();
            
            if ($check) {
                return redirect()->route('admin.trainers.index')
                ->with('error', 'Trainer already exist');
            }
            
            DB::beginTransaction();

            $user = User::create($input);
            $user->assignRole('Trainer');
         
            DB::commit();

            return redirect()->route('admin.trainers.index')->with('success', 'Trainer added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.trainers.index')
                ->with('error', $e->getMessage());
        }
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
        try {
            $id = base64_decode($id);
            $data = User::excludeSuperAdmin()->findOrFail($id);

            return view('admin.pages.trainers.edit', compact('data'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.trainers.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:250',
            'email' => 'nullable|email|max:250',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'salary' => 'required',
            'pt_fees' => 'required',
            'experience' => 'required',
            'start_date' => 'required',
        ]);

        try {
            $input = $request->all();
            
            $check = User::where('phone', $request->phone)->where('id', '!=', $id)->where('added_by', auth()->user()->id)->first();
            
            if ($check) {
                return redirect()->route('admin.trainers.index')
                ->with('error', 'Trainers already exist');
            }
            
            DB::beginTransaction();
            
            $user = User::find($id);
            $user->update($input);

            DB::commit();

            return redirect()->route('admin.trainers.index')
                ->with('success', 'Trainer info updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.trainers.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
