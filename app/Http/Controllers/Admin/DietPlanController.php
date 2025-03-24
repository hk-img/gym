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
    
    /**
     * Diet plan list
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = DietPlan::with(['user'])->withCount(['meals']);

                // Apply date range filter if provided
                if ($request->month) {
                    $month = $request->input('month');
                    $query->whereYear('created_at', substr($month, 0, 4))
                        ->whereMonth('created_at', substr($month, 5, 2));
                }
                
                $query->whereHas('user', function($q){
                    $q->where('added_by', auth()->user()->id);
                });

                $data = $query->orderBy('date', 'DESC')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
                    })
                    ->addColumn('member_name', function($row){
                        return $row->user->name .' '.'('.($row->user->country_code ?? '+91').' '.$row->user->phone.')' ?? 'N/A';
                    }) 
                    ->addColumn('diet_plan_name', function($row){
                        return $row->diet_plan_name;
                    })
                    ->addColumn('total_meals', function($row){
                        return $row->meals_count;
                    }) 
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.diet-plan.edit', $encodedId);
                        $viewRoute = route('admin.diet-plan.show', $encodedId);

                        // Edit button
                        $editButton = auth()->user()->can('diet-plan-edit') ?
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        // View button
                        $viewButton = auth()->user()->can('diet-plan-view') ?
                            '<a href="' . $viewRoute . '" class="dropdown-item"><i class="fa-solid fa-eye m-r-5"></i> View</a>' : '';

                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="javacript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                        ' . $viewButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['created_at_formatted','diet_plan_name','total_meals','action'])
                    ->make(true);
            }

            return view('admin.pages.diet_plan.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something went wrong');
        }
    }


    /**
     * Create a diet plan form
     */
    public function create()
    {
        try {
            $members = User::whereHas('roles', function($q){
                $q->where('name', 'Member');
            })->where('added_by', auth()->user()->id)->get();
            return view('admin.pages.diet_plan.create', compact('members'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')
                ->with('error', 'Something went wrong');
        }
    }


    /**
     * Store new diet plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'diet_plan_name' => 'required|string|max:250',
            'date' => 'required|date',
            'meals' => 'required|array',
            'meals.*.meal_type' => 'required|string|max:250',
            'meals.*.meal_name' => 'required|string|max:250',
            'meals.*.calories' => 'nullable|numeric|min:0',
            'meals.*.protein' => 'nullable|numeric|min:0',
            'meals.*.carbs' => 'nullable|numeric|min:0',
            'meals.*.fats' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create diet plan entry
            $dietPlan = DietPlan::create([
                'user_id' => $validated['member_id'],
                'diet_plan_name' => $validated['diet_plan_name'],
                'date' => $validated['date'],
                'added_by' => auth()->user()->id,
            ]);

            // Save meals
            foreach ($validated['meals'] as $meal) {
                Meal::create([
                    'diet_plan_id' => $dietPlan->id,
                    'meal_name' => $meal['meal_name'],
                    'meal_type' => $meal['meal_type'],
                    'calories' => $meal['calories'] ?? null,
                    'protein' => $meal['protein'] ?? null,
                    'carbs' => $meal['carbs'] ?? null,
                    'fats' => $meal['fats'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.diet-plan.index')->with('success', 'Diet Plan added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')->with('error', 'Something went wrong.');
        }
    }

    /**
     * View diet plan
     */
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

    /**
     * Edit an Existing diet plan
     */
    public function edit($id)
    {
        try {
            $id = base64_decode($id);
            
            $members = User::whereHas('roles', function($q){
                $q->where('name', 'Member');
            })->where('added_by', auth()->user()->id)->get();
            
            $dietPlan = DietPlan::findOrFail($id);
            
            return view('admin.pages.diet_plan.edit',compact('dietPlan','members'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.diet-plan.index')
            ->with('error', 'Something went wrong');
        }
    }

    /**
     * Update an Existing diet plan
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'diet_plan_name' => 'required|string|max:250',
            'date' => 'required|date',
            'meals' => 'required|array',
            'meals.*.meal_type' => 'required|string|max:250',
            'meals.*.meal_name' => 'required|string|max:250',
            'meals.*.calories' => 'nullable|numeric|min:0',
            'meals.*.protein' => 'nullable|numeric|min:0',
            'meals.*.carbs' => 'nullable|numeric|min:0',
            'meals.*.fats' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Find the existing diet plan
            $dietPlan = DietPlan::findOrFail($id);
            
            // Update diet plan details
            $dietPlan->update([
                'user_id' => $validated['member_id'],
                'diet_plan_name' => $validated['diet_plan_name'],
                'date' => $validated['date'],
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
                        'calories' => $meal['calories'] ?? null,
                        'protein' => $meal['protein'] ?? null,
                        'carbs' => $meal['carbs'] ?? null,
                        'fats' => $meal['fats'] ?? null,
                    ]);
                } else {
                    // Create new meal
                    Meal::create([
                        'diet_plan_id' => $dietPlan->id,
                        'meal_name' => $meal['meal_name'],
                        'meal_type' => $meal['meal_type'],
                        'calories' => $meal['calories'] ?? null,
                        'protein' => $meal['protein'] ?? null,
                        'carbs' => $meal['carbs'] ?? null,
                        'fats' => $meal['fats'] ?? null,
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

    /**
     * Delete workout
     */
    // public function destroy($id)
    // {
    //     try {
    //         $id = base64_decode($id);
    //         Workout::findOrFail($id)->delete();
            
    //         return redirect()->route('admin.workout.index')->with('success', 'Workout deleted successfully.');
    //     } catch (\Throwable $e) {
    //         Log::error($e->getMessage());
    //         return redirect()->route('admin.workout.index')
    //             ->with('error', 'Something went wrong');
    //     }
    // }
    
}