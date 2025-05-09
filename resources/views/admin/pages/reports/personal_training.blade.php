

<style>
    .selectuser{
    height: 50px;
    padding: 9px 10px 6px;!important
}
</style>

@extends('admin.layouts.app')
@section('page_title', 'Report Manager | Personal Training')
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
                        <h3 class="page-title">Personal Training</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Personal Training</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <!-- Filter Row -->
            <div class="row gx-3 align-items-end">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" id="monthFilter" class="form-control" placeholder="Select Month & Year" readonly>
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-focus">
                        <select id="trainerId" class="form-control userType" style="height: 50px; padding: 9px 10px 6px;">
                            <option value="" selected disabled>Select Trainer</option>
                            @if(!empty($trainer))
                                @foreach($trainer as $val)
                                    <option value="{{$val->id}}">{{ ucfirst($val->name) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-focus">
                        <select id="userId" class="form-control selectuser userType" style="height: 50px; padding: 9px 10px 6px;">
                            <option value="" selected disabled>Select User</option>
                            @if(!empty($users))
                                @foreach($users as $val)
                                    <option value="{{$val->id}}">{{ ucfirst($val->name) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-focus">
                        <select id="statusType" class="form-control" style="height: 50px; padding: 9px 10px 6px;">
                            <option value="" selected disabled>Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Expired">Expired</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 d-flex gap-2 mt-4">
                    <a href="javascript:void(0);" class="btn btn-success btn-search text-capitalize w-50">Search</a>
                    <button class="btn btn-danger btn-clear text-capitalize w-50">Clear</button>
                </div>
            </div>

            <!-- /Filter Row -->

            <!-- Table -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-0">Personal Training List</h4>
                                <h4 id="hideRevenue"><strong>Trainer Revenue of the Month:</strong> ₹ <span id="monthlyRevenue">0</span></h4>
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
                                            <th>Trainer Name</th>
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

        $('#statusType').change(function() {
            var status = $(this).val();
            if(status == 'Expired'){
                $('#hideRevenue').hide();
            }else{
                $('#hideRevenue').show();
            }
        })
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
            let selectedTrainerId = $('#trainerId').val(); // Get selected month
            let selectedUserId = $('#userId').val(); // Get selected month
            let statusType = $('#statusType').val(); // Get selected month
            if (selectedMonth || selectedTrainerId || selectedUserId || statusType) {
                fetchMonthlyRevenue(selectedMonth,selectedTrainerId,selectedUserId); // Fetch revenue
                loadDataTable(selectedMonth,selectedTrainerId,selectedUserId,statusType); // Reload DataTable with new filter
            } else {
                alert('Please select a month!');
            }
        });

        // Handle Clear Button Click
        $('.btn-clear').on('click', function () {
            $('#monthFilter').val(''); // Clear input
            $('#trainerId').val('Select Trainer'); // Clear input
            $('#userId').val('Select User'); // Clear input
            $('#statusType').val('Select Status'); // Clear input
            $('#hideRevenue').show();
            fetchMonthlyRevenue(currentMonth); // Reset to current month
            loadDataTable(currentMonth); // Reset table data
        });

        function fetchMonthlyRevenue(month,trainerId,userId) {
            $.ajax({
                url: "{{ route('admin.reports.pt-revenue') }}",
                type: "GET",
                data: { month: month,trainerId:trainerId ,user_id:userId},
                success: function (response) {
                    $('#monthlyRevenue').text(response.revenue);
                },
                error: function () {
                    alert('Failed to fetch revenue');
                }
            });
        }

        function loadDataTable(month,trainerId,userId,status) {
            if ($.fn.DataTable.isDataTable('.datatable')) {
                $('.datatable').DataTable().destroy(); // Destroy previous instance
            }

            $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reports.pt') }}",
                    data: { month: month ,trainerId:trainerId,user_id:userId,status:status}
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'created_at_formatted', name: 'created_at' },
                    { data: 'member_name', name: 'member_name' },
                    { data: 'trainer_name', name: 'trainer_name' },
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
