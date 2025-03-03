@extends('admin.layouts.app')
@section('page_title', 'Role Manager | List')
@section('content')
<div class="page-wrapper">

    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col">
                    <h3 class="page-title">Role Manager</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-sm-12">
                <div class="card mb-0">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title mb-0">Role List</h4>
                        <div class="col-auto float-end ms-auto">
                            <a href="{{route('admin.roles.create')}}" class="btn btn-sm add-btn"><i class="fa fa-plus"></i> Add Role</a>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="datatable table table-stripped mb-0">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Name</th>
                                        <th>Permission</th>
                                        <th>Created Date & Time</th>
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

    </div>
</div>
@endsection
@push('custom-script')
    <script>
        const userColumns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name', name: 'name' },
            { data: 'permission', name: 'permission' },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ];

        const filterSelectors = [];

        document.addEventListener('DOMContentLoaded', function() {
            initializeDataTable("{{ route('admin.roles.index') }}",filterSelectors, userColumns);
        });
    </script>
@endpush