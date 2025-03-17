@extends('admin.layouts.app')
@section('page_title', 'Report Manager | Membership Renewal')
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
                        <h3 class="page-title">Membership Renewal</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Membership Renewal</li>
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
                        <a href="#" class="btn btn-success btn-search text-capitalize">Search</a>
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
                                <h4 class="card-title mb-0">Membership Renewal List</h4>
                                <h4><strong>Total Revenue of the Month:</strong> ₹ <span id="monthlyRevenue">0</span></h4>
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
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Days Remaining</th>
                                            <th>Status</th>
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
        fetchMonthlyRevenue(currentMonth);
        loadDataTable(currentMonth);

        // Handle Search Button Click
        $('.btn-search').on('click', function () {
            let selectedMonth = $('#monthFilter').val(); // Get selected month
            if (selectedMonth) {
                fetchMonthlyRevenue(selectedMonth); // Fetch revenue
                loadDataTable(selectedMonth); // Reload DataTable with new filter
            } else {
                alert('Please select a month!');
            }
        });

        // Handle Clear Button Click
        $('.btn-clear').on('click', function () {
            $('#monthFilter').val(''); // Clear input
            fetchMonthlyRevenue(currentMonth); // Reset to current month
            loadDataTable(currentMonth); // Reset table data
        });

        function fetchMonthlyRevenue(month) {
            $.ajax({
                url: "{{ route('admin.reports.revenue') }}",
                type: "GET",
                data: { month: month },
                success: function (response) {
                    $('#monthlyRevenue').text(response.revenue);
                },
                error: function () {
                    alert('Failed to fetch revenue');
                }
            });
        }

        function loadDataTable(month) {
            if ($.fn.DataTable.isDataTable('.datatable')) {
                $('.datatable').DataTable().destroy(); // Destroy previous instance
            }

            $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reports.renewals') }}",
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
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date_formatted', name: 'end_date' },
                    { data: 'days_remaining', name: 'days_remaining' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                ]
            });
        }
    });

    </script>
@endpush
