@extends('admin.layouts.app')
@section('page_title', 'Brand Manager | List')
@push('custom-style')
<style>
    .list-image{
  height: 50px !important;
  width: 50px !important;
}
</style>
@endpush
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Brand Manager</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Search Filter -->
            <div class="row filter-row">
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus select-focus">
                        <select class="form-control typeList">
                        </select>
                        <label class="focus-label">Type</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus select-focus">
                        <select class="form-control popular" name="popular">
                            <option disabled selected>Is Popular</option>
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                        </select>
                        <label class="focus-label">Is Popular</label>
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
            <!-- Search Filter -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card mb-0">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Brand List</h4>
                            <div class="col-auto float-end ms-auto">
                                <a href="{{route('admin.brands.create')}}" class="btn btn-sm add-btn"><i class="fa fa-plus"></i> Add Brand</a>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="datatable table table-stripped mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Type</th>
                                            <th>Logo</th>
                                            <th>Name</th>
                                            <th>Created Date & Time</th>
                                            <th>Is Popular</th>
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
        </div>
    </div>

@endsection
@push('custom-script')
    <script>
        const userColumns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'type.name', name: 'type.name' },
            { data: 'logo', name: 'logo' },
            { data: 'name', name: 'name' },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'is_popular', name: 'is_popular', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ];

        const filterSelectors = [
            { name: 'type', selector: '.typeList'},
            { name: 'popular', selector: '.popular'},
        ];

        document.addEventListener('DOMContentLoaded', function() {
            initializeDataTable("{{ route('admin.brands.index') }}",filterSelectors, userColumns);
            initializeSelect2('.typeList', "{{ route('admin.type.list') }}", 'Select Type');
        });
    </script>
@endpush
