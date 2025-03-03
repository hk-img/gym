<?php

namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\Traits;

class BrandController extends Controller implements HasMiddleware
{
    use Traits;

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(['permission:brand-list|brand-create|brand-edit|brand-delete'], only: ['index']),
            new Middleware(['permission:brand-create'], only: ['create', 'store']),
            new Middleware(['permission:brand-edit'], only: ['edit', 'update']),
            new Middleware(['permission:brand-delete'], only: ['destroy']),
        ];
    }
   
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {

                $query = Brand::query();

                if ($request->type) {
                    $query->where('type_id', $request->type);
                }
                if ($request->popular) {
                    $query->where('is_popular', $request->popular);
                }
                $data = $query->with(['type'])->latest()->get();
        
                return DataTables::of($data)
                    ->addIndexColumn() // Adds the iteration column
                    ->addColumn('created_at_formatted', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
                    })
                    ->addColumn('logo', function ($row) {
                        $logoUrl = $row->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/placeholder.jpg');
                        $logo = '
                                    <a href="#" class="list-image">
                                        <img alt="Brand Logo" src="'. $logoUrl .'" />
                                    </a>
                                ';
                        return $logo;
                    })
                    ->addColumn('is_popular', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $status = $row->is_popular == 1 ? 'success' : 'danger';
                        $text = $row->is_popular == 1 ? 'Yes' : 'No';
                        $changeStatusActiveRoute = route('admin.brands.changePopularStatus', ['id' => $encodedId, 'status' => '1']);
                        $changeStatusInactiveRoute = route('admin.brands.changePopularStatus', ['id' => $encodedId, 'status' => '2']);

                        return '<div class="dropdown action-label">
                                    <a href="#" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false"><i
                                            class="fa-regular fa-circle-dot text-'.$status.'"></i> '.$text.' </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="'.$changeStatusActiveRoute.'"><i
                                                class="fa-regular fa-circle-dot text-success"></i> Active</a>
                                        <a class="dropdown-item" href="'.$changeStatusInactiveRoute.'"><i
                                                class="fa-regular fa-circle-dot text-danger"></i> Inactive</a>
                                    </div>
                                </div>';
                    })
                    ->addColumn('status', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $status = $row->status == 1 ? 'success' : 'danger';
                        $text = $row->status == 1 ? 'Active' : 'Inactive';
                        $changeStatusActiveRoute = route('admin.brands.changeStatus', ['id' => $encodedId, 'status' => '1']);
                        $changeStatusInactiveRoute = route('admin.brands.changeStatus', ['id' => $encodedId, 'status' => '2']);

                        return '<div class="dropdown action-label">
                                    <a href="#" class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false"><i
                                            class="fa-regular fa-circle-dot text-'.$status.'"></i> '.$text.' </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="'.$changeStatusActiveRoute.'"><i
                                                class="fa-regular fa-circle-dot text-success"></i> Active</a>
                                        <a class="dropdown-item" href="'.$changeStatusInactiveRoute.'"><i
                                                class="fa-regular fa-circle-dot text-danger"></i> Inactive</a>
                                    </div>
                                </div>';
                    })
                    ->addColumn('action', function ($row) {
                        $encodedId = base64_encode($row->id);
                        $editRoute = route('admin.brands.edit', $encodedId);
                        $deleteRoute = route('admin.brands.destroy', $encodedId); 
                      
                        // Edit button
                        $editButton = auth()->user()->can('brand-edit') ? 
                            '<a href="' . $editRoute . '" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>' : '';

                        // Delete button
                        $deleteButton = auth()->user()->can('brand-delete') ? 
                            "<a href='#' class='dropdown-item' onclick='confirmDelete(\"delete-brand-{$row->id}\")'><i class='fa-regular fa-trash-can m-r-5'></i> Delete</a>" : '';
                                        
                        // Return action buttons with form for deletion
                        return '<div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ' . $editButton . '
                                        ' . $deleteButton . '
                                    </div>
                                </div>
                                <form action="' . $deleteRoute . '" method="POST" id="delete-brand-' . $row->id . '" style="display: none;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                </form>';
                    })
                    ->rawColumns(['logo','is_popular','status', 'action'])
                    ->make(true);
            }

            return view('admin.pages.brands.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.dashboard')
            ->with('error', 'Something went wrong');
        }
    }

    public function create()
    {
        try {
            return view('admin.pages.brands.create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.brands.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:250|unique:brands,name,NULL,id,type_id,' . $request->type,
            'type' => 'required|exists:types,id',
            'description' => 'required|max:500',
            'question.*' => 'nullable|string|max:255',
            'answer.*' => 'nullable|string',
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:1048',
        ]);

        DB::beginTransaction();
        try {
            $input = $request->all();
            $input['type_id'] =$request->type;
            $input = Arr::except($input,['logo', 'type','question','answer','ids']);
            $brand = Brand::create($input);

            if($brand){
                $this->uploadMedia($request->file('logo'), $brand, 'images');

                // Ensure both question and answer arrays have values before looping
                if ($request->filled('question') && $request->filled('answer')) {
                    foreach ($request->question as $key => $question) {
                        if (!empty($question) && !empty($request->answer[$key])) {
                            $brand->faqs()->create([
                                'question' => $question,
                                'answer' => $request->answer[$key],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
        
            return redirect()->route('admin.brands.index')->with('success', 'Brand added successfully.');;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.brands.index')
            ->with('error', $e->getMessage());
        }
    }
    
    public function show($id)
    {
        try {
            $user = User::find($id);
            return view('admin.pages.users.show',compact('user'));
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
            $data = Brand::with('type')->findOrFail($id);
            
            return view('admin.pages.brands.edit',compact('data'));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.brands.index')
            ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:250|unique:brands,name,' . $id . ',id,type_id,' . $request->type,
            'type' => 'required|exists:types,id',
            'description' => 'required|max:500',
            'logo' => 'image|mimes:jpeg,png,jpg,svg,webp|max:1048',
            'question.*' => 'nullable|string|max:255',
            'answer.*' => 'nullable|string',
        ]);
        DB::beginTransaction();
        try {        
            $input = $request->all();
            $input['type_id'] =$request->type;
            $input = Arr::except($input,['logo', 'type', '_token','_method','question','answer','ids']);
            $brand = Brand::where('id', $id)->update($input);

            if($brand){
                $brand = Brand::where('id', $id)->first();
                if($request->hasFile('logo')){

                    if ($brand->hasMedia('images')) {
                        $brand->clearMediaCollection('images'); // Deletes all media in the 'images' collection
                    }

                    $this->uploadMedia($request->file('logo'), $brand, 'images');
                }
                if($request->has('ids')){
                    $brand->faqs()->whereNotIn('id',$request->ids)->delete();
    
                    foreach ($request->question as $key => $question) {
                        $brand->faqs()->updateOrCreate(
                        [
                            'id' => $request->ids[$key],
                        ],
                        [
                            'question' => $question,
                            'answer' => $request->answer[$key],
                        ]);
                    }
                }
            }

            DB::commit();
        
            return redirect()->route('admin.brands.index')
                            ->with('success','Brand updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.brands.index')
            ->with('error', $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        try {
            $id = base64_decode($id);
            Brand::findOrFail($id)->delete();
            return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.brands.index')
                ->with('error', 'Something went wrong');
        }
    }
    
    public function changeStatus($id,$status)
    {
        try {
            // Validate the status to ensure it's either 1 or 2
            if (!in_array($status, [1, 2])) {
                return redirect()->route('admin.brands.index')
                    ->with('error', 'Invalid status value. Status must be 1 or 2.');
            }

            // Find the vehicle and update its status
            $id = base64_decode($id);
            $brand = Brand::findOrFail($id);
            $brand->status = $status;
            $brand->save();
            
            return redirect()->route('admin.brands.index')->with('success', 'Status changed successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.brands.index')
                ->with('error', 'Something went wrong');
        }
    }

    public function changePopularStatus($id,$status)
    {
        try {
            // Validate the status to ensure it's either 1 or 2
            if (!in_array($status, [1, 2])) {
                return redirect()->route('admin.brands.index')
                    ->with('error', 'Invalid status value. Status must be 1 or 2.');
            }

            // Find the vehicle and update its status
            $id = base64_decode($id);
            $brand = Brand::findOrFail($id);
            $brand->is_popular = $status;
            $brand->save();
            
            return redirect()->route('admin.brands.index')->with('success', 'Status changed successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.brands.index')
                ->with('error', 'Something went wrong');
        }
    }

    public function brandList(Request $request, $typeId = null)
	{
		$term = $request->input('term');
		$query = Brand::query();
        $query->when($typeId, function ($q) use($typeId){
            $q->where('type_id',$typeId);
        });
		$brands = $query->where('name', 'LIKE', '%'.$term.'%')->where('status', 1)->limit(10)->get();

		return response()->json($brands);
	}
}