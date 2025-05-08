<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Traits\Traits;
use Carbon\Carbon;
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
            new Middleware(['permission:workout-list|workout-create|workout-edit|workout-delete|show'], only: ['index']),
            new Middleware(['permission:workout-create'], only: ['create', 'store']),
            new Middleware(['permission:workout-edit'], only: ['edit', 'update']),
            new Middleware(['permission:workout-view'], only: ['show']),
            new Middleware(['permission:workout-delete'], only: ['destroy']),
        ];
    }

    /**
     * Workout list
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = Workout::with(['user']);


                $query->whereHas('user', function ($q) {
                    $q->where('added_by', auth()->user()->id);
                });

                $data = $query->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('member_name', function ($row) {
                        return $row->user->name . ' ' . '(' . ($row->user->country_code ?? '+91') . ' ' . $row->user->phone . ')' ?? 'N/A';
                    })

                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->date)->format('d M Y');
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.workout.edit', $encodedId);
                        $viewRoute = route('admin.workout.show', $encodedId);

                        // Edit button
                        // $editButton = auth()->user()->can('workout-edit') ?
                        //     '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        // View button
                        $viewButton = auth()->user()->can('workout-view') ?
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

            return view('admin.pages.workout.index');
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
            return view('admin.pages.workout.create', compact('members'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.workout.index')
                ->with('error', 'Something went wrong');
        }
    }

    // public function store(Request $request)
    // {   
    //     $validated = $request->validate([
    //         'member_id' => 'required|exists:users,id|unique:workouts,user_id',
    //         'exercises' => 'required|array',
    //         'exercises.*.exercise_name' => 'required|string|max:250',
    //         'exercises.*.days' => 'required|string|max:250',
    //         'exercises.*.description' => 'required|string',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $workout = Workout::create([
    //             'user_id' => $validated['member_id'],
    //             'added_by' => auth()->user()->id,
    //         ]);

    //         foreach ($validated['exercises'] as $exercise) {
    //             Exercise::create([
    //                 'workout_id' => $workout->id,
    //                 'exercise_name' => $exercise['exercise_name'],
    //                 'days' => $exercise['days'],
    //                 'description' => $exercise['description'],
    //             ]);
    //         }

    //         DB::commit();
    //         return redirect()->route('admin.workout.index')->with('success', 'Workout added successfully.');
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error($e->getMessage());
    //         return redirect()->route('admin.workout.index')->with('error', 'Something went wrong.');
    //     }
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'exercises' => 'required|array',
            'exercises.*.exercise_name' => 'required|string|max:250',
            'exercises.*.days' => 'required|string|max:250',
            'exercises.*.description' => 'required|string',
        ]);

        DB::beginTransaction();
        try {

            $workouts = Workout::where('user_id', $request->member_id)->first();
            if ($workouts) {

                $workouts->exercises()->delete();

                foreach ($validated['exercises'] as $exercise) {
                    Exercise::create([
                        'workout_id' => $workouts->id,
                        'exercise_name' => $exercise['exercise_name'],
                        'days' => $exercise['days'],
                        'description' => $exercise['description'],
                    ]);
                }
            } else {
                $workout = Workout::create([
                    'user_id' => $validated['member_id'],
                    'added_by' => auth()->user()->id,
                ]);

                foreach ($validated['exercises'] as $exercise) {
                    Exercise::create([
                        'workout_id' => $workout->id,
                        'exercise_name' => $exercise['exercise_name'],
                        'days' => $exercise['days'],
                        'description' => $exercise['description'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.workout.index')->with('success', 'Workout added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.workout.index')->with('error', 'Something went wrong.');
        }
    }


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


    public function edit($id)
    {
        try {
            $id = base64_decode($id);

            $members = User::whereHas('roles', function ($q) {
                $q->where('name', 'Member');
            })->where('added_by', auth()->user()->id)->get();

            $workout = Workout::findOrFail($id);

            return view('admin.pages.workout.edit', compact('workout', 'members'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.workout.index')
                ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'exercises' => 'required|array',
            'exercises.*.exercise_name' => 'required|string|max:250',
            'exercises.*.days' => 'required|string|max:250',
            'exercises.*.description' => 'required|string',
        ]);

        DB::beginTransaction();
        try {

            $workout = Workout::findOrFail($id);

            $workout->update([
                'user_id' => $validated['member_id'],
                'added_by' => auth()->user()->id,
            ]);
            $workout->exercises()->delete();

            foreach ($validated['exercises'] as $exercise) {
                Exercise::create([
                    'workout_id' => $workout->id,
                    'exercise_name' => $exercise['exercise_name'],
                    'days' => $exercise['days'],
                    'description' => $exercise['description'],
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

    public function getdata(Request $request)
    {
        try {
            // dd($request->member_id);
            $memberId = $request->member_id;
            $exercise = Workout::with('exercises')->where('user_id', $memberId)->first();
            return response()->json(['exercise' => $exercise]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }
}
