@extends('admin.layouts.app')
@section('page_title', 'Member Manager | Add')
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Member Manager</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.plan.index') }}">List</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.plan.index') }}"><button type="button"
                                class="btn btn-primary me-2">Back</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->


            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Member Registration Form</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.plan.store') }}" method="post" id="myForm" enctype="multipart/form-data">
                                @csrf
                                <!--Plan Name -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Plan Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name') }}" id="name"  placeholder="Enter Plan Name">
                                        @error('name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <!-- duration -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Duration
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" name="duration" id="duration" class="form-control" placeholder="Enter Duration"
                                            value="{{ old('duration') }}">
                                        @error('duration') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <div class="input-block mb-3 row">
                                  <label class="col-form-label col-md-2">Status
                                    <span class="text-danger">*</span>
                                  </label>
                                  <div class="col-md-10">
                                    <select class="form-control" name="status" id="status">
                                      <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active
                                      </option>
                                      <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive
                                      </option>
                                    </select>
                                    @error('status') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                                </div>
                                

                                <button type="submit" class="btn btn-primary me-2" value="submit">Save</button>
                                <button type="button" class="btn btn-light" onclick="resetForm()">Reset</button>

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
        function resetForm() {
            document.getElementById('myForm').reset();
        }
    </script>
@endpush

