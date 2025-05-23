@extends('admin.layouts.app')
@section('page_title', 'Activity Assign | Add')
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Activity Assign</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activity-assign-list') }}">List</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.activity-assign-list') }}"><button type="button"
                                class="btn btn-primary me-2">Assign List</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Assign</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.assign-store') }}" method="post" id="myForm" enctype="multipart/form-data">
                                @csrf
                                <!-- User -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">User<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="userList form-control" name="user_id" id="userSelect">
                                            
                                        </select>
                                        @error('user_id') 
                                            <p class="text-danger text-xs pt-1"> {{$message}} </p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Select Plan -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Package<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="planList form-control" name="package_id" id="planSelect">
                                        </select>
                                        @error('package_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">Months <span class="text-danger">*</span></label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="duration" placeholder="Enter Month" oninput="validateMonth(this)">
                                        @error('duration')
                                            <p class="text-danger text-xs pt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="input-block mb-3 row" >
                                    <label class="col-form-label col-md-2">Discount<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <input type="text" id="discount" class="form-control" placeholder="Enter the discount" name="discount" onkeypress="return onlyNumbers(event)" value="{{old('discount')}}">
                                        @error('discount')
                                            <p class="text-danger text-xs pt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Payment Type -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Payment Type<span class="text-danger"> *</span></label>
                                    <div class="col-md-10 pt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_type" id="paymentTypeFull" value="full" checked>
                                            <label class="form-check-label" for="paymentTypeFull">Full Payment</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_type" id="paymentTypePartial" value="partial" >
                                            <label class="form-check-label" for="paymentTypePartial">Partial Payment</label>
                                        </div>
                                        @error('payment_type') 
                                            <p class="text-danger text-xs pt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>


                                 <div class="input-block mb-3 row" id="received_amtField" style="display: {{ $errors->has('received_amt') ? '' : 'none' }};">
                                    <label class="col-form-label col-md-2">Received Amount<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <input type="text" name="received_amt" id="received_amt" class="form-control" placeholder="Enter Received Amount"
                                            value="{{ old('received_amt') }}" maxLength="10">
                                        @error('received_amt') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Payment Method<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="payment_method" id="paymentMethod">
                                            <option value="" disabled selected>Select Payment Method</option>
                                            <option value="online">Online</option>
                                            <option value="offline">Offline</option>
                                        </select>
                                        @error('payment_method') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <!-- UTR -->
                                <div class="input-block mb-3 row" id="utrField" style="display: {{ $errors->has('utr') ? '' : 'none' }};">
                                    <label class="col-form-label col-md-2">UTR<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <input type="text" name="utr" id="utr" class="form-control" placeholder="Enter UTR Number"
                                            value="{{ old('utr') }}" maxLength="10">
                                        @error('utr') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary me-2" value="submit">Save</button>
                                <button type="button" class="btn btn-secondary px-4" onclick="resetForm()">Reset</button>

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

        // Show UTR field only when "Online" is selected
        $(document).ready(function () {
            $('#paymentMethod').change(function () {
                if ($(this).val() === "online") {
                    $('#utrField').show();
                } else {
                    $('#utrField').hide();
                }
            });

            $('input[name="payment_type"]').change(function () {
                if ($(this).val() === "partial") {
                    $('#received_amtField').show();
                } else {
                    $('#received_amtField').hide();
                }
            });
            
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeSelect2('.userList', "{{ route('admin.option.userlist') }}", 'Select User');
            initializeSelect2('.planList', "{{ route('admin.option.packagelist') }}", 'Select Package');
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

