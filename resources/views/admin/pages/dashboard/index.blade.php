@extends('admin.layouts.app')
@section('page_title', 'Dashboard - HRMS admin template')
@section('content')
        <div class="page-wrapper">

    <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Welcome {{auth()->user()->name}}!</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                <div class="row">
                    @if(auth()->user()->hasRole('Super Admin'))
                        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                            <div class="card dash-widget">
                                <div class="card-body">
                                    <span class="dash-widget-icon"><i class="fa fa-user"></i></span>
                                    <div class="dash-widget-info">
                                        <h3>{{isset($data) ? $data['gym'] : 0}}</h3>
                                        <span>Gym</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(auth()->user()->hasRole('Gym'))
                        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                            <div class="card dash-widget">
                                <div class="card-body">
                                    <span class="dash-widget-icon"><i class="fa fa-user"></i></span>
                                    <div class="dash-widget-info">
                                        <h3>{{isset($data) ? $data['member'] : 0}}</h3>
                                        <span>Member</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                            <div class="card dash-widget">
                                <div class="card-body">
                                    <span class="dash-widget-icon"><i class="fa fa-tasks"></i></span>
                                    <div class="dash-widget-info">
                                        <h3>{{isset($data) ? $data['plan'] : 0}}</h3>
                                        <span>Plan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Total Revenue</h3>
                                        <div id="bar-charts"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Sales Overview</h3>
                                        <div id="line-charts"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="card-group m-b-30">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">New Employees</span>
                                        </div>
                                        <div>
                                            <span class="text-success">+10%</span>
                                        </div>
                                    </div>
                                    <h3 class="mb-3">10</h3>
                                    <div class="progress height-five mb-2">
                                        <div class="progress-bar bg-primary w-70" role="progressbar" aria-valuenow="40"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mb-0">Overall Employees 218</p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">Earnings</span>
                                        </div>
                                        <div>
                                            <span class="text-success">+12.5%</span>
                                        </div>
                                    </div>
                                    <h3 class="mb-3">$1,42,300</h3>
                                    <div class="progress height-five mb-2">
                                        <div class="progress-bar bg-primary w-70" role="progressbar" aria-valuenow="40"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mb-0">Previous Month <span class="text-muted">$1,15,852</span></p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">Expenses</span>
                                        </div>
                                        <div>
                                            <span class="text-danger">-2.8%</span>
                                        </div>
                                    </div>
                                    <h3 class="mb-3">$8,500</h3>
                                    <div class="progress height-five mb-2">
                                        <div class="progress-bar bg-primary w-70" role="progressbar" aria-valuenow="40"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mb-0">Previous Month <span class="text-muted">$7,500</span></p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">Profit</span>
                                        </div>
                                        <div>
                                            <span class="text-danger">-75%</span>
                                        </div>
                                    </div>
                                    <h3 class="mb-3">$1,12,000</h3>
                                    <div class="progress height-five mb-2">
                                        <div class="progress-bar bg-primary w-70" role="progressbar" aria-valuenow="40"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mb-0">Previous Month <span class="text-muted">$1,42,000</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Widget -->
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-xl-4 d-flex">
                        <div class="card flex-fill dash-statistics">
                            <div class="card-body">
                                <h5 class="card-title">Statistics</h5>
                                <div class="stats-list">
                                    <div class="stats-info">
                                        <p>Today Leave <strong>4 <small>/ 65</small></strong></p>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 31%"
                                                aria-valuenow="31" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="stats-info">
                                        <p>Pending Invoice <strong>15 <small>/ 92</small></strong></p>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 31%"
                                                aria-valuenow="31" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="stats-info">
                                        <p>Completed Projects <strong>85 <small>/ 112</small></strong></p>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 62%"
                                                aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="stats-info">
                                        <p>Open Tickets <strong>190 <small>/ 212</small></strong></p>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: 62%"
                                                aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="stats-info">
                                        <p>Closed Tickets <strong>22 <small>/ 212</small></strong></p>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 22%"
                                                aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6 col-xl-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <h4 class="card-title">Task Statistics</h4>
                                <div class="statistics">
                                    <div class="row">
                                        <div class="col-md-6 col-6 text-center">
                                            <div class="stats-box mb-4">
                                                <p>Total Tasks</p>
                                                <h3>385</h3>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6 text-center">
                                            <div class="stats-box mb-4">
                                                <p>Overdue Tasks</p>
                                                <h3>19</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress mb-4">
                                    <div class="progress-bar bg-purple w-30" role="progressbar" aria-valuenow="30"
                                        aria-valuemin="0" aria-valuemax="100">30%</div>
                                    <div class="progress-bar bg-warning w-22" role="progressbar" aria-valuenow="18"
                                        aria-valuemin="0" aria-valuemax="100">22%</div>
                                    <div class="progress-bar bg-success w-24" role="progressbar" aria-valuenow="12"
                                        aria-valuemin="0" aria-valuemax="100">24%</div>
                                    <div class="progress-bar bg-danger w-21" role="progressbar" aria-valuenow="14"
                                        aria-valuemin="0" aria-valuemax="100">21%</div>
                                    <div class="progress-bar bg-info w-10" role="progressbar" aria-valuenow="14"
                                        aria-valuemin="0" aria-valuemax="100">10%</div>
                                </div>
                                <div>
                                    <p><i class="fa-regular fa-circle-dot text-purple me-2"></i>Completed Tasks <span
                                            class="float-end">166</span></p>
                                    <p><i class="fa-regular fa-circle-dot text-warning me-2"></i>Inprogress Tasks <span
                                            class="float-end">115</span></p>
                                    <p><i class="fa-regular fa-circle-dot text-success me-2"></i>On Hold Tasks <span
                                            class="float-end">31</span></p>
                                    <p><i class="fa-regular fa-circle-dot text-danger me-2"></i>Pending Tasks <span
                                            class="float-end">47</span></p>
                                    <p class="mb-0"><i class="fa-regular fa-circle-dot text-info me-2"></i>Review Tasks
                                        <span class="float-end">5</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6 col-xl-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <h4 class="card-title">Today Absent <span class="badge bg-inverse-danger ms-2">5</span>
                                </h4>
                                <div class="leave-info-box">
                                    <div class="media d-flex align-items-center">
                                        <a href="profile.html" class="avatar"><img alt=""
                                                src="{{asset('assets/img/user.jpg')}}"></a>
                                        <div class="media-body flex-grow-1">
                                            <div class="text-sm my-0">Martin Lewis</div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center mt-3">
                                        <div class="col-6">
                                            <h6 class="mb-0">4 Sep 2019</h6>
                                            <span class="text-sm text-muted">Leave Date</span>
                                        </div>
                                        <div class="col-6 text-end">
                                            <span class="badge bg-inverse-danger">Pending</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="leave-info-box">
                                    <div class="media d-flex align-items-center">
                                        <a href="profile.html" class="avatar"><img alt=""
                                                src="{{asset('assets/img/user.jpg')}}"></a>
                                        <div class="media-body flex-grow-1">
                                            <div class="text-sm my-0">Martin Lewis</div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center mt-3">
                                        <div class="col-6">
                                            <h6 class="mb-0">4 Sep 2019</h6>
                                            <span class="text-sm text-muted">Leave Date</span>
                                        </div>
                                        <div class="col-6 text-end">
                                            <span class="badge bg-inverse-success">Approved</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="load-more text-center">
                                    <a class="text-dark" href="javascript:void(0);">Load More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Statistics Widget -->

                <div class="row">
                    <div class="col-md-6 d-flex">
                        <div class="card card-table flex-fill">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Invoices</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-nowrap custom-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Client</th>
                                                <th>Due Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="invoice-view.html">#INV-0001</a></td>
                                                <td>
                                                    <h2><a href="javacript:void(0);">Global Technologies</a></h2>
                                                </td>
                                                <td>11 Mar 2019</td>
                                                <td>$380</td>
                                                <td>
                                                    <span class="badge bg-inverse-warning">Partially Paid</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="invoice-view.html">#INV-0002</a></td>
                                                <td>
                                                    <h2><a href="javacript:void(0);">Delta Infotech</a></h2>
                                                </td>
                                                <td>8 Feb 2019</td>
                                                <td>$500</td>
                                                <td>
                                                    <span class="badge bg-inverse-success">Paid</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="invoice-view.html">#INV-0003</a></td>
                                                <td>
                                                    <h2><a href="javacript:void(0);">Cream Inc</a></h2>
                                                </td>
                                                <td>23 Jan 2019</td>
                                                <td>$60</td>
                                                <td>
                                                    <span class="badge bg-inverse-danger">Unpaid</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="invoices.html">View all invoices</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex">
                        <div class="card card-table flex-fill">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Payments</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Client</th>
                                                <th>Payment Type</th>
                                                <th>Paid Date</th>
                                                <th>Paid Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="invoice-view.html">#INV-0001</a></td>
                                                <td>
                                                    <h2><a href="javacript:void(0);">Global Technologies</a></h2>
                                                </td>
                                                <td>Paypal</td>
                                                <td>11 Mar 2019</td>
                                                <td>$380</td>
                                            </tr>
                                            <tr>
                                                <td><a href="invoice-view.html">#INV-0002</a></td>
                                                <td>
                                                    <h2><a href="javacript:void(0);">Delta Infotech</a></h2>
                                                </td>
                                                <td>Paypal</td>
                                                <td>8 Feb 2019</td>
                                                <td>$500</td>
                                            </tr>
                                            <tr>
                                                <td><a href="invoice-view.html">#INV-0003</a></td>
                                                <td>
                                                    <h2><a href="javacript:void(0);">Cream Inc</a></h2>
                                                </td>
                                                <td>Paypal</td>
                                                <td>23 Jan 2019</td>
                                                <td>$60</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="payments.html">View all payments</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 d-flex">
                        <div class="card card-table flex-fill">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Clients</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a href="javacript:void(0);" class="avatar"><img
                                                                src="{{asset('assets/img/profiles/avatar-19.jpg')}}"
                                                                alt="User Image"></a>
                                                        <a href="client-profile.html">Barry Cuda <span>CEO</span></a>
                                                    </h2>
                                                </td>
                                                <td><a href="https://smarthr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                        class="__cf_email__"
                                                        data-cfemail="90f2f1e2e2e9f3e5f4f1d0f5e8f1fde0fcf5bef3fffd">[email&#160;protected]</a>
                                                </td>
                                                <td>
                                                    <div class="dropdown action-label">
                                                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                                            href="javacript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-regular fa-circle-dot text-success"></i> Active
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-success"></i>
                                                                Active</a>
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-danger"></i>
                                                                Inactive</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a href="javacript:void(0);" class="avatar"><img
                                                                src="{{asset('assets/img/profiles/avatar-19.jpg')}}"
                                                                alt="User Image"></a>
                                                        <a href="client-profile.html">Tressa Wexler
                                                            <span>Manager</span></a>
                                                    </h2>
                                                </td>
                                                <td><a href="https://smarthr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                        class="__cf_email__"
                                                        data-cfemail="1561677066667462706d79706755706d74786579703b767a78">[email&#160;protected]</a>
                                                </td>
                                                <td>
                                                    <div class="dropdown action-label">
                                                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                                            href="javacript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-regular fa-circle-dot text-danger"></i>
                                                            Inactive
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-success"></i>
                                                                Active</a>
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-danger"></i>
                                                                Inactive</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a href="client-profile.html" class="avatar"><img
                                                                src="{{asset('assets/img/profiles/avatar-07.jpg')}}"
                                                                alt="User Image"></a>
                                                        <a href="client-profile.html">Ruby Bartlett <span>CEO</span></a>
                                                    </h2>
                                                </td>
                                                <td><a href="https://smarthr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                        class="__cf_email__"
                                                        data-cfemail="196b6c7b607b786b6d757c6d6d597c61787469757c377a7674">[email&#160;protected]</a>
                                                </td>
                                                <td>
                                                    <div class="dropdown action-label">
                                                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                                            href="javacript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-regular fa-circle-dot text-danger"></i>
                                                            Inactive
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-success"></i>
                                                                Active</a>
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-danger"></i>
                                                                Inactive</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a href="client-profile.html" class="avatar"><img
                                                                src="{{asset('assets/img/profiles/avatar-06.jpg')}}"
                                                                alt="User Image"></a>
                                                        <a href="client-profile.html"> Misty Tison <span>CEO</span></a>
                                                    </h2>
                                                </td>
                                                <td><a href="https://smarthr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                        class="__cf_email__"
                                                        data-cfemail="fa9793898e838e93899594ba9f829b978a969fd4999597">[email&#160;protected]</a>
                                                </td>
                                                <td>
                                                    <div class="dropdown action-label">
                                                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                                            href="javacript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-regular fa-circle-dot text-success"></i> Active
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-success"></i>
                                                                Active</a>
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-danger"></i>
                                                                Inactive</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a href="client-profile.html" class="avatar"><img
                                                                src="{{asset('assets/img/profiles/avatar-14.jpg')}}"
                                                                alt="User Image"></a>
                                                        <a href="client-profile.html"> Daniel Deacon
                                                            <span>CEO</span></a>
                                                    </h2>
                                                </td>
                                                <td><a href="https://smarthr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                        class="__cf_email__"
                                                        data-cfemail="a5c1c4cbccc0c9c1c0c4c6cacbe5c0ddc4c8d5c9c08bc6cac8">[email&#160;protected]</a>
                                                </td>
                                                <td>
                                                    <div class="dropdown action-label">
                                                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                                            href="javacript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-regular fa-circle-dot text-danger"></i>
                                                            Inactive
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-success"></i>
                                                                Active</a>
                                                            <a class="dropdown-item" href="javacript:void(0);"><i
                                                                    class="fa-regular fa-circle-dot text-danger"></i>
                                                                Inactive</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="clients.html">View all clients</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex">
                        <div class="card card-table flex-fill">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Recent Projects</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Project Name </th>
                                                <th>Progress</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <h2><a href="project-view.html">Office Management</a></h2>
                                                    <small class="block text-ellipsis">
                                                        <span>1</span> <span class="text-muted">open tasks, </span>
                                                        <span>9</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="progress progress-xs progress-striped">
                                                        <div class="progress-bar" role="progressbar"
                                                            data-bs-toggle="tooltip" title="65%" style="width: 65%">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2><a href="project-view.html">Project Management</a></h2>
                                                    <small class="block text-ellipsis">
                                                        <span>2</span> <span class="text-muted">open tasks, </span>
                                                        <span>5</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="progress progress-xs progress-striped">
                                                        <div class="progress-bar" role="progressbar"
                                                            data-bs-toggle="tooltip" title="15%" style="width: 15%">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2><a href="project-view.html">Video Calling App</a></h2>
                                                    <small class="block text-ellipsis">
                                                        <span>3</span> <span class="text-muted">open tasks, </span>
                                                        <span>3</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="progress progress-xs progress-striped">
                                                        <div class="progress-bar" role="progressbar"
                                                            data-bs-toggle="tooltip" title="49%" style="width: 49%">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2><a href="project-view.html">Hospital Administration</a></h2>
                                                    <small class="block text-ellipsis">
                                                        <span>12</span> <span class="text-muted">open tasks, </span>
                                                        <span>4</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="progress progress-xs progress-striped">
                                                        <div class="progress-bar" role="progressbar"
                                                            data-bs-toggle="tooltip" title="88%" style="width: 88%">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2><a href="project-view.html">Digital Marketplace</a></h2>
                                                    <small class="block text-ellipsis">
                                                        <span>7</span> <span class="text-muted">open tasks, </span>
                                                        <span>14</span> <span class="text-muted">tasks completed</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="progress progress-xs progress-striped">
                                                        <div class="progress-bar" role="progressbar"
                                                            data-bs-toggle="tooltip" title="100%" style="width: 100%">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javacript:void(0);" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="material-icons">more_vert</i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="javascript:void(0)"><i
                                                                    class="fa-regular fa-trash-can m-r-5"></i>
                                                                Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="projects.html">View all projects</a>
                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>

@endsection