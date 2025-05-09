<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Category;
use App\Models\Activity;
use Illuminate\Http\Request;
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
	
	public function trainers(Request $request)
	{
		$term = $request->input('term');
		
		$plans = User::where('name', 'LIKE', '%' . $term . '%')
					->where('status', 1)
					->where('salary','>',0)
					->where('added_by', auth()->user()->id)
					->limit($this->limit)
					->get()
					->map(function ($trainer) {
						return [
							'id' => $trainer->id,
							'name' => $trainer->name. ' ( One Month'.')' ." ". '(&#8377;'.''.$trainer->pt_fees.')'
						];
					});

		return response()->json($plans);
	}
	
	public function categoryList(Request $request)
	{
		$term = $request->input('term');
		
		$categoryList = Category::where('title', 'LIKE', '%' . $term . '%')
					->where('status', 1)
					->where('added_by', auth()->user()->id)
					->limit($this->limit)
					->get()
					->map(function ($category) {
						return [
							'id' => $category->id,
							'name' => $category->title
						];
					});

		return response()->json($categoryList);
	}
	
	public function packagelist(Request $request)
	{
		$term = $request->input('term');
		
		$packageList = Activity::where('title', 'LIKE', '%' . $term . '%')
					->where('status', '1')
					->where('added_by', auth()->user()->id)
					->limit($this->limit)
					->get()
					->map(function ($package) {
						return [
							'id' => $package->id,
							'name' => $package->title
						];
					});


		return response()->json($packageList);
	}

}
