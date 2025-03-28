@extends('admin.layouts.app')
@section('page_title', 'Diet Plan | View')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Diet Plan</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.diet-plan.index') }}">List</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Diet Plan Info -->
        <div class="card mb-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="profile-view">
                            <div class="profile-img-wrap">
                                <div class="profile-img">
                                    <a href="javacript:void(0);"><img alt=""
                                            src="{{ $dietPlan->user->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/user.jpg') }}"></a>
                                </div>
                            </div>
                            <div class="profile-basic">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="personal-info">
                                            <li>
                                                <div class="title">Member:</div>
                                                <div class="text">{{ $dietPlan->user->name }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Diet Plan Name:</div>
                                                <div class="text">{{ $dietPlan->diet_plan_name }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Date:</div>
                                                <div class="text">{{ Carbon\Carbon::parse($dietPlan->date)->format('d M Y') }}</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meal List -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Meal Type</th>
                        <th>Meal Name</th>
                        <th>Calories</th>
                        <th>Protein (g)</th>
                        <th>Carbs (g)</th>
                        <th>Fats (g)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dietPlan->meals as $index => $meal)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $meal->meal_type }}</td>
                            <td>{{ $meal->meal_name }}</td>
                            <td>{{ $meal->calories ?? 'N/A' }}</td>
                            <td>{{ $meal->protein ?? 'N/A' }}</td>
                            <td>{{ $meal->carbs ?? 'N/A' }}</td>
                            <td>{{ $meal->fats ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection