<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\City;
use App\Models\EmailTemplate;
use App\Models\FuelType;
use App\Models\Plan;
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

   
	public function userList(Request $request)
	{
		$term = $request->input('term');
		$users = User::where(function($q)use($term){
				$q->where('name', 'LIKE', '%'.$term.'%')
					->orWhere('phone', 'LIKE', '%'.$term.'%');
				})->whereHas('roles', function($q){
					$q->where('name', '=', 'Member');
				})->where('status', 1)->limit($this->limit)->get();

		return response()->json($users);
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

	public function planList(Request $request)
	{
		$term = $request->input('term');
		$query = Plan::query();
		$brands = $query->where('name', 'LIKE', '%'.$term.'%')->where('status', 1)->limit($this->limit)->get();

		return response()->json($brands);
	}
}
