@extends('admin.layouts.app')
@section('page_title', 'Report Manager | Membership Expired')
@push('custom-style')
<!-- Bootstrap Datepicker JS & CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endpush
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Membership Expired</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Membership Expired</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Filter Row -->
            <div class="row filter-row">
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <input type="text" id="monthFilter" class="form-control" placeholder="Select Month & Year" readonly>
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
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
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-0">Membership Expired List</h4>
                                {{-- <h4><strong>Total Revenue of the Month:</strong> ₹ <span id="monthlyRevenue">0</span></h4> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="datatable table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Date & Time</th>
                                            <th>Member Name</th>
                                            <th>Plan</th>
                                            <th>SubTotal</th>
                                            <th>Discount</th>
                                            <th>Net Amount</th>
                                            <th>Membership Expired On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
    $(document).ready(function () {
        // Initialize Month & Year Picker
        $('#monthFilter').datepicker({
            format: "yyyy-mm", // Year-Month format
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

        // Set default revenue for the current month on page load
        let currentMonth = moment().format('YYYY-MM');
        loadDataTable(currentMonth);

        // Handle Search Button Click
        $('.btn-search').on('click', function () {
            let selectedMonth = $('#monthFilter').val(); // Get selected month
            if (selectedMonth) {
                loadDataTable(selectedMonth); // Reload DataTable with new filter
            } else {
                alert('Please select a month!');
            }
        });

        // Handle Clear Button Click
        $('.btn-clear').on('click', function () {
            $('#monthFilter').val(''); // Clear input
            loadDataTable(currentMonth); // Reset table data
        });

        function loadDataTable(month) {
            if ($.fn.DataTable.isDataTable('.datatable')) {
                $('.datatable').DataTable().destroy(); // Destroy previous instance
            }

            $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reports.expired') }}",
                    data: { month: month }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'created_at_formatted', name: 'created_at' },
                    { data: 'member_name', name: 'member_name' },
                    { data: 'plan', name: 'plan' },
                    { data: 'price', name: 'price' },
                    {data: 'discount', name: 'discount'},
                    {data: 'netamount', name: 'netamount'},
                    { data: 'end_date_formatted', name: 'end_date' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        }
    });

    </script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.assign-plan-btn', function () {
                let userId = $(this).data('user-id'); 
                window.location.href = "{{ route('admin.assign-plan.create') }}?user=" + userId;
            });
        });
    </script>
@endpush
