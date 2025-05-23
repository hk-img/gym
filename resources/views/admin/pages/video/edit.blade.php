@extends('admin.layouts.app')
@section('page_title', 'Video | Edit')
@section('content')
<div class="page-wrapper">

    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col">
                    <h3 class="page-title">Video</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <!-- <li class="breadcrumb-item"><a href="{{ route('admin.video.index') }}">List</a></li> -->
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                </div>
                <div class="col d-flex justify-content-end align-items-center">
                    <a href="{{route('admin.video.index')}}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                </div>
            </div>
            
        </div>
        <!-- /Page Header -->

      
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Edit Member</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('admin.video.update', $data->id) }}" enctype="multipart/form-data" id="myForm">
                            @csrf
                            @method('patch')
                            
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="categoryList form-control" name="category_id" id="categorySelect">
                                        @if($data && $data->category)
                                            <option value="{{ $data->category_id }}" selected>{{ $data->category->title }}</option>
                                        @endif
                                    </select>
                                    @error('category_id') 
                                        <p class="text-danger text-xs pt-1"> {{$message}} </p>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title',$data->title) }}" placeholder="Enter Title" onkeypress="return onlyLetters(event)">
                                    @error('title') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label">Link <span class="text-danger">*</span></label>
                                    <input type="text" value="{{ old('link',$data->link) }}" name="link" class="form-control" placeholder="Enter Link" >
                                    @error('link') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
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
@push('custom-script')
    <script> 
        document.addEventListener('DOMContentLoaded', function() {
            initializeSelect2('.categoryList', "{{ route('admin.option.categoryList') }}", 'Select Category');
        });

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

