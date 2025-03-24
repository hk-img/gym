@extends('admin.layouts.app')
@section('page_title', 'Workout Manager | Add')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Workout Manager</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.workout.index') }}">List</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.workout.index') }}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add Workout</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.workout.store') }}" method="post" id="workoutForm">
                                @csrf
                                
                                <div class="row g-3">
                                    <!-- Member Selection -->
                                    <div class="col-md-6">
                                        <label class="form-label">Member <span class="text-danger">*</span></label>
                                        <select class="userList form-control" name="member_id" required>
                                            
                                        </select>
                                        @error('member_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>

                                    <!-- Workout Name -->
                                    <div class="col-md-6">
                                        <label class="form-label">Workout Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="workout_name" placeholder="Enter Workout Name" required>
                                        @error('workout_name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <!-- Date -->
                                    <div class="col-md-6">
                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required>
                                        @error('date') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <!-- Dynamic Exercise Fields -->
                                <div class="mt-4">
                                    <h5>Exercises</h5>
                                    <table class="table" id="exerciseTable">
                                        <thead>
                                            <tr>
                                                <th>Exercise Name</th>
                                                <th>Sets</th>
                                                <th>Reps</th>
                                                <th>Weight (kg)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="exercises[0][exercise_name]" class="form-control" required></td>
                                                <td><input type="number" name="exercises[0][sets]" class="form-control" required></td>
                                                <td><input type="number" name="exercises[0][reps]" class="form-control" required></td>
                                                <td><input type="number" name="exercises[0][weight]" class="form-control"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm removeExercise">X</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-secondary" id="addExercise">Add Exercise</button>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary px-4">Save</button>
                                    <button type="button" class="btn btn-secondary px-4" onclick="resetForm()">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeSelect2('.userList', "{{ route('admin.option.userlist') }}", 'Select User');
        });

        function resetForm() {
            document.getElementById('workoutForm').reset();
        }

        document.getElementById('addExercise').addEventListener('click', function() {
            let table = document.getElementById('exerciseTable').getElementsByTagName('tbody')[0];
            let rowCount = table.rows.length;
            let row = table.insertRow(rowCount);
            row.innerHTML = `
                <td><input type="text" name="exercises[${rowCount}][exercise_name]" class="form-control" required></td>
                <td><input type="number" name="exercises[${rowCount}][sets]" class="form-control" required></td>
                <td><input type="number" name="exercises[${rowCount}][reps]" class="form-control" required></td>
                <td><input type="number" name="exercises[${rowCount}][weight]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger btn-sm removeExercise">X</button></td>
            `;
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('removeExercise')) {
                event.target.closest('tr').remove();
            }
        });
    </script>
@endpush
