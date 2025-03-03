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
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">List</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.users.index') }}"><button type="button"
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
                            <form action="{{ route('admin.users.store') }}" method="post" id="myForm" enctype="multipart/form-data">
                                @csrf
                                <!-- Name -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name') }}" id="name"  placeholder="Enter Member Name" onkeypress="return onlyLetters(event)">
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
                                            value="{{ old('phone') }}" maxLength="10">
                                        @error('phone') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>
                                
                                <!-- Address -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Address<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <textarea rows="5" cols="5" class="form-control " name="address" placeholder="Enter Full Address" >{{ old('address') }}</textarea>
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

