<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AssignPlan;
use App\Models\User;
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

                $query = User::query()->where('added_by',auth()->user()->id);
                
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'Member');
                });

                // Filter by membership status
                if ($request->membership_status) {
                    $query->where('membership_status', $request->membership_status);
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
                    ->addColumn('start_date', function ($row) {
                        return $row->start_date != null ? Carbon::parse($row->start_date)->format('d M Y') : 'N/A';
                    })
                    ->addColumn('phone', function ($row) {
                        return $row->country_code ?? '+91' . ' ' . $row->phone;
                    })
                    ->addColumn('end_date', function ($row) {
                        return $row->end_date != null ? Carbon::parse($row->end_date)->format('d M Y') : 'N/A';
                    })

                    ->addColumn('added_by', function ($row) {
                        return $row->gym_id;
                    })
                    ->addColumn('membership_status', function ($row) {
                        $statusClass = $row->membership_status == 'Pending' ? 'primary' : ($row->membership_status == 'Active' ? 'success' : ($row->membership_status == 'Expired' ? 'danger' : ''));
                        $status = $row->membership_status;
                        $returnData = '<div class="action-label">
                                            <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                                <i class="fa-regular fa-circle-dot text-' . $statusClass . '"></i> ' . $status . '
                                            </a>
                                        </div>';


                        return $returnData;
                    })
                    // ->addColumn('status', function ($row) {
                    //     $encodedId = base64_encode($row->id);
                    //     $status = $row->status == 1 ? 'success' : 'danger';
                    //     $text = $row->status == 1 ? 'Active' : 'Inactive';
                    //     $changeStatusActiveRoute = route('admin.users.changeStatus', ['id' => $encodedId, 'status' => '1']);
                    //     $changeStatusInactiveRoute = route('admin.users.changeStatus', ['id' => $encodedId, 'status' => '2']);

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
                    // })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.users.edit', $encodedId);
                        $viewRoute = route('admin.users.show', $encodedId);
                        $planRoute = route('admin.assign-plan.create', $encodedId);

                        // Edit button
                        $editButton = auth()->user()->can('user-edit') ?
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        // View button
                        $viewButton = auth()->user()->can('user-view') ?
                            '<a href="' . $viewRoute . '" class="dropdown-item"><i class="fa-solid fa-eye m-r-5"></i> View</a>' : '';


                        // $planButton = auth()->user()->can('user-view') ? 
                        // '<a href="javascript:void(0);" class="dropdown-item assign-plan-btn" data-user-id="' . $row->id . '">
                        //     <i class="fa-solid fa-eye m-r-5"></i> Assign Plan
                        // </a>' : '';

                        $planButton = ($row->membership_status == 'Pending' || $row->membership_status == 'Expired') ?
                            '<a href="javascript:void(0);" class="dropdown-item assign-plan-btn" data-user-id="' . $row->id . '">
                                            <i class="fa-solid fa-user-plus m-r-5"></i> Assign Plan
                                        </a>'
                            : '';

                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                        ' . $viewButton . '
                                        ' . $planButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['name', 'start_date', 'end_date', 'membership_status', 'status', 'action'])
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
            return view('admin.pages.users.create', compact('roles'));
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
            'email' => 'nullable|email|max:250',
            'phone' => 'required',
            // 'phone' => 'unique:users,phone_number,NULL,id,added_by,' . $request->added_by,
            'address' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:1048',
        ]);

        try {
            
            $input = $request->all();
            $input['added_by'] = auth()->user()->id;
            
            $check = User::where('phone', $request->phone)->where('added_by', auth()->user()->id)->first();
            
            if ($check) {
                return redirect()->route('admin.users.index')
                ->with('error', 'User already exist');
            }
            
            DB::beginTransaction();

            $user = User::create($input);
            $user->assignRole('Member');

            if ($user) {
                $this->uploadMedia($request->file('image'), $user, 'images');
            }
            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Member added successfully.');
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
            $id = base64_decode($id);
            $user = User::findOrFail($id);
            $lastestPlan = $user->assignPlan()->latest()->first();
            return view('admin.pages.users.show', compact('user', 'lastestPlan'));
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

            return view('admin.pages.users.edit', compact('data'));
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
            'phone' => 'required|digits:10',
            // 'phone' => 'required|digits:10|unique:users,phone,' . $id,
            'address' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1048',
        ]);
        try {
            $input = $request->all();
            
            $check = User::where('phone', $request->phone)->where('id', '!=', $id)->where('added_by', auth()->user()->id)->first();
            
            if ($check) {
                return redirect()->route('admin.users.index')
                ->with('error', 'User already exist');
            }
            
            DB::beginTransaction();
            
            $user = User::find($id);
            $user->update($input);

            if ($user) {
                if ($request->hasFile('image')) {

                    if ($user->hasMedia('images')) {
                        $user->clearMediaCollection('images'); // Deletes all media in the 'images' collection
                    }

                    $this->uploadMedia($request->file('image'), $user, 'images');
                }
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Member info updated successfully');
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

    public function changeStatus($id, $status)
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

    public function userInfo(Request $request, $id)
    {
        try {
            $user = User::find($id);
            return response()->json([
                'email' => $user->email,
                'phone' => $user->country_code ?? '+91' . ' ' . $user->phone,
                'status' => ucfirst($user->membership_status)
            ]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong!']);
        }
    }

    public function userRenewalHistory(Request $request, $id)
    {
        try {
            if ($request->ajax()) {

                $query = AssignPlan::query();

                // Filter by user ID
                if ($id) {
                    $query->where('user_id', $id);
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
                                        <i class="fa-regular fa-circle-dot text-' . $status . '"></i> ' . $text . '
                                    </a>
                                </div>';
                    })
                    ->addColumn('member_name', function ($row) {
                        return $row->user->name . ' ' . '(' . ($row->user->country_code ?? '+91') . ' ' . $row->user->phone . ')' ?? 'N/A';
                    })
                    ->addColumn('plan', function ($row) {
                        return $row->plan->name;
                    })
                    ->addColumn('price', function ($row) {
                        return '₹ ' . number_format($row->plan->price);;
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
                                        <i class="fa-regular fa-circle-dot text-' . $status . '"></i> ' . $text . '
                                    </a>
                                </div>';
                    })
                    ->addColumn('utr', function ($row) {
                        return $row->utr ?? 'N/A';
                    })
                    ->addColumn('membership_status', function ($row) {
                        $statusClass = $row->membership_status == 'Pending' ? 'primary' : ($row->membership_status == 'Active' ? 'success' : ($row->membership_status == 'Expired' ? 'danger' : ''));
                        $status = $row->membership_status;
                        $returnData = '<div class="action-label">
                                            <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                                <i class="fa-regular fa-circle-dot text-' . $statusClass . '"></i> ' . $status . '
                                            </a>
                                        </div>';


                        return $returnData;
                    })
                    ->rawColumns(['user_type', 'member_name', 'plan', 'status', 'payment_method', 'membership_status'])
                    ->make(true);
            }
            return view('admin.pages.assign_plan.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Something went wrong');
        }
    }
}
