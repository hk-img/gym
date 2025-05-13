@extends('admin.layouts.app')
@section('page_title', 'Transactions | List')
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
                        <h3 class="page-title">Transactions</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">List</li>
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
                            <option value="pending">Pending</option>
                            <option value="cleared">Cleared</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 d-flex gap-2 mt-4">
                    <a href="javascript:void(0);" class="btn btn-success btn-search text-capitalize w-50">Search</a>
                    <button class="btn btn-danger btn-clear text-capitalize w-50">Clear</button>
                </div>
            </div>
            <!-- Table -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                
                                <h4 class="card-title mb-0">Transactions List</h4>
                                <h4>Total Balance Amount:{{$pendingBalanceSum}}</h4>
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
                                            <th>Received Amount</th>
                                            <th>Balance Amount</th>
                                            <th>Total Amount</th>
                                            <th>Type</th>
                                            <th>Payment Type</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script>

        $('#monthFilter').datepicker({
            format: "yyyy-mm", // Year-Month format
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });


        $('.btn-search').on('click', function () {
            let selectedMonth = $('#monthFilter').val();
            let selectedUserId = $('#userId').val();
            let statusType = $('#statusType').val();

            if (selectedMonth || selectedUserId || statusType) {
                $('.datatable').DataTable().ajax.reload(); // Reload table with new filters
            } else {
                alert('Please select at least one filter!');
            }
        });

        $('.btn-clear').on('click', function () {
            $('#monthFilter').val('');
            $('#userId').val('');
            $('#statusType').val('');
            $('#trainerId').val(''); // optional
            $('#hideRevenue').show();
            $('.datatable').DataTable().ajax.reload(); // Reload table with cleared filters
        });

        var base_Url = "{{ route('admin.transactions.index') }}";
        var url_pt = base_Url;

        $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url_pt,
                data: function (d) {
                    d.month = $('#monthFilter').val();
                    d.user_id = $('#userId').val();
                    d.status = $('#statusType').val();
                }
            },
            destroy: true, // make sure you can reinitialize it
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'name', name: 'name' },
                { data: 'phone', name: 'phone' },
                { data: 'received', name: 'received', orderable: false, searchable: false },
                { data: 'balance', name: 'balance', orderable: false, searchable: false },
                { data: 'total', name: 'total' },
                { data: 'type', name: 'type' },
                { data: 'payment_type', name: 'payment_type' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action' },
            ]
        });

    </script>

    
    
@endpush

