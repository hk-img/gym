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
                            <!-- <li class="breadcrumb-item"><a href="{{ route('admin.diet-plan.index') }}">List</a></li> -->
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
                                    


                                <div class="mt-4">
                                    <h5>Meals</h5>
                                    <table class="table" id="mealTable">
                                        <thead>
                                            <tr>
                                                <th>Meal Type</th>
                                                <th>Meal Name</th>
                                                <th>Description</th>
                                                {{-- <th>Action</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dietPlan->meals as $index => $meal)

                                                <tr>
                                                    <td>{{$meal->meal_type}}</td>
                                                    
                                                    <td>
                                                        <input type="hidden" name="meals[{{ $index }}][meal_type]"  value="{{ $meal->meal_type }}" class="form-control" required/>
                                                        <input type="text" name="meals[{{ $index }}][meal_name]"  value="{{ $meal->meal_name }}" class="form-control" required></td>
                                                    <td><textarea name="meals[{{ $index }}][description]" class="form-control" required>{{$meal->description}}</textarea></td>
                                                </tr>
                                
                                            @endforeach
                                        </tbody>
                                    </table>
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

        if (selectedMemberId) {
            var option = new Option(selectedMemberName, selectedMemberId, true, true);
            $('.userList').append(option).trigger('change');
        }
        
        function resetForm() {
            document.getElementById('dietPlanForm').reset();
        }

        // document.getElementById('addMeal').addEventListener('click', function() {
        //     let table = document.getElementById('mealTable').getElementsByTagName('tbody')[0];
        //     let rowCount = table.rows.length;
        //     let row = table.insertRow(rowCount);
        //     row.innerHTML = `
        //         <td>
        //             <select name="meals[${rowCount}][meal_type]" class="form-control" required>
        //                 <option value="">Select</option>
        //                 <option value="Breakfast">Breakfast</option>
        //                 <option value="Lunch">Lunch</option>
        //                 <option value="Dinner">Dinner</option>
        //                 <option value="Snacks">Snacks</option>
        //             </select>
        //         </td>
        //         <td><input type="text" name="meals[${rowCount}][meal_name]" class="form-control" required></td>
        //         <td><input type="number" name="meals[${rowCount}][description]" class="form-control" required></td>
        //     `;
        // });

        // document.addEventListener('click', function(event) {
        //     if (event.target.classList.contains('removeMeal')) {
        //         event.target.closest('tr').remove();
        //     }
        // });
    </script>
@endpush
