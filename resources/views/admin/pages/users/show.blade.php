@extends('admin.layouts.app')
@section('page_title', 'Member Manager | View')
@push('custom-style')
    <style>
    .profile-info-left{
        height:119px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        justify-content: center;
    }
    </style>
@endpush
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Member Manager</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">List</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        
        <!-- Member Info -->
        <div class="card mb-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="profile-view">
                            <div class="profile-img-wrap">
                                <div class="profile-img">
                                    <a href="javacript:void(0);"><img alt=""
                                            src="{{ $user->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/user.jpg') }}"></a>
                                </div>
                            </div>
                            <div class="profile-basic">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="profile-info-left">
                                            <h3 class="user-name m-t-0 mb-0">{{ $user->name }}</h3>
                                            <h6 class="text-muted">+91 {{$user->phone }}</h6>
                                            {{-- <small class="text-muted">Web Designer</small>
                                            <div class="staff-id">Employee ID : FT-0001</div> --}}
                                            <div class="small doj text-muted">Member Since : {{ Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</div>

                                            {{-- <div class="staff-msg"><a class="btn btn-custom" href="chat.html">Send
                                                    Message</a></div> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <ul class="personal-info">
                                            <li>
                                                <div class="title">Plan:</div>
                                                <div class="text">{{$lastestPlan->plan->name ?? 'N/A'}}</div>
                                            </li>
                                            <li>
                                                <div class="title">Price:</div>
                                                <div class="text">{{$lastestPlan != null ? '₹ '.number_format($lastestPlan->plan->price) : 'N/A'}}</div>
                                            </li>
                                            <li>
                                                <div class="title">Duration (in days):</div>
                                                <div class="text">{{$lastestPlan != null ? $lastestPlan->days.' Days' : 'N/A'}}</div>
                                            </li>
                                            <li>
                                                <div class="title">Started on:</div>
                                                <div class="text">{{$lastestPlan != null ? Carbon\Carbon::parse($lastestPlan->start_date)->format('d M Y') : 'N/A'}}</div>
                                            </li>
                                            <li>
                                                <div class="title">Expiry Date:</div>
                                                <div class="text">{{$lastestPlan != null ? Carbon\Carbon::parse($lastestPlan->end_date)->format('d M Y') : 'N/A'}}</div>
                                            </li>
                                            <li>
                                                <div class="title">Plan Status</div>
                                                <div class="text">{{$lastestPlan->membership_status ?? 'N/A'}}</div>
                                            </li>
                                            
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="pro-edit"><a data-bs-target="#profile_info" data-bs-toggle="modal"
                                    class="edit-icon" href="javacript:void(0);"><i class="fa fa-pencil"></i></a></div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Plan Histort -->
        <div class="table-responsive table-newdatatable">
            <table class="table table-new custom-table mb-0 datatable">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Date & Time</th>
                        <th>User Type</th>
                        <th>Member Name</th>
                        <th>Plan</th>
                        <th>Price</th>
                        <th>Duration (in days)</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Payment Method</th>
                        <th>UTR</th>
                        <th>Membership Status</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>

    </div>
    <!-- /Page Content -->
</div>

@endsection
@push('custom-script')
    <script>
        if ($.fn.DataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable().destroy(); // Destroy previous instance
        }
        var userId = "{{$user->id}}";
        var baseUrl = "{{ route('admin.users.userRenewalHistory', ':userId') }}";
        var url = baseUrl.replace(':userId', userId);
        $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' }, // Iteration column
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'user_type', name: 'user_type' },
                { data: 'member_name', name: 'member_name' },
                { data: 'plan', name: 'plan' },
                { data: 'price', name: 'price' },
                { data: 'days', name: 'days' },
                { data: 'start_date', name: 'start_date' },
                { data: 'end_date', name: 'end_date' },
                { data: 'payment_method', name: 'payment_method' },
                { data: 'utr', name: 'utr' },
                { data: 'membership_status', name: 'membership_status' },
            ]
        });

    </script>
@endpush