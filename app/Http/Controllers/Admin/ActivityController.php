<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Activity;
use \App\Models\AssignPackage;
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
class ActivityController extends Controller
{
    use Traits;
    
    public function index(Request $request)
    {
        try {

            if ($request->ajax()) {

                $query = Activity::query()
                    ->where('added_by', auth()->user()->id)
                    ->latest();
            
                $data = $query->get();
                
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('d D M, Y h:i:s A');
                    })
                    ->editColumn('name', function ($row) {
                        return '<h2 class="table-avatar">
                                    <a>
                                        <span>' . e($row->title) . '</span>
                                    </a>
                                </h2>';
                    })
                    ->editColumn('charges', function ($row) {
                        return '<h2 class="table-avatar">
                                    <a>
                                        <span>' . e($row->charges) . '</span>
                                    </a>
                                </h2>';
                    })->editColumn('duration', function ($row) {
                        return '<h2 class="table-avatar">
                                    <a>
                                        <span>' . e($row->duration) . '</span>
                                    </a>
                                </h2>';
                    })->editColumn('description', function ($row) {
                        return \Str::limit($row->description, 50) ?? 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.activity.edit', $encodedId);
                        $deleteRoute = route('admin.activity.destroy', $encodedId);
                    
                        return '<div class="dropdown dropdown-action">
                                    <a href="javascript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                        
                                    </div>
                                </div>';
                    })
                    
                    ->rawColumns(['name', 'duration','charges','description','action','created_at_formatted'])
                    ->make(true);
            }
            
            return view('admin.pages.activity.index');
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
            return view('admin.pages.activity.create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.activity.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:250',
            'charges' => 'required',
            'duration' => 'required',
            'description' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['added_by'] = auth()->user()->id;

            $check = Activity::where('title', $request->title)
                        ->where('added_by', auth()->user()->id)
                        ->first();

            if ($check) {
                DB::rollBack(); // Not strictly necessary here, but safe
                return redirect()->route('admin.activity.index')
                                ->with('error', 'Activity already exists.');
            }

            Activity::create($input);

            DB::commit(); // ✅ You must commit the transaction

            return redirect()->route('admin.activity.index')
                            ->with('success', 'Activity added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack(); // Roll back if exception occurs
            Log::error($e->getMessage());
            return $e->getMessage();
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
            $data = Activity::findOrFail($id);

            return view('admin.pages.activity.edit', compact('data'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.activity.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:250',
            'charges' => 'required',
            'duration' => 'required',
            'description' => 'required',
        ]);
        
        try {
            $input = $request->all();
            
            $check = Activity::where('title', $request->title)->where('id', '!=', $id)->where('added_by', auth()->user()->id)->first();
            
            if ($check) {
                return redirect()->route('admin.activity.index')
                ->with('error', 'Activity already exist');
            }
            
            DB::beginTransaction();
            
            $user = Activity::find($id);
            $user->update($input);

            DB::commit();

            return redirect()->route('admin.activity.index')
                ->with('success', 'Activity info updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $id = base64_decode($id);
            Activity::findOrFail($id)->delete();

            return redirect()->route('admin.activity.index')->with('success', 'Activity deleted successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.activity.index')
                ->with('error', 'Something went wrong');
        }
    }

    public function activityAssign()
    {
        try {
            return view('admin.pages.activity.assign');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.activity.assign-list')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function assignStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:activities,id',
            'duration' => 'required|integer',
            'payment_type' => 'required|in:full,partial',
            'payment_method' => 'required|in:online,offline',
            'utr' => [
                'required_if:payment_method,online',
                'nullable',
                Rule::unique('assign_packages', 'utr')->ignore(null),
            ],
            'received_amt' => [
                'required_if:payment_type,partial',
                'nullable',
            ],
            'discount' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $userId = $validated['user_id'];
            $packageId = $validated['package_id'];
            $months = (int) $validated['duration'];
            $discount = floatval($validated['discount'] ?? 0);
            $plan = Activity::select('charges')->findOrFail($packageId);
            $planCharges = $plan->charges;
            $totalAmount = ($planCharges * $months) - $discount;

            // Check if user has previous package
            $user_type = AssignPackage::where('user_id', $userId)->exists() ? 'old' : 'new';

            // Validate partial payment
            if ($validated['payment_type'] === "partial" && $validated['received_amt'] > $totalAmount) {

                return response()->json([
                    'message' => 'Discount amount cannot exceed the activity price.'
                ], 422); 
                // return redirect()->back()->with('error', 'Received amount cannot be greater than activity price');
            }

            // Dates
            $startDate = now();
            $endDate = now()->addMonths($months);

            // Determine received amount
            $receivedAmt = $validated['received_amt'] ?? $totalAmount;

            // Create package assignment
            $assignPlan = AssignPackage::create([
                'user_id' => $userId,
                'package_id' => $packageId,
                'duration' => $months,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_type' => $validated['payment_type'],
                'payment_method' => $validated['payment_method'],
                'utr' => $validated['utr'] ?? null,
                'discount' => $discount,
                'received_amt' => $receivedAmt,
                'user_type' => $user_type,
            ]);

            // Update user's package status only if not already active
            $assignPlan->user()->where('package_status', '!=', 'active')->update(['package_status' => 'active']);

            $commonData = [
                'gym_id' => auth()->user()->id,
                'user_id' => $userId,
                'table_id' => $assignPlan->id,
                'type' => 'assign_package',
                'received_amt' => $receivedAmt,
                'balance_amt' => $totalAmount - $receivedAmt,
                'total_amt' => $totalAmount,
                'payment_type' => $validated['payment_type'],
                'status' => 'cleared',
            ];

            // Create Cr & Dr transactions
            $this->setTransactions($commonData + ['payment_status' => 'Cr']);
            $this->setTransactions($commonData + ['payment_status' => 'Dr']);

            // Update closing balance
            $this->setClosingAmt($userId, auth()->user()->id);

            DB::commit();

            return response()->json([
                    'message' => 'Activity assigned successfully.',
                    "url"=>url('admin/activity-assign-list')
            ], 200);

            // return redirect()->route('admin.activity-assign-list')->with('success', 'Activity assigned successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function assignList(Request $request)
    {
        try {

            if ($request->ajax()) {

                $query = AssignPackage::query();
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
                    ->addColumn('activity', function ($row) {
                        return $row->activity->title;
                    })
                    ->addColumn('duration', function ($row) {
                        return $row->duration;
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
            
            return view('admin.pages.activity.assign-list');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Something went wrong');
        }
    }
}
