@extends('admin.layouts.app')
@section('page_title', 'Send Notification')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Send Notification</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Send Notification</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.dashboard') }}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Notification Form</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.notifications.send') }}" method="post" id="notificationForm">
                                @csrf
                                <div class="row g-3">
                                    <!-- Notification Title -->
                                    <div class="col-md-6">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" placeholder="Enter Notification Title">
                                        @error('title') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                    </div>
                                    
                                    <!-- Notification Type -->
                                    <div class="col-md-6">
                                        <label class="form-label">Type <span class="text-danger">*</span></label>
                                        <select class="form-control" name="type">
                                            <option value="sms" {{ old('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                                            <option value="in-app" {{ old('type') == 'in-app' ? 'selected' : '' }}>In-App</option>
                                        </select>
                                        @error('type') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <!-- User Selection -->
                                    <div class="col-md-6">
                                        <label class="form-label">Send To <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="radio" name="send_to" value="all" id="allUsers" checked onclick="toggleUserSelect()">
                                            <label for="allUsers">All Users</label>
                                            <input type="radio" name="send_to" value="specific" id="specificUser" onclick="toggleUserSelect()">
                                            <label for="specificUser">Specific User</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6" id="userSelect" style="display: none;">
                                        <select class="userList form-control" name="user_id[]" multiple>
                                            <!-- User List -->
                                        </select>
                                        @error('user_id') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <!-- Notification Message -->
                                    <div class="col-md-12">
                                        <label class="form-label">Message <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="message" rows="4" placeholder="Enter Notification Message">{{ old('message') }}</textarea>
                                        @error('message') <p class="text-danger text-xs pt-1">{{$message}}</p> @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary px-4">Send</button>
                                    <button type="button" class="btn btn-secondary px-4" onclick="resetForm()">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeSelect2('.userList', "{{ route('admin.option.userlist') }}", 'Select User');
        });

        function resetForm() {
            document.getElementById('notificationForm').reset();
            toggleUserSelect();
        }
        
        function toggleUserSelect() {
            let userSelect = document.getElementById('userSelect');
            let specificUser = document.getElementById('specificUser').checked;
            userSelect.style.display = specificUser ? 'block' : 'none';
        }
    </script>
@endpush
