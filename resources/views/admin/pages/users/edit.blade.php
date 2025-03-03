@extends('admin.layouts.app')
@section('page_title', 'Member Manager | Edit')
@section('content')
<div class="page-wrapper">

    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col">
                    <h3 class="page-title">Member Manager</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">List</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                </div>
                <div class="col d-flex justify-content-end align-items-center">
                    <a href="{{route('admin.users.index')}}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                </div>
            </div>
            
        </div>
        <!-- /Page Header -->

      
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Member Edit Form</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.update', $data->id) }}" enctype="multipart/form-data"  id="myForm">
                            @csrf
                            @method('patch')
                            <!-- Name -->
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Name
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="name" onkeypress="return onlyLetters(event)"
                                        value="{{ old('name', $data->name) }}" id="name"  placeholder="Enter Member Name">
                                    @error('name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Phone
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-10">
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter Mob. Number" onkeypress="return onlyNumbers(event)"
                                        value="{{ old('phone', $data->phone) }}" maxLength="10">
                                    @error('phone') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Address<span class="text-danger"> *</span></label>
                                <div class="col-md-10">
                                    <textarea rows="5" cols="5" class="form-control " name="address" placeholder="Enter Full Address" >{{ old('address', $data->address) }}</textarea>
                                    @error('address') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            
                            <!-- Image -->
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Image<span class="text-danger"> *</span></label>
                                <div class="col-md-10">
                                    <div class="custom-file-container" data-upload-id="myFirstImage">
                                        <label>Upload (Single File) <a href="javascript:void(0)" class="custom-file-container__image-clear"
                                                title="Clear Image">x</a></label>
                                        <label class="custom-file-container__custom-file">
                                            <input type="file" class="custom-file-container__custom-file__custom-file-input"
                                                name="image" accept="image/*">
                                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                                        </label>
                                        <div class="custom-file-container__image-preview"></div>
                                    </div>
                                    @error('image') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            
                            <!-- Image Preview -->
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Preview</label>
                                <div class="col-md-10 d-flex flex-wrap gap-3">
                                    <div class="image-container position-relative">
                                        <img src="{{ $data->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/placeholder.jpg') }}" width="150" height="150" class="img-thumbnail">
                                    </div>
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