@extends('admin.layouts.app')
@section('page_title', 'Assign Plan | List')
@push('custom-style')
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
@endpush
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Assign Plan Manager</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Filter Row -->
            <div class="row filter-row">
                <!-- Date Range Filter -->
                <div class="col-md-6 col-md-3">
                    <div class="input-group mb-3">
                        <input type="text" id="dateFilter" class="form-control date_range" placeholder="Select Date Range">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <!-- User Type -->
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus select-focus">
                        <select class="form-control userType">
                            <option selected disabled>Select User Type</option>
                            <option value="new">New</option>
                            <option value="old">Old</option>
                        </select>
                        <label class="focus-label">User Type</label>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus select-focus">
                        <select class="form-control paymentMethod">
                            <option selected disabled>Select Payment Method</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                        <label class="focus-label">Payment Method</label>
                    </div>
                </div>

                <!-- Membership Status -->
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus select-focus">
                        <select class="form-control membershipStatus">
                            <option selected disabled>Select Membership Status</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                        </select>
                        <label class="focus-label">Membership Status</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 d-flex gap-3">
                    <div class="d-grid h-25">
                        <a href="javacript:void(0);" class="btn btn-success btn-search text-capitalize">Search</a>
                    </div>
                     <div class="d-grid h-25">
                        <button class="btn btn-danger btn-clear text-capitalize">Clear</button>
                    </div>
                </div>
            </div>
            <!-- /Filter Row -->

            <!-- Table -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card mb-0">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Assigned Plan List</h4>
                            <div class="col-auto float-end ms-auto">
                                <a href="{{route('admin.assign-plan.create')}}" class="btn btn-sm add-btn"><i class="fa fa-plus"></i> Assign Plan</a>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="datatable table table-stripped mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Date & Time</th>
                                            <th>User Type</th>
                                            <th>Member Name</th>
                                            <th>Plan</th>
                                            <th>Duration (in days)</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Payment Method</th>
                                            <th>UTR</th>
                                            <th>Discount</th>
                                            <th>Membership Status</th>
                                            {{-- <th>Status</th> --}}
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table -->
        </div>
    </div>

@endsection
@push('custom-script')
    <!-- Moment.js (Required for Date Range Picker) -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- Date Range Picker JS -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        // Initialize Date Range Picker
        $('#dateFilter').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#dateFilter').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#dateFilter').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
        
        const userColumns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, // Iteration column
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'user_type', name: 'user_type' },
            { data: 'member_name', name: 'member_name' },
            { data: 'plan', name: 'plan' },
            { data: 'days', name: 'days' },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'utr', name: 'utr' },
            { data: 'discount', name: 'discount'},
            { data: 'membership_status', name: 'membership_status' },
            {{-- { data: 'status', name: 'status', orderable: false, searchable: false }, --}}
            {{-- { data: 'action', name: 'action', orderable: false, searchable: false }, --}}
        ];

        const filterSelectors = [
            { name: 'date_range', selector: '.date_range'},
            { name: 'user_type', selector: '.userType'},
            { name: 'payment_method', selector: '.paymentMethod'},
            { name: 'membership_status', selector: '.membershipStatus'},
        ];

        document.addEventListener('DOMContentLoaded', function() {
            initializeDataTable("{{ route('admin.assign-plan.index') }}",filterSelectors, userColumns);
        });
    </script>
@endpush
