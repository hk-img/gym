@extends('admin.layouts.app')
@section('page_title', 'Workout Manager | View')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Workout Manager</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.workout.index') }}">List</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Workout Info -->
        <div class="card mb-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="profile-view">
                            <div class="profile-basic">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="personal-info">
                                            <li>
                                                <div class="title">Member:</div>
                                                <div class="text">{{ $workout->user->name }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Workout Name:</div>
                                                <div class="text">{{ $workout->workout_name }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Date:</div>
                                                <div class="text">{{ Carbon\Carbon::parse($workout->date)->format('d M Y') }}</div>
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

        <!-- Exercise List -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exercise Name</th>
                        <th>Sets</th>
                        <th>Reps</th>
                        <th>Weight (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workout->exercises as $index => $exercise)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $exercise->exercise_name }}</td>
                            <td>{{ $exercise->sets }}</td>
                            <td>{{ $exercise->reps }}</td>
                            <td>{{ $exercise->weight ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
