@extends('admin.layouts.app')
@section('page_title', 'Assgin Plan | Add')
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Assign Plan</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <!-- <li class="breadcrumb-item"><a href="{{ route('admin.assign-plan.index') }}">List</a></li> -->
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.assign-plan.index') }}"><button type="button"
                                class="btn btn-primary me-2">Back</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            @if (session('status_error'))
                <div class="alert alert-danger">
                    {{ session('status_error') }}
                </div>
            @endif
            <!-- Form -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Assign Plan Form</h4>
                        </div>
                        <div class="card-body">
                            <form id="assignForm" action="{{ route('admin.assign-plan.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <!-- User -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">User<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="userList form-control" name="user_id" id="userSelect">
                                            <option value="{{ $user->id ?? '' }}" selected>{{ $user->name ?? 'Select a User' }}</option>
                                        </select>
                                        @error('user_id') 
                                            <p class="text-danger text-xs pt-1"> {{$message}} </p>
                                        @enderror
                                    </div>
                                </div>
                                

                                <!-- User Info -->
                                <div id="userInfo" class="mb-3 row" style="display: none;">
                                    <label class="col-form-label col-md-2"></label>
                                    <div class="col-md-10">
                                        <div class="d-flex gap-4">
                                            <p><strong>Phone:</strong> <span id="userPhone"></span></p>
                                            <p><strong>Status:</strong> <span id="userStatus"></span></p>
                                        </div>
                                    </div>
                                </div>


                                <!-- Select Plan -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Plan<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="planList form-control" name="plan_id" id="planSelect">
                                        </select>
                                        @error('plan_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <div class="input-block mb-3 row" >
                                    <label class="col-form-label col-md-2">Discount<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <input type="text" id="discount" class="form-control" placeholder="Enter the discount" name="discount" onkeypress="return onlyNumbers(event)" value="{{old('discount')}}">

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
                                        <input type="text" name="received_amt" id="received_amt"
                                            class="form-control" placeholder="Enter Received Amount"
                                            value="{{ old('received_amt') }}" maxLength="10"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                                        @error('received_amt') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>


                                <!-- Plan Info -->
                                {{-- <div id="planInfo" class="mb-3 row" style="display: none;">
                                    <label class="col-form-label col-md-2"></label>
                                    <div class="col-md-10">
                                        <div class="d-flex gap-4">
                                            <p><strong>Name:</strong> <span id="planName"></span></p>
                                            <p><strong>Duration:</strong> <span id="planDuration"></span></p>
                                            <p><strong>Price:</strong> <span id="planPrice"></span></p>
                                        </div>
                                    </div>
                                </div> --}}

                                <!-- User Type -->
                                {{-- <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">User Type<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="user_type">
                                            <option value="" disabled selected>Select User Type</option>
                                            <option value="new">New</option>
                                            <option value="old">Old</option>
                                        </select>
                                        @error('plan_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div> --}}

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
            <!-- /Form -->
        </div>
    </div>

@endsection
@push('custom-script')
    <script>
        function resetForm() {
            document.getElementById('myForm').reset();
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeSelect2('.userList', "{{ route('admin.option.userlist') }}", 'Select User');
            initializeSelect2('.planList', "{{ route('admin.option.planlist') }}", 'Select Plan');
        });

        $(document).ready(function () {
            // Fetch user details when a user is selected
            $('#userSelect').change(function () {
                let userId = $(this).val();
                var baseUrl = "{{ route('admin.users.info', ':userId') }}";
                var url  = baseUrl.replace(':userId',userId);
                if (userId) {
                    $.ajax({
                        url: url,
                        type: "GET",
                        data: { id: userId },
                        success: function (user) {
                            $('#userPhone').text(user.phone);
                            $('#userStatus').text(user.status);
                            $('#userInfo').show();
                        }
                    });
                } else {
                    $('#userInfo').hide();
                }
            });

            // Fetch plan details when a plan is selected
            $('#planSelect').change(function () {
                let planId = $(this).val();
                var baseUrl = "{{ route('admin.plan.info', ':planId') }}";
                var url  = baseUrl.replace(':planId',planId);
                if (planId) {
                    $.ajax({
                        url: url,
                        type: "GET",
                        data: { id: planId },
                        success: function (plan) {
                            $('#planName').text(plan.name);
                            $('#planDuration').text(plan.duration);
                            $('#planPrice').text(plan.price);
                            $('#planInfo').show();
                        }
                    });
                } else {
                    $('#planInfo').hide();
                }
            });

            // Show UTR field only when "Online" is selected
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

    </script>
@endpush

