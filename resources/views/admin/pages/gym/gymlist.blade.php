@extends('admin.layouts.app')
@section('page_title', 'Gym Manager | List')
@section('content')
{{-- @dd('hm yshi h'); --}}
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
                                            <th>No. Of Users</th>
                                            <th>Status</th>
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
            { data: 'count', name: 'count'},
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ];

        const filterSelectors = [
            // { name: 'membership_status', selector: '.membershipStatus'},
        ];

        document.addEventListener('DOMContentLoaded', function() {
            initializeDataTable("{{ route('admin.gym.gymlist') }}",filterSelectors, userColumns);
        });
    </script>
@endpush

