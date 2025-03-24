@extends('admin.layouts.app')
@section('page_title', 'Equipment | Edit')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Edit Equipment</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.equipment.index') }}">List</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.equipment.index') }}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Edit Equipment</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.equipment.update', $equipment->id) }}" method="post" id="equipmentForm">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <!-- Equipment Name -->
                                    <div class="col-md-6">
                                        <label class="form-label">Equipment Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="equipment_name" value="{{ old('equipment_name', $equipment->equipment_name) }}" required>
                                        @error('equipment_name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>

                                    <!-- Purchase Date -->
                                    <div class="col-md-6">
                                        <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date) }}" required>
                                        @error('purchase_date') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <!-- Condition -->
                                    <div class="col-md-6">
                                        <label class="form-label">Condition <span class="text-danger">*</span></label>
                                        <select class="form-control" name="condition" required>
                                            <option value="">Select</option>
                                            <option value="New" {{ old('condition', $equipment->condition) == 'New' ? 'selected' : '' }}>New</option>
                                            <option value="Good" {{ old('condition', $equipment->condition) == 'Good' ? 'selected' : '' }}>Good</option>
                                            <option value="Needs Maintenance" {{ old('condition', $equipment->condition) == 'Needs Maintenance' ? 'selected' : '' }}>Needs Maintenance</option>
                                        </select>
                                        @error('condition') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>

                                    <!-- Maintenance Date -->
                                    <div class="col-md-6">
                                        <label class="form-label">Next Maintenance Date</label>
                                        <input type="date" class="form-control" name="maintenance_date" value="{{ old('maintenance_date', $equipment->maintenance_date) }}">
                                        @error('maintenance_date') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary px-4">Update</button>
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
        function resetForm() {
            document.getElementById('equipmentForm').reset();
        }
    </script>  
@endpush
