@extends('admin.layouts.app')
@section('page_title', 'Transactions | List')
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

            <div class="card mb-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-view">
                                <div class="profile-img-wrap">
                                    <div class="profile-img">
                                        <img alt="User Image"
                                            src="{{ $user->getFirstMediaUrl('images', 'thumb') }}"
                                            onerror="this.onerror=null; this.src='{{ asset('assets/img/user.jpg') }}';">
                                    </div>
                                </div>
                                <div class="profile-basic">
                                    <div class="row align-items-center">
                                        <!-- User Info -->
                                        <div class="col-md-6">
                                            <div class="profile-info-left">
                                                <h3 class="user-name mb-1">{{ $user->name }}</h3>
                                                <h6 class="text-muted">+91 {{ $user->phone }}</h6>
                                                <div class="small doj text-muted">
                                                    Member Since: {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Balance Info -->
                                        <div class="col-md-6 text-md-end">
                                            <div class="d-flex flex-column align-items-md-end">
                                                <div class="mb-2">
                                                    <h6 class="mb-1 text-muted">Opening Balance</h6>
                                                    <h5 class="text-success fw-bold mb-0">
                                                        ₹{{ number_format($openingBalanceSum->received_amt ?? 0, 2) }}
                                                    </h5>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 text-muted">Closing Balance</h6>
                                                    <h5 class="text-danger fw-bold mb-0">
                                                        ₹{{ number_format($closingBalanceSum ?? 0, 2) }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
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
                            <div class="col-auto float-end ms-auto">
                                <a href="javascript:void(0);" class="btn btn-sm add-btn payAmount"><i class="fa fa-plus"></i> Pay</a>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="datatable table table-stripped mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Date & Time</th>
                                            <th>Received Amount</th>
                                            <th>Balance Amount</th>
                                            <th>Total Amount</th>
                                            <th>Type</th>
                                            <th>Payment Type</th>
                                            <th>Status</th>
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

   <div class="modal fade" id="amountModal" tabindex="-1" aria-labelledby="amountModalLabel" aria-hidden="true">
        <div class="modal-dialog"> <!-- Missing container -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="amountModalLabel">Enter Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="amountForm">
                    @csrf
                    <input type="hidden" id="id" name="id" value="{{ $id }}">
                    <input type="hidden" id="pending_amt" name="pending_amt" value="{{ $pendingBalanceSum }}">

                    <div class="modal-body">
                        <!-- Amount Input -->
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="amount" name="amount" required value="{{ $pendingBalanceSum }}">
                            <div class="form-text text-danger d-none" id="amountError">Only numbers are allowed, and must not exceed the pending amount.</div>
                        </div>

                        <!-- Payment Type -->
                        <div class="mb-3">
                            <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                            <select class="form-control" name="payment_type" id="paymentType" required>
                                <option value="" disabled selected>Select Payment Type</option>
                                <option value="full">Full Payment</option>
                                <option value="partial">Partial Payment</option>
                            </select>
                            @error('payment_type') <p class="text-danger text-xs pt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
@push('custom-script')
    <script>

        const maxPendingAmount = {{ $pendingBalanceSum }};

        $(document).ready(function(){
            $('.payAmount').click(function(){
                $('#amountModal').modal('show');
            });

            $('#amount').on('input', function () {
                let value = $(this).val();

                // Allow only digits
                value = value.replace(/\D/g, '');
                $(this).val(value);

                const numericValue = parseInt(value, 10);

                // Validation logic
                if (value === '') {
                    $('#amountError').addClass('d-none');
                    return;
                }

                if (isNaN(numericValue)) {
                    $('#amountError').removeClass('d-none').text('Please enter a valid number.');
                } else if (numericValue > maxPendingAmount) {
                    $('#amountError').removeClass('d-none').text(`Amount should not exceed ${maxPendingAmount}.`);
                } else {
                    $('#amountError').addClass('d-none');
                }
            });

            $('#amountForm').on('submit', function (e) {
                e.preventDefault();
                const amount = parseInt($('#amount').val(), 10);
                if (isNaN(amount) || amount > maxPendingAmount) {
                    e.preventDefault();
                    $('#amountError').removeClass('d-none').text(`Amount should not exceed ${maxPendingAmount}.`);
                    return false;
                }

                $.ajax({
                    url: "{{ route('admin.users.pay') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    beforeSend: function () {
                        $('#amountError').addClass('d-none').text('');
                    },
                    success: function (response) {
                        $('#amountModal').modal('hide');

                        if(response.error == true){
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                            });
                        }else{
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                            });
                        }
                        $('#amount').val('');
                        $('.datatable').DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            // Laravel validation error
                            let errors = xhr.responseJSON.errors;
                            if (errors.amount) {
                                $('#amountError').removeClass('d-none').text(errors.amount[0]);
                            }
                        } else {
                            alert('An unexpected error occurred.');
                        }
                    }
                });
            });

            $('#paymentMethod').change(function () {
                if ($(this).val() === "online") {
                    $('#utrField').show();
                } else {
                    $('#utrField').hide();
                }
            });
            
            // $('#paymentType').change(function () {
            //     if ($(this).val() === "partial") {
            //         $('#received_amtField').show();
            //     } else {
            //         $('#received_amtField').hide();
            //     }
            // });
        });

        if ($.fn.DataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable().destroy(); // Destroy previous instance
        }
        var id = "{{$id}}";
        var base_Url = "{{ route('admin.users.transactions', ':id') }}";
        var url_pt = base_Url.replace(':id', id);
        $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url_pt,
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' }, // Iteration column
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'received', name: 'received', orderable: false, searchable: false },
                { data: 'balance', name: 'balance', orderable: false, searchable: false },
                { data: 'total', name: 'total' },
                { data: 'type', name: 'type' },
                { data: 'payment_type', name: 'payment_type' },
                { data: 'status', name: 'status' },
            ]
        });

    </script>

    
    
@endpush

