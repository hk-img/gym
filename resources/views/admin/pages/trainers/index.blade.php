@extends('admin.layouts.app')
@section('page_title', 'Trainers Manager | List')
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Trainers Manager</h3>
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
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus select-focus">
                        <select class="form-control membershipStatus">
                            <option selected disabled>Select Membership Status</option>
                            <option value="0">In-Active</option>
                            <option value="1">Active</option>
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
                            <h4 class="card-title mb-0">Trainers List</h4>
                            <div class="col-auto float-end ms-auto">
                                <a href="{{route('admin.trainers.create')}}" class="btn btn-sm add-btn"><i class="fa fa-plus"></i> Add Trainer</a>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="datatable table table-stripped mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Date & Time</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Salary</th>
                                            <th>PT Fees (per month)</th>
                                            <th>Joining Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
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
    <script>
        const userColumns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, // Iteration column
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'name', name: 'name' },
            { data: 'phone', name: 'phone' },
            { data: 'salary', name: 'salary' },
            { data: 'pt_fees', name: 'pt_fees' },
            { data: 'joining_date', name: 'joining_date' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ];

        const filterSelectors = [
            { name: 'status', selector: '.membershipStatus'},
        ];

        document.addEventListener('DOMContentLoaded', function() {
            initializeDataTable("{{ route('admin.trainers.index') }}",filterSelectors, userColumns);
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

