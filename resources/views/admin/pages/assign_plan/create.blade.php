@extends('admin.layouts.app')
@section('page_title', 'Assgin Plan | Add')
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Assgin Plan</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.assign-plan.index') }}">List</a></li>
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

            <!-- Form -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Assign Plan Form</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.assign-plan.store') }}" method="post" id="myForm" enctype="multipart/form-data">
                                @csrf
                                <!-- User -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">User<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="userList form-control" name="user_id" id="userSelect">
                                        </select>
                                        @error('user_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <!-- User Info -->
                                <div id="userInfo" class="mb-3 row" style="display: none;">
                                    <label class="col-form-label col-md-2">User Info</label>
                                    <div class="col-md-10">
                                        {{-- <p><strong>Email:</strong> <span id="userEmail"></span></p> --}}
                                        <p><strong>Phone:</strong> <span id="userPhone"></span></p>
                                        <p><strong>Status:</strong> <span id="userStatus"></span></p>
                                    </div>
                                </div>


                                <!-- Select Plan -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Plan<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="planList form-control" name="plan_id">
                                            {{-- <option value="" disabled selected>Select Plan</option>
                                            <option value="1">Plan A</option>
                                            <option value="2">Plan A</option>
                                            <option value="3">Plan B</option>
                                            <option value="4">Plan C</option> --}}
                                        </select>
                                        @error('plan_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <!-- User Type -->
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">User Type<span class="text-danger"> *</span></label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="user_type">
                                            <option value="" disabled selected>Select User Type</option>
                                            <option value="new">New</option>
                                            <option value="old">Old</option>
                                        </select>
                                        @error('plan_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
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
                                    <label class="col-form-label col-md-2">UTR</label>
                                    <div class="col-md-10">
                                        <input type="text" name="utr" id="utr" class="form-control" placeholder="Enter UTR Number"
                                            value="{{ old('utr') }}" maxLength="10">
                                        @error('utr') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                
                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary me-2" value="submit">Save</button>
                                <button type="button" class="btn btn-light" onclick="resetForm()">Reset</button>

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
                            {{-- $('#userEmail').text(user.email); --}}
                            $('#userPhone').text(user.phone);
                            $('#userStatus').text(user.status);
                            $('#userInfo').show();
                        }
                    });
                } else {
                    $('#userInfo').hide();
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
    });
    </script>
@endpush

