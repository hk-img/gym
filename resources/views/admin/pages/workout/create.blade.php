@extends('admin.layouts.app')
@section('page_title', 'Workout Manager | Add')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            {{-- @dd($members); --}}
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Workout Manager</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <!-- <li class="breadcrumb-item"><a href="{{ route('admin.workout.index') }}">List</a></li> -->
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.workout.index') }}"><button type="button"
                                class="btn btn-primary me-2">Back</button></a>
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
                                        <select class="userList form-control extract-data" name="member_id" required>

                                        </select>

                                        @error('member_id')
                                            <p class="text-danger text-xs pt-1"> {{ $message }} </p>
                                        @enderror

                                    </div>

                                </div>

                                <!-- Dynamic Exercise Fields -->
                                <div class="mt-4">
                                    <h5>Exercises</h5>
                                    <table class="table" id="exercise-Table">
                                        <thead>
                                            <tr>
                                                <th>Day</th>
                                                <th>Exercise Name</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="exerciseTable">
                                            <tr>
                                                <td>Monday</td>
                                                <input type="hidden" name="exercises[0][days]" value="Monday"
                                                    class="form-control" required>
                                                <td><input type="text" name="exercises[0][exercise_name]"
                                                        class="form-control" required></td>
                                                <td>
                                                    <textarea name="exercises[0][description]" class="form-control" required></textarea>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Tuesday</td>
                                                <input type="hidden" name="exercises[1][days]" value="Tuesday"
                                                    class="form-control" required>
                                                <td><input type="text" name="exercises[1][exercise_name]"
                                                        class="form-control" required></td>
                                                <td>
                                                    <textarea name="exercises[1][description]" class="form-control" required></textarea>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Wednesday</td>
                                                <input type="hidden" name="exercises[2][days]" value="Wednesday"
                                                    class="form-control" required>
                                                <td><input type="text" name="exercises[2][exercise_name]"
                                                        class="form-control" required></td>
                                                <td>
                                                    <textarea name="exercises[2][description]" class="form-control" required></textarea>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Thursday</td>
                                                <input type="hidden" name="exercises[3][days]" value="Thursday"
                                                    class="form-control" required>
                                                <td><input type="text" name="exercises[3][exercise_name]"
                                                        class="form-control" required></td>
                                                <td>
                                                    <textarea name="exercises[3][description]" class="form-control" required></textarea>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Friday</td>
                                                <input type="hidden" name="exercises[4][days]" value="Friday"
                                                    class="form-control" required>
                                                <td><input type="text" name="exercises[4][exercise_name]"
                                                        class="form-control" required></td>
                                                <td>
                                                    <textarea name="exercises[4][description]" class="form-control" required></textarea>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Saturday</td>
                                                <input type="hidden" name="exercises[5][days]" value="Saturday"
                                                    class="form-control" required>
                                                <td><input type="text" name="exercises[5][exercise_name]"
                                                        class="form-control" required></td>
                                                <td>
                                                    <textarea name="exercises[5][description]" class="form-control" required></textarea>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary px-4">Save</button>
                                    <button type="button" class="btn btn-secondary px-4"
                                        onclick="resetForm()">Reset</button>
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
    </script>


    <script>
        $(document).ready(function() {
            $(".extract-data").change(function() {
                var userId = $(this).val();
                $.ajax({
                    url: "{{ route('admin.workout.getdata') }}",
                    data: {
                        member_id: userId,
                    },
                    success: function(data) {
                        var exercises = data.exercise?.exercises || [];
                        if (exercises.length > 0) {
                            // alert('not empty');

                            var tableBody = '';

                            exercises.forEach(function(exercise, index) {
                                tableBody += `
                            <tr>
                                <td>${exercise.days}</td>
                                <input type="hidden" name="exercises[${index}][days]" value="${exercise.days}" class="form-control" required>
                                <td><input type="text" name="exercises[${index}][exercise_name]" class="form-control" value="${exercise.exercise_name}" required></td>
                                <td><textarea name="exercises[${index}][description]" class="form-control" required>${exercise.description}</textarea></td>
                            </tr>`;
                            });

                            $('#exerciseTable').html(tableBody);
                        } else {
                            // alert('empty');
                            $('input[name$="[exercise_name]"]').val('');
                            $('textarea[name$="[description]"]').text('');
                        }

                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endpush
