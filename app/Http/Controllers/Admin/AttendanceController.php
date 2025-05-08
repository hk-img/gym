<?php

namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DietPlan;
use App\Traits\Traits;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller implements HasMiddleware
{
    use Traits;

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:attendance-list|attendance-create|attendance-edit|attendance-delete|show'], only: ['index']),
            new Middleware(['permission:attendance-create'], only: ['create', 'store']),
            new Middleware(['permission:attendance-edit'], only: ['edit', 'update']),
            new Middleware(['permission:attendance-view'], only: ['show']),
            new Middleware(['permission:attendance-delete'], only: ['destroy']),
        ];
    }
    
    /**
     * Attendance list
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = Attendance::with(['user']);

                // Apply date filter if provided
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
                    ->addColumn('date', function ($row) {
                        return \Carbon\Carbon::parse($row->date)->format('D, M d, Y');
                    })
                    ->addColumn('member_name', function($row){
                        return $row->user->name .' ('.($row->user->country_code ?? '+91').' '.$row->user->phone.')' ?? 'N/A';
                    }) 
                    ->addColumn('time_in', function($row){
                        return $row->time_in;
                    })
                    ->addColumn('time_out', function($row){
                        return $row->time_out ?? 'N/A';
                    }) 
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.attendance.edit', $encodedId);
                        $viewRoute = route('admin.attendance.show', $encodedId);

                        // Edit button
                        $editButton = auth()->user()->can('attendance-edit') ?
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        // Return action buttons
                        return '<div class="dropdown dropdown-action">
                                    <a href="javascript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['date', 'member_name', 'time_in', 'time_out', 'action'])
                    ->make(true);
            }

            return view('admin.pages.attendance.index');
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
            return view('admin.pages.attendance.create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.attendance.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Store new attendance record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
        ]);

        DB::beginTransaction();
        try {
            // Create attendance entry
            Attendance::create([
                'user_id' => $validated['member_id'],
                'date' => $validated['date'],
                'time_in' => $validated['time_in'],
                'time_out' => $validated['time_out'] ?? null,
                // 'marked_by' => auth()->user()->id,
            ]);

            DB::commit();
            return redirect()->route('admin.attendance.index')->with('success', 'Attendance marked successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.attendance.index')->with('error', 'Something went wrong.');
        }
    }


    /**
     * View attendance
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
     * Edit an Existing attendance
     */
    public function edit($id)
    {
        try {
            $id = base64_decode($id);
              
            $attendance = Attendance::findOrFail($id);
            
            return view('admin.pages.attendance.edit',compact('attendance'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.attendance.index')
            ->with('error', 'Something went wrong');
        }
    }

    /**
     * Update an Existing attendance
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time_in' => 'required',
            'time_out' => 'nullable|after:time_in',
        ]);

        DB::beginTransaction();
        try {
            // Find the attendance record
            $attendance = Attendance::findOrFail($id);

            // Update attendance entry
            $attendance->update([
                'user_id' => $validated['member_id'],
                'date' => $validated['date'],
                'time_in' => $validated['time_in'],
                'time_out' => $validated['time_out'] ?? null,
                // 'marked_by' => auth()->user()->id,
            ]);

            DB::commit();
            return redirect()->route('admin.attendance.index')->with('success', 'Attendance updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.attendance.index')->with('error', 'Something went wrong.');
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