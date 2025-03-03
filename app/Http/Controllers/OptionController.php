<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\City;
use App\Models\EmailTemplate;
use App\Models\FuelType;
use App\Models\State;
use App\Models\Tag;
use App\Models\TransmissionType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleVariant;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Traits\Traits;
use PharIo\Manifest\Email;

class OptionController extends Controller
{
	use Traits;

	public $limit;

	public function __construct() {
		$this->limit = 10;
	}

    public function fuelList(Request $request)
	{
		$term = $request->input('term');
		$fuels = FuelType::where('name', 'LIKE', '%'.$term.'%')->where('status', 1)->limit($this->limit)->get();

		return response()->json($fuels);
	}

    public function transmissionList(Request $request)
	{
		$term = $request->input('term');
		$transmissions = TransmissionType::where('name', 'LIKE', '%'.$term.'%')->where('status', 1)->limit($this->limit)->get();

		return response()->json($transmissions);
	}

    public function roleList(Request $request)
	{
		$term = $request->input('term');
		$roles = Role::where('name', '!=', 'Super Admin')->where('name', 'LIKE', '%'.$term.'%')->limit($this->limit)->get();

		return response()->json($roles);
	}

    public function cityList(Request $request, $stateId = null)
	{
		$term = $request->input('term');
		$query = City::where('name', 'LIKE', '%'.$term.'%');

		$query->when($stateId, function($q) use($stateId){
			$q->where('state', $stateId);
		});

		$limit = $this->limit;
		if($request->has('no_limit')){
			$limit = $query->count();
		}

		$cities = $query->limit($limit)->orderBy('name', 'ASC')->get();

		return response()->json($cities);
	}

    public function stateList(Request $request)
	{
		$term = $request->input('term');
		$states = State::where('name', 'LIKE', '%'.$term.'%')->limit($this->limit)->get();

		return response()->json($states);
	}

    public function variantList(Request $request, $vehicleId = null)
	{
		$term = $request->input('term');
		$query = VehicleVariant::where('name', 'LIKE', '%'.$term.'%');

		$query->when($vehicleId, function($q) use($vehicleId){
			$q->where('vehicle_id', $vehicleId);
		});

		$variants = $query->where('status', 1)->limit($this->limit)->get();

		// Attach image URLs to each model
		$variants->each(function ($variant) {
			$variant->price = formatPrice($variant->price); // Change 'vehicles' to your media collection name
		});
		
		return response()->json($variants);
	}

    public function modelList(Request $request, $typeId = null)
	{
		$term = $request->input('term');
		$brandId = $request->get('brand_id') ?? null;

		$query = Vehicle::where('vehicle_model', 'LIKE', '%'.$term.'%');

		$query->when($typeId, function($q) use($typeId){
			$q->where('type_id', $typeId);
		});

		$query->when($brandId, function($q) use($brandId){
			$q->where('brand_id', $brandId);
		});

		$models = $query->with('brand')->where('status', 1)->limit($this->limit)->get(['id', 'vehicle_model as name', 'vehicle_model', 'brand_id', 'slug']);

		// Attach image URLs to each model
		$models->each(function ($model) {
			$model->image = $model->getFirstMediaUrl('images'); // Change 'vehicles' to your media collection name
			$model->brand_name = $model->brand->name ?? 'N/A';
		});

		return response()->json($models);
	}

	public function userList(Request $request)
	{
		$term = $request->input('term');
		$users = User::where(function($q)use($term){
				$q->where('name', 'LIKE', '%'.$term.'%')
					->orWhere('phone', 'LIKE', '%'.$term.'%')
					->orWhere('email', 'LIKE', '%'.$term.'%');
				})->whereHas('roles', function($q){
					$q->where('name', '=', 'Customer');
				})->where('status', 1)->limit($this->limit)->get();

		return response()->json($users);
	}

	public function templateList(Request $request)
	{
		$term = $request->input('term');
		$templates = EmailTemplate::where('name', 'LIKE', '%'.$term.'%')->where('status', 1)->limit($this->limit)->get();

		return response()->json($templates);
	}

	public function getSingleTemplate(Request $request, $templateId)
	{
		$template = EmailTemplate::findOrFail($templateId);
		if($template){
			return response()->json($template);
		}else{
			return response()->json(null);
		}
	}

	public function tagList(Request $request)
	{
		$term = $request->input('term');
		$query = Tag::where('name', 'LIKE', '%'.$term.'%');

		$tags = $query->where('status', 1)->limit($this->limit)->get();
		
		return response()->json($tags);
	}

	public function brandList(Request $request, $typeId = null)
	{
		$term = $request->input('term');
		$query = Brand::query();
        $query->when($typeId, function ($q) use($typeId){
            $q->where('type_id',$typeId);
        });
		$brands = $query->where('name', 'LIKE', '%'.$term.'%')->where('status', 1)->limit($this->limit)->get();

		return response()->json($brands);
	}
}
