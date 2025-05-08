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

      
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Trainers Registration</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.trainers.update', $data->id) }}" method="post" id="myForm" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name',$data->name) }}" placeholder="Enter Trainer Name" onkeypress="return onlyLetters(event)">
                                    @error('name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" placeholder="Enter Mobile Number" onkeypress="return onlyNumbers(event)" value="{{ old('phone',$data->phone) }}" maxLength="10">
                                    @error('phone') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-2">

                                <div class="col-md-6">
                                    <label class="form-label">Experience <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="experience" placeholder="Enter Experience" value="{{ old('experience',$data->experience) }}">
                                    @error('experience') <p class="text-danger text-xs pt-1"> {{$message}} </p> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Salary <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="salary" placeholder="Enter Salary" value="{{ old('salary',$data->salary) }}">
                                    @error('salary') <p class="text-danger text-xs pt-1"> {{$message}} </p> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">PT Fees (per month) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="pt_fees" placeholder="Enter PT Fees" value="{{ old('pt_fees',$data->pt_fees) }}">
                                    @error('pt_fees') <p class="text-danger text-xs pt-1"> {{$message}} </p> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="start_date" value="{{ old('start_date',$data->start_date) }}">
                                    @error('start_date') <p class="text-danger text-xs pt-1"> {{$message}} </p> @enderror
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

