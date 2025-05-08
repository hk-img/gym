@extends('admin.layouts.app')
@section('page_title', 'Workout Manager | List')
@push('custom-style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endpush
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Workout Manager</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Workout Manager</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filter Row -->
            {{-- <div class="row filter-row">
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
            </div> --}}
            <!-- /Filter Row -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card mb-0">
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title mb-0">Workout List</h4>
                            <div class="col-auto float-end ms-auto">
                                <a href="{{route('admin.workout.create')}}" class="btn btn-sm add-btn"><i class="fa fa-plus"></i> Add / Edit Workout</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="datatable table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Date</th>
                                            <th>Member Name</th>
                                            {{-- <th>Workout Name</th> --}}
                                            {{-- <th>Total Exercises</th> --}}
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
    loadDataTable();

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
        loadDataTable(); // Reset table data
    });

    function loadDataTable(month = null) {
        if ($.fn.DataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable().destroy();
        }

        $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.workout.index') }}",
                data: { month: month }

            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'created_at_formatted', name: 'created_at_formatted' },
                { data: 'member_name', name: 'member_name' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    }
});
</script>
@endpush