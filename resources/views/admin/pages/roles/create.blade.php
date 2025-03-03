@extends('admin.layouts.app')
@section('page_title', 'Role | Add')
@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Role Manager</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ul>
                    </div>
                     <div class="col d-flex justify-content-end align-items-center">
                        <a href="{{route('admin.roles.index')}}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-lg-12 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Role Add Form</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.roles.store') }}" method="post" id="myForm">
                                @csrf
                                <div class="input-block mb-3 row">
                                    <label class="col-form-label col-md-2">Role Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name') }}">
                                        @error('name')
                                            <span style="color:red">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="input-block mb-3 row">
                                    @php
                                    $currentHeading = null;
                                    @endphp

                                    @foreach ($permission as $key => $value)
                                        @php
                                            $heading = explode('-', $value->name)[0];
                                            $class = "class='roleHead text-capitalize'";
                                            if ($heading !== $currentHeading) {
                                                echo "<br/><strong {$class}>* {$heading} Permission</strong><br/>";
                                                $currentHeading = $heading;
                                            }
                                        @endphp
                                        <input type="checkbox" name="permission[]"  value="{{ $value->id ?? '' }}">&nbsp; {{ $value->name }}<br/>
                                        @error('permission[]')<p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                    @endforeach
                                </div>

                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <button type="button" class="btn btn-light" onclick="resetForm()">Reset</button>   
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
    document.getElementById('myForm').reset();
  }
</script>
@endpush
