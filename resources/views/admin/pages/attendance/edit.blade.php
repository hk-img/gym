@extends('admin.layouts.app')
@section('page_title', 'Attendance | Edit')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Edit Attendance</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <!-- <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">List</a></li> -->
                            <li class="breadcrumb-item active">Edit</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.attendance.index') }}">
                            <button type="button" class="btn btn-primary me-2">Back</button>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Edit Attendance</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="post" id="attendanceForm">
                                @csrf
                                @method('PATCH')

                                <div class="row g-3">
                                    <!-- Member Selection -->
                                    <div class="col-md-6">
                                        <label class="form-label">Member <span class="text-danger">*</span></label>
                                        <select class="userList form-control" name="member_id" required>
                                            <option value="{{ $attendance->user_id }}" selected>{{ $attendance->user->name }}</option>
                                        </select>
                                        @error('member_id') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                    </div>

                                    <!-- Date -->
                                    <div class="col-md-6">
                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" value="{{ $attendance->date }}" required>
                                        @error('date') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <!-- Time In -->
                                    <div class="col-md-6">
                                        <label class="form-label">Time In <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="time_in" value="{{ $attendance->time_in }}" required>
                                        @error('time_in') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                    </div>

                                    <!-- Time Out -->
                                    <div class="col-md-6">
                                        <label class="form-label">Time Out <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="time_out" value="{{ $attendance->time_out }}">
                                        @error('time_out') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                    </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            initializeSelect2('.userList', "{{ route('admin.option.userlist') }}", 'Select User');
        });

        function resetForm() {
            document.getElementById('attendanceForm').reset();
        }
    </script>
@endpush
