<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Category;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    
    public function index(Request $request)
    {
        try {

            if ($request->ajax()) {
                $query = Category::query()
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
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.category.edit', $encodedId);
                        $deleteRoute = route('admin.category.destroy', $encodedId);
                        $playRoute = $row->link;
                    
                        return '<div class="dropdown dropdown-action">
                                    <a href="javascript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                        
                                    </div>
                                </div>';
                    })
                    
                    ->rawColumns(['name', 'action','created_at_formatted'])
                    ->make(true);
            }
            
            return view('admin.pages.category.index');
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
            return view('admin.pages.category.create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.category.index')
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
        ]);

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['added_by'] = auth()->user()->id;

            $check = Category::where('title', $request->title)
                        ->where('added_by', auth()->user()->id)
                        ->first();

            if ($check) {
                DB::rollBack(); // Not strictly necessary here, but safe
                return redirect()->route('admin.category.index')
                                ->with('error', 'Category already exists.');
            }

            Category::create($input);

            DB::commit(); // ✅ You must commit the transaction

            return redirect()->route('admin.category.index')
                            ->with('success', 'Category added successfully.');
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
            $data = Category::findOrFail($id);

            return view('admin.pages.category.edit', compact('data'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.category.index')
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
        ]);
        try {
            $input = $request->all();
            
            $check = Category::where('title', $request->title)->where('id', '!=', $id)->where('added_by', auth()->user()->id)->first();
            
            if ($check) {
                return redirect()->route('admin.category.index')
                ->with('error', 'Category already exist');
            }
            
            DB::beginTransaction();
            
            $user = Category::find($id);
            $user->update($input);

            DB::commit();

            return redirect()->route('admin.category.index')
                ->with('success', 'Category info updated successfully');
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
            Category::findOrFail($id)->delete();

            return redirect()->route('admin.category.index')->with('success', 'Category deleted successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.category.index')
                ->with('error', 'Something went wrong');
        }
    }
}
