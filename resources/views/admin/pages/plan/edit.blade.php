@extends('admin.layouts.app')
@section('page_title', 'Plan Manager | Edit')
@section('content')
<div class="page-wrapper">

    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col">
                    <h3 class="page-title">Plan Manager</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.plan.index') }}">List</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                </div>
                <div class="col d-flex justify-content-end align-items-center">
                    <a href="{{route('admin.plan.index')}}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                </div>
            </div>
            
        </div>
        <!-- /Page Header -->

      
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Plan Edit Form</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.plan.update', $data->id) }}" enctype="multipart/form-data"  id="myForm">
                            @csrf
                            @method('patch')
                            <!-- Name -->
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Name
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="name"
                                        value="{{ old('name', $data->name) }}" id="name"  placeholder="Enter Member Name" onkeypress="return onlyLetters(event)">
                                    @error('name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>

                            <!-- Duration -->
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Duration (in days)
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-10">
                                    <input type="text" name="duration" id="duration" class="form-control" placeholder="Enter Duration (in days)"  onkeypress="return onlyNumbers(event)"  maxlength="5"
                                        value="{{ old('duration', $data->duration) }}" maxLength="10">
                                    @error('duration') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>

                            <!--Status-->
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Status
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-10">
                                    <select class="form-control" name="status">
                                        <option value="1" {{ old('status', $data->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $data->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>

                                </div>
                            </div>
                        
                            
                            <!-- Update Button -->
                            <button type="submit" class="btn btn-primary me-2">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection