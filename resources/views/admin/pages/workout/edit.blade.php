@extends('admin.layouts.app')
@section('page_title', 'Workout Manager | Edit')
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
                            <!-- <li class="breadcrumb-item"><a href="{{ route('admin.workout.index') }}">List</a></li> -->
                            <li class="breadcrumb-item active">Edit</li>
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
                            <h4 class="card-title mb-0">Edit Workout</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.workout.update', $workout->id) }}" method="post" id="workoutForm">
                                @csrf
                                @method('PUT')
                                
                                <div class="row g-3">
                                    <!-- Member Selection -->
                                    <div class="col-md-6">
                                        <label class="form-label">Member <span class="text-danger">*</span></label>
                                        <select class="userList form-control" name="member_id" required>
                                            
                                        </select>
                                        @error('member_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>
                                

                                <div class="mt-4">
                                    <h5>Exercises</h5>
                                    <table class="table" id="exerciseTable">
                                        <thead>
                                            <tr>
                                                <th>Days</th>
                                                <th>Exercise Name</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($workout->exercises as $index => $exercise)
                                                <tr>
                                                    <td>{{$exercise->days}}</td>
                                                    <td>    
                                                        <input type="hidden" name="exercises[{{ $index }}][days]" class="form-control" value="{{ $exercise->days }}" required>
                                                        <input type="text" name="exercises[{{ $index }}][exercise_name]" class="form-control" value="{{ $exercise->exercise_name }}" required/>
                                                    </td>
                                                    <td><textarea name="exercises[{{ $index }}][description]" class="form-control" required>{{$exercise->description}}</textarea></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary px-4">Update</button>
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
        var selectedMemberId = "{{ $workout->user_id ?? '' }}"; 
        var selectedMemberName = "{{ $workout->user->name ?? '' }}";

        document.addEventListener('DOMContentLoaded', function() {
            initializeSelect2('.userList', "{{ route('admin.option.userlist') }}", 'Select User');
        });

        if (selectedMemberId) {
            var option = new Option(selectedMemberName, selectedMemberId, true, true);
            $('.userList').append(option).trigger('change');
        } 
        
        function resetForm() {
            document.getElementById('workoutForm').reset();
        }

        // document.getElementById('addExercise').addEventListener('click', function() {
        //     let table = document.getElementById('exerciseTable').getElementsByTagName('tbody')[0];
        //     let rowCount = table.rows.length;
        //     let row = table.insertRow(rowCount);
        //     row.innerHTML = `
        //         <td><input type="text" name="exercises[${rowCount}][exercise_name]" class="form-control" required></td>
        //         <td><input type="number" name="exercises[${rowCount}][sets]" class="form-control" required></td>
        //         <td><input type="number" name="exercises[${rowCount}][reps]" class="form-control" required></td>
        //         <td><input type="number" name="exercises[${rowCount}][weight]" class="form-control"></td>
        //         <td><button type="button" class="btn btn-danger btn-sm removeExercise">X</button></td>
        //     `;
        // });

        // document.addEventListener('click', function(event) {
        //     if (event.target.classList.contains('removeExercise')) {
        //         event.target.closest('tr').remove();
        //     }
        // });
    </script>
@endpush