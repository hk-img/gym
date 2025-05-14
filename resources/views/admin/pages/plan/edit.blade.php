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
                        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.plan.index') }}">List</a></li> -->
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                </div>
                <div class="col d-flex justify-content-end align-items-center">
                    <a href="{{route('admin.plan.index')}}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                </div>
            </div>
            
        </div>
        <!-- /Page Header -->

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Edit Plan</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('admin.plan.update', $data->id) }}" enctype="multipart/form-data" id="myForm">
                            @csrf
                            @method('patch')
                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $data->name) }}" placeholder="Enter Plan Name" onkeypress="return onlyLetters(event)">
                                    @error('name') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                </div>
                                
                                <!-- Duration -->
                                <div class="col-md-6">
                                    <label class="form-label">Duration (in days) <span class="text-danger">*</span></label>
                                    <input type="text" name="duration" class="form-control" placeholder="Enter Duration (in days)" onkeypress="return onlyNumbers(event)" maxlength="5" value="{{ old('duration', $data->duration) }}">
                                    @error('duration') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                </div>
                            </div>
                            
                            {{-- <div class="row g-3 mt-2">
                                <!-- Status -->
                                <div class="col-md-6">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status">
                                        <option value="1" {{ old('status', $data->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $data->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div> --}}

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Price <span class="text-danger">*</span></label>
                                    <input type="text" name="price" class="form-control" placeholder="Enter Price" onkeypress="return onlyNumbers(event)" value="{{ old('price', $data->price) }}">
                                    @error('price') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror

                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary px-4">Update</button>
                                {{-- <button type="button" class="btn btn-secondary px-4" onclick="resetForm()">Reset</button> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection