<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewGymNotification;
use App\Traits\Traits;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Throwable;
use Yajra\DataTables\Facades\DataTables;


class GymController extends Controller implements HasMiddleware
{
    use Traits;

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:gym-list|gym-create|gym-edit|gym-delete'], only: ['index']),
            new Middleware(['permission:gym-create'], only: ['create', 'store']),
            new Middleware(['permission:gym-edit'], only: ['edit', 'update']),
            new Middleware(['permission:gym-delete'], only: ['destroy']),
        ];
    }

    /**
     * List of gym 
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {

                $query = User::query();

                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'Gym');
                });

                $data = $query->with('media')->latest()->excludeSuperAdmin()->get();

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
                    ->addColumn('phone', function ($row) {
                        return $row->country_code ?? '+91' . ' ' . $row->phone;
                    })

                    ->addColumn('email', function ($row) {
                        return $row->email ?? 'N/A';
                    })

                    ->addColumn('status', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $status = $row->status == 1 ? 'success' : 'danger';
                        $text = $row->status == 1 ? 'Active' : 'Inactive';
                        $changeStatusActiveRoute = route('admin.gym.changeStatus', ['id' => $encodedId, 'status' => '1']);
                        $changeStatusInactiveRoute = route('admin.gym.changeStatus', ['id' => $encodedId, 'status' => '2']);

                        return '<div class="dropdown action-label">
                                    <a href="javacript:void(0);" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false"><i
                                            class="fa-regular fa-circle-dot text-' . $status . '"></i> ' . $text . ' </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="' . $changeStatusActiveRoute . '"><i
                                                class="fa-regular fa-circle-dot text-success"></i> Active</a>
                                        <a class="dropdown-item" href="' . $changeStatusInactiveRoute . '"><i
                                                class="fa-regular fa-circle-dot text-danger"></i> Inactive</a>
                                    </div>
                                </div>';
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.gym.edit', $encodedId);

                        // Edit button
                        $editButton = auth()->user()->can('gym-edit') ?
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';


                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="javacript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                        
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['name', 'status', 'action'])
                    ->make(true);
            }
            return view('admin.pages.gym.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Create new gym
     */
    public function create()
    {
        try {
            $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name')->all();
            return view('admin.pages.gym.create', compact('roles'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.gym.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Store new gym
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:250|unique:users,name',
            'email' => 'required|email|max:250|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone',
            'password' => 'required',
        ]);

        DB::beginTransaction();
        try {

            $input = $request->all();

            $user = User::create($input);
            $user->gym_id = 'GYM' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
            $user->save();
            $user->assignRole('Gym');

            if ($user) {
                // $this->uploadMedia($request->file('image'), $user, 'images');

                // Notify
                $user->notify(new NewGymNotification($user));
            }


            DB::commit();

            return redirect()->route('admin.gym.index')->with('success', 'Gym added successfully.');;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.gym.index')
                ->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $id = base64_decode($id);
            $user = User::findOrFail($id);
            $lastestPlan = $user->assignPlan()->latest()->first();
            return view('admin.pages.gym.show', compact('user', 'lastestPlan'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.gym.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Edit existing gym
     */
    public function edit($id)
    {
        try {
            $id = base64_decode($id);
            $data = User::excludeSuperAdmin()->findOrFail($id);

            return view('admin.pages.gym.edit', compact('data'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.gym.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Update existing gym
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:250|unique:users,name',
            'email' => 'required|email|max:250|unique:users,email,' . $id,
            'phone' => 'required|digits:10|unique:users,phone,' . $id,
        ]);
        DB::beginTransaction();
        try {
            $input = $request->all();

            $user = User::find($id);
            $user->update($input);

            // if($user){
            //     if($request->hasFile('image')){

            //         if ($user->hasMedia('images')) {
            //             $user->clearMediaCollection('images'); // Deletes all media in the 'images' collection
            //         }

            //         $this->uploadMedia($request->file('image'), $user, 'images');
            //     }
            // }

            DB::commit();

            return redirect()->route('admin.gym.index')
                ->with('success', 'Gym info updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.gym.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Delete gym
     */
    public function destroy($id)
    {
        try {
            $id = base64_decode($id);
            User::excludeSuperAdmin()->findOrFail($id)->delete();

            return redirect()->route('admin.gym.index')->with('success', 'Gym deleted successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.gym.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Change status (Active/ Inactive) of gym
     */
    public function changeStatus($id, $status)
    {
        try {
            // Validate the status to ensure it's either 1 or 2
            if (!in_array($status, [1, 2])) {
                return redirect()->route('admin.gym.index')
                    ->with('error', 'Invalid status value. Status must be 1 or 2.');
            }

            // Find the vehicle and update its status
            $id = base64_decode($id);
            $user = User::excludeSuperAdmin()->findOrFail($id);
            $user->status = $status;
            $user->save();

            return redirect()->route('admin.gym.index')->with('success', 'Status changed successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.gym.index')
                ->with('error', 'Something went wrong');
        }
    }


    public function gymlisting(Request $request)
    {
        try {

            if ($request->ajax()) {

                $query = User::query();

                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'Gym');
                });

                $query->withCount([
                    'addedUsers as count' => function ($query) {
                        $query->role('Member');
                    }
                ]);
                
                $data = $query->with('media')->latest()->excludeSuperAdmin()->get();

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

                    ->addColumn('status', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $status = $row->status == 1 ? 'success' : 'danger';
                        $text = $row->status == 1 ? 'Active' : 'Inactive';
                        $changeStatusActiveRoute = route('admin.gym.changeStatus', ['id' => $encodedId, 'status' => '1']);
                        $changeStatusInactiveRoute = route('admin.gym.changeStatus', ['id' => $encodedId, 'status' => '2']);

                        return '<div class="dropdown action-label">
                                    <a href="javacript:void(0);" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false"><i
                                            class="fa-regular fa-circle-dot text-' . $status . '"></i> ' . $text . ' </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="' . $changeStatusActiveRoute . '"><i
                                                class="fa-regular fa-circle-dot text-success"></i> Active</a>
                                        <a class="dropdown-item" href="' . $changeStatusInactiveRoute . '"><i
                                                class="fa-regular fa-circle-dot text-danger"></i> Inactive</a>
                                    </div>
                                </div>';
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.gym.edit', $encodedId);

                        // Edit button
                        $editButton = auth()->user()->can('gym-edit') ?
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';


                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="javacript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                        
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['name', 'status', 'action'])
                    ->make(true);
            }
            return view('admin.pages.gym.gymlist');
        } catch (\Throwable $e) {
            dd($e);
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Something went wrong');
        }
    }
}

// $gymAdmins = User::role('gym')
//             ->withCount([
//                 'addedUsers as users_count' => function($query){
//                     $query->role('member');
//                 }
//             ])
//             ->get(['id','name']);