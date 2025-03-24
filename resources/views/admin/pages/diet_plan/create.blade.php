@extends('admin.layouts.app')
@section('page_title', 'Diet Plan | Add')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Diet Plan</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.diet-plan.index') }}">List</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.diet-plan.index') }}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add Diet Plan</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.diet-plan.store') }}" method="post" id="dietPlanForm">
                                @csrf
                                
                                <div class="row g-3">
                                    <!-- Member Selection -->
                                    <div class="col-md-6">
                                        <label class="form-label">Member <span class="text-danger">*</span></label>
                                        <select class="userList form-control" name="member_id" required>
                                            
                                        </select>
                                        @error('member_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>

                                    <!-- Diet Plan Name -->
                                    <div class="col-md-6">
                                        <label class="form-label">Diet Plan Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="diet_plan_name" placeholder="Enter Diet Plan Name" required>
                                        @error('diet_plan_name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <!-- Date -->
                                    <div class="col-md-6">
                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required>
                                        @error('date') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                </div>

                                <!-- Dynamic Meal Fields -->
                                <div class="mt-4">
                                    <h5>Meals</h5>
                                    <table class="table" id="mealTable">
                                        <thead>
                                            <tr>
                                                <th>Meal Type</th>
                                                <th>Meal Name</th>
                                                <th>Calories</th>
                                                <th>Protein (g)</th>
                                                <th>Carbs (g)</th>
                                                <th>Fats (g)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select name="meals[0][meal_type]" class="form-control" required>
                                                        <option value="">Select</option>
                                                        <option value="Breakfast">Breakfast</option>
                                                        <option value="Lunch">Lunch</option>
                                                        <option value="Dinner">Dinner</option>
                                                        <option value="Snacks">Snacks</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="meals[0][meal_name]" class="form-control" required></td>
                                                <td><input type="number" name="meals[0][calories]" class="form-control" required></td>
                                                <td><input type="number" step="0.1" name="meals[0][protein]" class="form-control" required></td>
                                                <td><input type="number" step="0.1" name="meals[0][carbs]" class="form-control" required></td>
                                                <td><input type="number" step="0.1" name="meals[0][fats]" class="form-control" required></td>
                                                <td><button type="button" class="btn btn-danger btn-sm removeMeal"><i class="fa-solid fa-trash"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-secondary" id="addMeal">Add Meal</button>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary px-4">Save</button>
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
            document.getElementById('dietPlanForm').reset();
        }

        document.getElementById('addMeal').addEventListener('click', function() {
            let table = document.getElementById('mealTable').getElementsByTagName('tbody')[0];
            let rowCount = table.rows.length;
            let row = table.insertRow(rowCount);
            row.innerHTML = `
                <td>
                    <select name="meals[${rowCount}][meal_type]" class="form-control" required>
                        <option value="">Select</option>
                        <option value="Breakfast">Breakfast</option>
                        <option value="Lunch">Lunch</option>
                        <option value="Dinner">Dinner</option>
                        <option value="Snacks">Snacks</option>
                    </select>
                </td>
                <td><input type="text" name="meals[${rowCount}][meal_name]" class="form-control" required></td>
                <td><input type="number" name="meals[${rowCount}][calories]" class="form-control" required></td>
                <td><input type="number" step="0.1" name="meals[${rowCount}][protein]" class="form-control" required></td>
                <td><input type="number" step="0.1" name="meals[${rowCount}][carbs]" class="form-control" required></td>
                <td><input type="number" step="0.1" name="meals[${rowCount}][fats]" class="form-control" required></td>
                <td><button type="button" class="btn btn-danger btn-sm removeMeal"><i class="fa-solid fa-trash"></i></button></td>
            `;
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('removeMeal')) {
                event.target.closest('tr').remove();
            }
        });
    </script>
@endpush
