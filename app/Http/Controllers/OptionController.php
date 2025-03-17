<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Traits\Traits;

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
				})
				->where('status', 1)
				->where('added_by', auth()->user()->id)
				->limit($this->limit)->get();

		return response()->json($users);
	}

	public function planList(Request $request)
	{
		$term = $request->input('term');
		
		$plans = Plan::where('name', 'LIKE', '%' . $term . '%')
					->where('status', 1)
					->where('created_by', auth()->user()->id)
					->limit($this->limit)
					->get()
					->map(function ($plan) {
						return [
							'id' => $plan->id,
							'name' => $plan->name. ' ('.$plan->duration.' Days'.')' ." ". '(&#8377;'.''.$plan->price.')'
						];
					});

		return response()->json($plans);
	}

}
