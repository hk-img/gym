@extends('admin.layouts.app')
@section('page_title', 'Category | Add')
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Category</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.category.index') }}">List</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.category.index') }}"><button type="button"
                                class="btn btn-primary me-2">Back</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Category</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.category.store') }}" method="post" id="myForm" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="row g-3">
                                    <!-- Name -->
                                    <div class="col-md-12">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" placeholder="Enter Title" onkeypress="return onlyLetters(event)">
                                        @error('title') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
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
        function resetForm() {
            document.getElementById('myForm').reset();
        }
        
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
@endpush

