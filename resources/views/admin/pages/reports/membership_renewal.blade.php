@extends('admin.layouts.app')
@section('page_title', 'Report Manager | Membership Renewal')
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
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="dateFilter" class="form-control date_range" placeholder="Select Date Range">
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
                            <h4 class="card-title mb-0">Membership Renewal List</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="datatable table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Created Date & Time</th>
                                            <th>Member Name</th>
                                            <th>Plan</th>
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
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'created_at_formatted', name: 'created_at' }
            { data: 'member_name', name: 'member_name' },
            { data: 'plan', name: 'plan' },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date_formatted', name: 'end_date' },
            { data: 'days_remaining', name: 'days_remaining' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
        ];

        const filterSelectors = [
            { name: 'date_range', selector: '.date_range'},
        ];

        document.addEventListener('DOMContentLoaded', function () {
            initializeDataTable("{{ route('admin.reports.renewals') }}", filterSelectors, userColumns);
        });
    </script>
@endpush
