@extends('admin.layouts.app')
@section('page_title', 'Brand Manager | Edit')
@section('content')
<div class="page-wrapper">

    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col">
                    <h3 class="page-title">Brand Manager</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                </div>
                <div class="col d-flex justify-content-end align-items-center">
                    <a href="{{route('admin.brands.index')}}"><button type="button" class="btn btn-primary me-2">Back</button></a>
                </div>
            </div>
            
        </div>
        <!-- /Page Header -->

      
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Brands Edit Form</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.brands.update', $data->id) }}" enctype="multipart/form-data"  id="updatedata">
                            @csrf
                            @method('patch')
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Type<span class="text-danger"> *</span></label>
                                <div class="col-md-10">
                                    <select class="placeholder typeList form-control" name="type">
                                    </select>
                                    @error('type') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Name<span class="text-danger"> *</span></label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="name" placeholder="Enter Brand Name" value="{{ $data->name }}">
                                    @error('name') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Description<span class="text-danger"> *</span></label>
                                <div class="col-md-10">
                                    <textarea rows="5" cols="5" class="form-control" name="description" placeholder="Write Description..." id="ckeditor">{{ $data->description }}</textarea>
                                    @error('description') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Logo</label>
                                <div class="col-md-10">
                                    <div class="custom-file-container" data-upload-id="myFirstImage">
                                        <label>Upload (Single File) <a href="javascript:void(0)" class="custom-file-container__image-clear"
                                                title="Clear Image">x</a></label>
                                        <label class="custom-file-container__custom-file">
                                            <input type="file" class="custom-file-container__custom-file__custom-file-input"
                                                name="logo" accept="image/*">
                                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                                        </label>
                                        <div class="custom-file-container__image-preview"></div>
                                    </div>
                                    @error('logo') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                </div>
                            </div>
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Preview</label>
                                <div class="col-md-10 d-flex flex-wrap gap-3">
                                    <div class="image-container position-relative">
                                        <img src="{{ $data->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/placeholder.jpg') }}" width="150" height="150" class="img-thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="input-block mb-3 row">
                                <div class="col-md-12 d-flex justify-content-end align-items-center">
                                    <a href="#" class="add-new add-pipeline-btn" id="addFaq"><i class="la la-plus-circle me-2"></i>Add New</a>
                                </div>
                            </div>
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">FAQs<span class="text-danger">*</span></label>
                                <div class="col-md-10" id="faqList">
                                    @forelse( $data->faqs as $key => $item )
                                        <div class="row mb-2">
                                                <input type="hidden" name="ids[]" class="form-control" value="{{$item->id}}" placeholder="Write Question" id="id">
                                            <div class="col-md-6">
                                                <input type="text" name="question[]" class="form-control" value="{{$item->question}}" placeholder="Write Question" id="question" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="answer[]" class="form-control" value="{{$item->answer}}" placeholder="Write Answer" id="answer" required>
                                            </div>
                                            @if($key != 0)
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger delete-faq"><i class="las la-trash"></i></button>
                                            </div>
                                            @endif
                                        </div>
                                        @empty
                                        {{-- <div class="row mb-2">
                                            <div class="col-md-6">
                                                <input type="text" name="question[]" class="form-control" placeholder="Write Question" id="question">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="answer[]" class="form-control" placeholder="Write Answer" id="answer">
                                            </div>
                                        </div> --}}
                                    @endforelse
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary me-2">Update</button>
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
    var selectedTypeId = "{{ $data->type_id ?? '' }}"; // Pre-selected ID from Laravel
    var selectedTypeName = "{{ $data->type->name ?? '' }}"; // Pre-selected name from Laravel

    document.addEventListener('DOMContentLoaded', function() {
        initializeSelect2('.typeList', "{{ route('admin.type.list') }}", 'Select Type');
    });

     // Preselect the item
    if (selectedTypeId) {
        var option = new Option(selectedTypeName, selectedTypeId, true, true);
        $('.typeList').append(option).trigger('change');
    }   

    $(document).ready(function() {
        // When the "Add New FAQ" button is clicked
        $('#addFaq').click(function() {
            // Create new input fields for question and answer
            var newFaq = `
                <div class="row mb-2">
                        <input type="hidden" name="ids[]" class="form-control" value="" placeholder="Write Question" id="id">
                    <div class="col-md-6">
                        <input type="text" name="question[]" class="form-control" placeholder="Write Question" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="answer[]" class="form-control" placeholder="Write Answer" required>
                    </div>
                     <div class="col-md-1">
                        <button type="button" class="btn btn-danger delete-faq"><i class="las la-trash"></i></button>
                    </div>
                </div>
            `;
            // Append the new fields to the FAQ list
            $('#faqList').append(newFaq);
        });
    });

    $(document).on('click','.delete-faq',function(){
        $(this).closest('.row').remove();
    })

</script>
@endpush