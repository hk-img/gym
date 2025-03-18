<?php

namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
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


class WorkoutController extends Controller implements HasMiddleware
{
    use Traits;

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:assign-plan-list|assign-plan-create|assign-plan-edit|assign-plan-delete'], only: ['index']),
            new Middleware(['permission:assign-plan-create'], only: ['create', 'store']),
            new Middleware(['permission:assign-plan-edit'], only: ['edit', 'update']),
            new Middleware(['permission:assign-plan-delete'], only: ['destroy']),
        ];
    }
    
    /**
     * Workout list
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = Workout::with(['user'])->withCount(['exercises']);

                // Apply date range filter if provided
                if ($request->month) {
                    $month = $request->input('month');
                    $query->whereYear('date', substr($month, 0, 4))
                    ->whereMonth('date', substr($month, 5, 2));
                }
                
                $query->whereHas('user', function($q){
                    $q->where('added_by', auth()->user()->id);
                });

                $data = $query->orderBy('date', 'DESC')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('member_name', function($row){
                        return $row->user->name .' '.'('.($row->user->country_code ?? '+91').' '.$row->user->phone.')' ?? 'N/A';
                    }) 
                    ->addColumn('total_exercises', function($row){
                        return $row->exercises_count;
                    }) 

                    ->addColumn('created_at_formatted', function($row){
                        return \Carbon\Carbon::parse($row->date)->format('D m, Y');
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.workout.edit', $encodedId);
                        $viewRoute = route('admin.workout.show', $encodedId);

                        // Edit button
                        $editButton = auth()->user()->can('user-edit') ?
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        // View button
                        $viewButton = auth()->user()->can('user-view') ?
                            '<a href="' . $viewRoute . '" class="dropdown-item"><i class="fa-solid fa-eye m-r-5"></i> View</a>' : '';

                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                        ' . $viewButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['created_at_formatted','total_exercises','action'])
                    ->make(true);
            }

            return view('admin.pages.workout.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something went wrong');
        }
    }

    /**
     * Create a workout form
     */
    public function create()
    {
        try {
            $members = User::whereHas('roles', function($q){
                $q->where('name', 'Member');
            })->where('added_by', auth()->user()->id)->get();
            return view('admin.pages.workout.create',compact('members'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.workout.index')
            ->with('error', 'Something went wrong');
        }
    }

    /**
     * Store new workout
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'workout_name' => 'required|string|max:250',
            'date' => 'required|date',
            'exercises' => 'required|array',
            'exercises.*.exercise_name' => 'required|string|max:250',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|integer|min:1',
            'exercises.*.weight' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create workout entry
            $workout = Workout::create([
                'user_id' => $validated['member_id'],
                'workout_name' => $validated['workout_name'],
                'date' => $validated['date'],
                'added_by' => auth()->user()->id,
            ]);

            // Save exercises
            foreach ($validated['exercises'] as $exercise) {
                Exercise::create([
                    'workout_id' => $workout->id,
                    'exercise_name' => $exercise['exercise_name'],
                    'sets' => $exercise['sets'],
                    'reps' => $exercise['reps'],
                    'weight' => $exercise['weight'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.workout.index')->with('success', 'Workout added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.workout.index')->with('error', 'Something went wrong.');
        }
    }

    /**
     * View Workout
     */
    public function show($id)
    {
        try {
            $id = base64_decode($id);
            $workout = Workout::with('exercises', 'user')->findOrFail($id);
            return view('admin.pages.workout.show', compact('workout'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.workout.index')
                ->with('error', 'Something went wrong');
        }
    }

    
    /**
     * Edit an Existing workout
     */
    public function edit($id)
    {
        try {
            $id = base64_decode($id);
            
            $members = User::whereHas('roles', function($q){
                $q->where('name', 'Member');
            })->where('added_by', auth()->user()->id)->get();
            
            $workout = Workout::findOrFail($id);
            
            return view('admin.pages.workout.edit',compact('workout','members'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.workout.index')
            ->with('error', 'Something went wrong');
        }
    }

    /**
     * Update an Existing workout
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'workout_name' => 'required|string|max:250',
            'date' => 'required|date',
            'exercises' => 'required|array',
            'exercises.*.exercise_name' => 'required|string|max:250',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|integer|min:1',
            'exercises.*.weight' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Find workout
            $workout = Workout::findOrFail($id);
            
            // Update workout details
            $workout->update([
                'user_id' => $validated['member_id'],
                'workout_name' => $validated['workout_name'],
                'date' => $validated['date'],
                'added_by' => auth()->user()->id,
            ]);

            // Remove existing exercises and insert new ones (can be optimized with update logic if needed)
            $workout->exercises()->delete();

            foreach ($validated['exercises'] as $exercise) {
                Exercise::create([
                    'workout_id' => $workout->id,
                    'exercise_name' => $exercise['exercise_name'],
                    'sets' => $exercise['sets'],
                    'reps' => $exercise['reps'],
                    'weight' => $exercise['weight'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.workout.index')->with('success', 'Workout updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.workout.index')->with('error', 'Something went wrong.');
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