<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Throwable;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:role-list|role-create|role-edit|role-delete'], only: ['index']),
            new Middleware(['permission:role-create'], only: ['create', 'store']),
            new Middleware(['permission:role-edit'], only: ['edit', 'update']),
            new Middleware(['permission:role-delete'], only: ['destroy']),
        ];
    }
    
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {

                $query = Role::query();

                if ($request->type) {
                    $query->where('type_id', $request->type);
                }

                $data = $query->where('name', '!=', 'Super Admin')->latest()->get();
        
                return DataTables::of($data)
                    ->addIndexColumn() // Adds the iteration column
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
                    })
                    ->addColumn('permission', function ($row) {
                        return $row->permissions->pluck('name')->implode(',');
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.roles.edit', $encodedId);
                    
                        // Edit button
                        $editButton = auth()->user()->can('role-edit') ? 
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';
                                        
                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['permission','action'])
                    ->make(true);
            }

            return view('admin.pages.roles.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
    
            return redirect()->route('admin.dashboard')
            ->with('error', 'Something went wrong');
        }
    }

    public function create()
    {
        try {
            $permission = Permission::get();
            return view('admin.pages.roles.create', compact('permission'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return redirect()->route('admin.roles.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:250',
            'permission' => 'nullable',
        ]);

        DB::beginTransaction();

        try {

            $role = Role::create(['name' => $request->input('name')]);
            $permissions = [];
            $post_permissions = $request->input('permission');
            foreach ($post_permissions as $key => $val) {
                $permissions[intval($val)] = intval($val);
            }
            $role->syncPermissions($permissions);
    
            DB::commit();

            return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.roles.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function show($id)
    {
        try {
            $role = Role::find($id);
            $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                ->where("role_has_permissions.role_id", $id)
                ->get();
            return view('admin.pages.roles.show', compact('role', 'rolePermissions'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('roles.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function edit($id)
    {
        try {
            $id = base64_decode($id);
            $role = Role::where('name', '!=', 'Super Admin')->findOrFail($id);
            $permission = Permission::get();
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->all();
    
            return view('admin.pages.roles.edit', compact('role', 'permission', 'rolePermissions'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.roles.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:250|unique:roles,name,' . $id,
            'permission' => 'nullable',
        ]);

        DB::beginTransaction();

        try {    
            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->save();
            $permissions = [];
            $post_permissions = $request->input('permission');
            foreach ($post_permissions as $key => $val) {
                $permissions[intval($val)] = intval($val);
            }
            $role->syncPermissions($permissions);
            DB::commit();

            return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');;
    
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.roles.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function destroy($id)
    {
        try {
            $id = base64_decode($id);
            $role = Role::where('name', '!=', 'Super Admin')->findOrFail($id);
            $role->delete();
            return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.roles.index')
            ->with('error', 'Something went wrong');
        }
    }
}