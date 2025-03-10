@extends('admin.layouts.app')
@section('page_title', 'Gym Manager | Edit')
@section('content')
<div class="page-wrapper">

    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col">
                    <h3 class="page-title">Gym Manager</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.gym.index') }}">List</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                </div>
                <div class="col d-flex justify-content-end align-items-center">
                    <a href="{{route('admin.gym.index')}}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                </div>
            </div>
            
        </div>
        <!-- /Page Header -->

      
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Edit Gym Info</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('admin.gym.update', $data->id) }}" enctype="multipart/form-data" id="myForm">
                            @csrf
                            @method('patch')
                            
                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $data->name) }}" placeholder="Enter Name" onkeypress="return onlyLetters(event)">
                                    @error('name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" placeholder="Enter Mobile Number" onkeypress="return onlyNumbers(event)" value="{{ old('phone', $data->phone) }}" maxLength="10">
                                    @error('phone') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="email" placeholder="Enter Email" value="{{ old('email', $data->email)}}"/>
                                    @error('email') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
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

