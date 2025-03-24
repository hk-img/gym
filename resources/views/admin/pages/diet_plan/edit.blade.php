@extends('admin.layouts.app')
@section('page_title', 'Diet Plan | Edit')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Edit Diet Plan</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.diet-plan.index') }}">List</a></li>
                            <li class="breadcrumb-item active">Edit</li>
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
                            <h4 class="card-title mb-0">Edit Diet Plan</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.diet-plan.update', $dietPlan->id) }}" method="post" id="dietPlanForm">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <!-- Member Selection -->
                                    <div class="col-md-6">
                                        <label class="form-label">Member <span class="text-danger">*</span></label>
                                        <select class="userList form-control" name="member_id" required>
                                            
                                        </select>
                                        @error('member_id') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Diet Plan Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="diet_plan_name" value="{{ $dietPlan->diet_plan_name }}" required>
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" value="{{ $dietPlan->date }}" required>
                                    </div>
                                </div>

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
                                            @foreach($dietPlan->meals as $index => $meal)
                                            <tr>
                                                <td>
                                                    <select name="meals[{{ $index }}][meal_type]" class="form-control" required>
                                                        <option value="Breakfast" {{ $meal->meal_type == 'Breakfast' ? 'selected' : '' }}>Breakfast</option>
                                                        <option value="Lunch" {{ $meal->meal_type == 'Lunch' ? 'selected' : '' }}>Lunch</option>
                                                        <option value="Dinner" {{ $meal->meal_type == 'Dinner' ? 'selected' : '' }}>Dinner</option>
                                                        <option value="Snacks" {{ $meal->meal_type == 'Snacks' ? 'selected' : '' }}>Snacks</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="meals[{{ $index }}][meal_name]" class="form-control" value="{{ $meal->meal_name }}" required></td>
                                                <td><input type="number" name="meals[{{ $index }}][calories]" class="form-control" value="{{ $meal->calories }}" required></td>
                                                <td><input type="number" step="0.1" name="meals[{{ $index }}][protein]" class="form-control" value="{{ $meal->protein }}" required></td>
                                                <td><input type="number" step="0.1" name="meals[{{ $index }}][carbs]" class="form-control" value="{{ $meal->carbs }}" required></td>
                                                <td><input type="number" step="0.1" name="meals[{{ $index }}][fats]" class="form-control" value="{{ $meal->fats }}" required></td>
                                                <td><button type="button" class="btn btn-danger btn-sm removeMeal"><i class="fa-solid fa-trash"></i></button></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-secondary" id="addMeal">Add Meal</button>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary px-4">Update</button>
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
        var selectedMemberId = "{{ $dietPlan->user_id ?? '' }}"; 
        var selectedMemberName = "{{ $dietPlan->user->name ?? '' }}";

        document.addEventListener('DOMContentLoaded', function() {
            initializeSelect2('.userList', "{{ route('admin.option.userlist') }}", 'Select User');
        });

        // Preselect the item
        if (selectedMemberId) {
            var option = new Option(selectedMemberName, selectedMemberId, true, true);
            $('.userList').append(option).trigger('change');
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
