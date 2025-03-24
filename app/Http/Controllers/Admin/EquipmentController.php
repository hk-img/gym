<?php

namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use App\Models\Equipment;
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

class EquipmentController extends Controller implements HasMiddleware
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
     * Equipment list
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = Equipment::query();

                // Apply date filter if provided
                if ($request->month) {
                    $month = $request->input('month');
                    $query->whereYear('purchase_date', substr($month, 0, 4))
                        ->whereMonth('purchase_date', substr($month, 5, 2));
                }

                // Filter by user who added the equipment
                $query->where('added_by', auth()->user()->id);

                $data = $query->orderBy('purchase_date', 'DESC')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('purchase_date_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->purchase_date)->format('D m, Y');
                    })
                    ->addColumn('equipment_name', function($row){
                        return $row->equipment_name;
                    })
                    ->addColumn('condition', function($row){
                        return ucfirst($row->condition);
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.equipment.edit', $encodedId);

                        // Edit button
                        $editButton = auth()->user()->can('diet-plan-edit') ?
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        return '<div class="dropdown dropdown-action">
                                    <a href="javacript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['purchase_date_formatted', 'equipment_name', 'condition', 'action'])
                    ->make(true);
            }

            return view('admin.pages.equipment.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something went wrong');
        }
    }

    /**
     * Create a equipment form
     */
    public function create()
    {
        try {
            return view('admin.pages.equipment.create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.equipment.index')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Store equipment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:250',
            'purchase_date' => 'required|date',
            'condition' => 'required|in:New,Good,Needs Maintenance',
            'maintenance_date' => 'nullable|date|after_or_equal:purchase_date',
        ]);

        DB::beginTransaction();
        try {
            // Create new equipment entry
            Equipment::create([
                'equipment_name' => $validated['equipment_name'],
                'purchase_date' => $validated['purchase_date'],
                'condition' => $validated['condition'],
                'maintenance_date' => $validated['maintenance_date'] ?? null,
                'added_by' => auth()->user()->id,
            ]);

            DB::commit();
            return redirect()->route('admin.equipment.index')->with('success', 'Equipment added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.equipment.index')->with('error', 'Something went wrong.');
        }
    }

    /**
     * View diet plan
     */
    // public function show($id)
    // {
    //     try {
    //         $id = base64_decode($id);
    //         $dietPlan = DietPlan::with('user')->findOrFail($id);
    //         return view('admin.pages.diet_plan.show', compact('dietPlan'));
    //     } catch (\Throwable $e) {
    //         Log::error($e->getMessage());
    //         return redirect()->route('admin.diet-plan.index')
    //             ->with('error', 'Something went wrong');
    //     }
    // }

    /**
     * Edit an Existing diet plan
     */
    public function edit($id)
    {
        try {
            $id = base64_decode($id);
             
            $equipment = Equipment::findOrFail($id);
            
            return view('admin.pages.equipment.edit',compact('equipment'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.equipment.index')
            ->with('error', 'Something went wrong');
        }
    }

    /**
     * Update an Existing equipment
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:250',
            'purchase_date' => 'required|date',
            'condition' => 'required|in:New,Good,Needs Maintenance',
            'maintenance_date' => 'nullable|date|after_or_equal:purchase_date',
        ]);

        DB::beginTransaction();
        try {
            // Find equipment by ID
            $equipment = Equipment::findOrFail($id);

            // Update equipment details
            $equipment->update([
                'equipment_name' => $validated['equipment_name'],
                'purchase_date' => $validated['purchase_date'],
                'condition' => $validated['condition'],
                'maintenance_date' => $validated['maintenance_date'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('admin.equipment.index')->with('success', 'Equipment updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.equipment.index')->with('error', 'Something went wrong.');
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