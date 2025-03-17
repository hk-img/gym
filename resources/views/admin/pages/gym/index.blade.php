@extends('admin.layouts.app')
@section('page_title', 'Gym Manager | List')
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Gym Manager</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Filter Row -->
            {{-- <div class="row filter-row">
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
                        <a href="#" class="btn btn-success btn-search text-capitalize">Search</a>
                    </div>
                     <div class="d-grid h-25">
                        <button class="btn btn-danger btn-clear text-capitalize">Clear</button>
                    </div>
                </div>
            </div> --}}
            <!-- /Filter Row -->
            
            <!-- Table -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card mb-0">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Gym List</h4>
                            <div class="col-auto float-end ms-auto">
                                <a href="{{route('admin.gym.create')}}" class="btn btn-sm add-btn"><i class="fa fa-plus"></i> Add Gym</a>
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
                                            <th>Email</th>
                                            {{-- <th>Status</th> --}}
                                            <th>Action</th>
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
    <script>
        const userColumns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, // Iteration column
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'name', name: 'name' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            {{-- { data: 'status', name: 'status', orderable: false, searchable: false }, --}}
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ];

        const filterSelectors = [
            // { name: 'membership_status', selector: '.membershipStatus'},
        ];

        document.addEventListener('DOMContentLoaded', function() {
            initializeDataTable("{{ route('admin.gym.index') }}",filterSelectors, userColumns);
        });
    </script>
@endpush

<script>
    $(document).ready(function(){

        
    });
</script>
