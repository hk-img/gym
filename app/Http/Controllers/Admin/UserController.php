<?php

namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Traits;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Throwable;
use Yajra\DataTables\Facades\DataTables;


class UserController extends Controller implements HasMiddleware
{
    use Traits;

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:user-list|user-create|user-edit|user-delete'], only: ['index']),
            new Middleware(['permission:user-create'], only: ['create', 'store']),
            new Middleware(['permission:user-edit'], only: ['edit', 'update']),
            new Middleware(['permission:user-delete'], only: ['destroy']),
        ];
    }
   
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {

                $query = User::query();

                if ($request->role) {
                    $query->whereHas('roles', function ($q) use ($request) {
                        $q->where('id', $request->role);
                    });
                }

                $data = $query->with('media')->latest()->excludeSuperAdmin()->get();
        
                return DataTables::of($data)
                    ->addIndexColumn() // Adds the iteration column
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
                    })
                    ->editColumn('name', function ($row) {
                        $name = '<h2 class="table-avatar">
                            <a href="#" class="avatar">
                                <img src="' . ($row->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/user.jpg')) . '" alt="User Image">
                            </a>
                            <a>
                                <span>' . htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8') . '</span>
                            </a>
                        </h2>';

                        return $name;
                    })
                    ->addColumn('status', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $status = $row->status == 1 ? 'success' : 'danger';
                        $text = $row->status == 1 ? 'Active' : 'Inactive';
                        $changeStatusActiveRoute = route('admin.users.changeStatus', ['id' => $encodedId, 'status' => '1']);
                        $changeStatusInactiveRoute = route('admin.users.changeStatus', ['id' => $encodedId, 'status' => '2']);

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
                        $editRoute = route('admin.users.edit', $encodedId);
                        $deleteRoute = route('admin.users.destroy', $encodedId);  // Assume the delete route
                    
                        // Edit button
                        $editButton = auth()->user()->can('user-edit') ? 
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';
                    
                        // Delete button
                        $deleteButton = auth()->user()->can('user-delete') ? 
                            "<a href='#' class='dropdown-item' onclick='confirmDelete(\"delete-user-{$row->id}\")'><i class='fa-regular fa-trash-can m-r-5'></i> Delete</a>" : '';
                    
                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                        ' . $deleteButton . '
                                    </div>
                                </div>
                                <form action="' . $deleteRoute . '" method="POST" id="delete-user-' . $row->id . '" style="display: none;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                </form>';
                    })
                    ->rawColumns(['name','status', 'action'])
                    ->make(true);
            }
            return view('admin.pages.users.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
            ->with('error', 'Something went wrong');
        }
    }

    public function create()
    {
        try {
            $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name')->all();
            return view('admin.pages.users.create',compact('roles'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.users.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:250',
            // 'email' => 'required|email|max:250|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone',
            'address' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:1048',
        ]);

        DB::beginTransaction();
        try {
        
            $input = $request->all();
        
            $user = User::create($input);
            $user->assignRole($request->input('roles'));

            if($user){
                $this->uploadMedia($request->file('image'), $user, 'images');
            }
            DB::commit();
        
            return redirect()->route('admin.users.index')->with('success', 'Member added successfully.');;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.users.index')
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
            $data = User::excludeSuperAdmin()->findOrFail($id);
        
            return view('admin.pages.users.edit',compact('data'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.users.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:250',
            // 'email' => 'required|email|max:250|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone,'.$id,
            'address' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1048',
        ]);
        DB::beginTransaction();
        try {        
            $input = $request->all();
        
            $user = User::find($id);
            $user->update($input);

            if($user){
                if($request->hasFile('image')){

                    if ($user->hasMedia('images')) {
                        $user->clearMediaCollection('images'); // Deletes all media in the 'images' collection
                    }

                    $this->uploadMedia($request->file('image'), $user, 'images');
                }
            }

            DB::commit();
        
            return redirect()->route('admin.users.index')
                            ->with('success','Member info updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.users.index')
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