<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use App\Models\Meal;
use App\Models\User;
use App\Traits\Traits;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class DietPlanController extends Controller implements HasMiddleware
{
    use Traits;

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:diet-plan-list|diet-plan-create|diet-plan-edit|diet-plan-delete|show'], only: ['index']),
            new Middleware(['permission:diet-plan-create'], only: ['create', 'store']),
            new Middleware(['permission:diet-plan-edit'], only: ['edit', 'update']),
            new Middleware(['permission:diet-plan-view'], only: ['show']),
            new Middleware(['permission:diet-plan-delete'], only: ['destroy']),
        ];
    }


    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = DietPlan::with(['user']);

                // Apply date range filter if provided
                // if ($request->month) {
                //     $month = $request->input('month');
                //     $query->whereYear('created_at', substr($month, 0, 4))
                //         ->whereMonth('created_at', substr($month, 5, 2));
                // }

                $query->whereHas('user', function ($q) {
                    $q->where('added_by', auth()->user()->id);
                });

                $data = $query->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('d D m, Y h:i:s');
                    })
                    ->addColumn('member_name', function ($row) {
                        return $row->user->name . ' ' . '(' . ($row->user->country_code ?? '+91') . ' ' . $row->user->phone . ')' ?? 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        // $editRoute = route('admin.diet-plan.edit', $encodedId);
                        $viewRoute = route('admin.diet-plan.show', $encodedId);

                        // Edit button
                        // $editButton = auth()->user()->can('diet-plan-edit') ?
                        //     '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        // View button
                        $viewButton = auth()->user()->can('diet-plan-view') ?
                            '<a href="' . $viewRoute . '" class="dropdown-item"><i class="fa-solid fa-eye m-r-5"></i> View</a>' : '';

                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="javacript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $viewButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['created_at_formatted', 'action'])
                    ->make(true);
            }

            return view('admin.pages.diet_plan.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something went wrong');
        }
    }

    public function create()
    {
        try {
            $members = User::whereHas('roles', function ($q) {
                $q->where('name', 'Member');
            })->where('added_by', auth()->user()->id)->get();
            return view('admin.pages.diet_plan.create', compact('members'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')
                ->with('error', 'Something went wrong');
        }
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'member_id' => 'required|exists:users,id',
    //         'meals' => 'required|array',
    //         'meals.*.meal_type' => 'required|string|max:250',
    //         'meals.*.meal_name' => 'required|string|max:250',
    //         'meals.*.description' => 'required|string',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $dietPlan = DietPlan::create([
    //             'user_id' => $validated['member_id'],
    //             'added_by' => auth()->user()->id,
    //         ]);

    //         foreach ($validated['meals'] as $meal) {
    //             Meal::create([
    //                 'diet_plan_id' => $dietPlan->id,
    //                 'meal_name' => $meal['meal_name'],
    //                 'meal_type' => $meal['meal_type'],
    //                 'description' => $meal['description'],
    //             ]);
    //         }

    //         DB::commit();
    //         return redirect()->route('admin.diet-plan.index')->with('success', 'Diet Plan added successfully.');
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error($e->getMessage());
    //         return redirect()->route('admin.diet-plan.index')->with('error', 'Something went wrong.');
    //     }
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'meals' => 'required|array',
            'meals.*.meal_type' => 'required|string|max:250',
            'meals.*.meal_name' => 'required|string|max:250',
            'meals.*.description' => 'required|string',
        ]);

        DB::beginTransaction();
        try {

            $dietPlan = DietPlan::where('user_id', $request->member_id)->first();

            if ($dietPlan) {
                // Get existing meal IDs
                $existingMealIds = $dietPlan->meals->pluck('id')->toArray();
                $submittedMealIds = collect($validated['meals'])->pluck('id')->filter()->toArray();

                // Delete removed meals
                $mealsToDelete = array_diff($existingMealIds, $submittedMealIds);
                Meal::whereIn('id', $mealsToDelete)->delete();

                // Update or create meals
                foreach ($validated['meals'] as $meal) {
                    if (isset($meal['id']) && in_array($meal['id'], $existingMealIds)) {
                        // Update existing meal
                        Meal::where('id', $meal['id'])->update([
                            'meal_name' => $meal['meal_name'],
                            'meal_type' => $meal['meal_type'],
                            'description' => $meal['description'],
                        ]);
                    } else {
                        // Create new meal
                        Meal::create([
                            'diet_plan_id' => $dietPlan->id,
                            'meal_name' => $meal['meal_name'],
                            'meal_type' => $meal['meal_type'],
                            'description' => $meal['description'],
                        ]);
                    }
                }
            }
            
            else {
                $dietPlan = DietPlan::create([
                    'user_id' => $validated['member_id'],
                    'added_by' => auth()->user()->id,
                ]);
    
                foreach ($validated['meals'] as $meal) {
                    Meal::create([
                        'diet_plan_id' => $dietPlan->id,
                        'meal_name' => $meal['meal_name'],
                        'meal_type' => $meal['meal_type'],
                        'description' => $meal['description'],
                    ]);
                }
    
            }
            DB::commit();
            return redirect()->route('admin.diet-plan.index')->with('success', 'Diet Plan added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')->with('error', 'Something went wrong.');
        }
    }

    public function show($id)
    {
        try {
            $id = base64_decode($id);
            $dietPlan = DietPlan::with('user')->findOrFail($id);
            return view('admin.pages.diet_plan.show', compact('dietPlan'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')
                ->with('error', 'Something went wrong');
        }
    }

    public function edit($id)
    {
        try {
            $id = base64_decode($id);

            $members = User::whereHas('roles', function ($q) {
                $q->where('name', 'Member');
            })->where('added_by', auth()->user()->id)->get();

            $dietPlan = DietPlan::findOrFail($id);
            // dd($dietPlan);
            return view('admin.pages.diet_plan.edit', compact('dietPlan', 'members'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')
                ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'meals' => 'required|array',
            'meals.*.meal_type' => 'required|string|max:250',
            'meals.*.meal_name' => 'required|string|max:250',
            'meals.*.description' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Find the existing diet plan
            $dietPlan = DietPlan::findOrFail($id);

            // Update diet plan details
            $dietPlan->update([
                'user_id' => $validated['member_id'],
                'added_by' => auth()->user()->id,
            ]);

            // Get existing meal IDs
            $existingMealIds = $dietPlan->meals->pluck('id')->toArray();
            $submittedMealIds = collect($validated['meals'])->pluck('id')->filter()->toArray();

            // Delete removed meals
            $mealsToDelete = array_diff($existingMealIds, $submittedMealIds);
            Meal::whereIn('id', $mealsToDelete)->delete();

            // Update or create meals
            foreach ($validated['meals'] as $meal) {
                if (isset($meal['id']) && in_array($meal['id'], $existingMealIds)) {
                    // Update existing meal
                    Meal::where('id', $meal['id'])->update([
                        'meal_name' => $meal['meal_name'],
                        'meal_type' => $meal['meal_type'],
                        'description' => $meal['description'],
                    ]);
                } else {
                    // Create new meal
                    Meal::create([
                        'diet_plan_id' => $dietPlan->id,
                        'meal_name' => $meal['meal_name'],
                        'meal_type' => $meal['meal_type'],
                        'description' => $meal['description'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.diet-plan.index')->with('success', 'Diet Plan updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')->with('error', 'Something went wrong.');
        }
    }

    public function dietdata(Request $request)
    {
        try {

            $memberId = $request->member_id;
            $meal = DietPlan::with('meals')->where('user_id', $memberId)->first();
            return response()->json(['meal' => $meal]);
        } catch (\Throwable $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')->with('error', 'Something went wrong.');
        }
    }
}
