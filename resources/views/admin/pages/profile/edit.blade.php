@extends('admin.layouts.app')
@section('page_title', 'Profile | Edit')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Profile</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->


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
                                                <h6 class="text-muted">Admin</h6>
                                                {{-- <h6 class="text-muted">UI/UX Design Team</h6>
                                                <small class="text-muted">Web Designer</small>
                                                <div class="staff-id">Employee ID : FT-0001</div>
                                                <div class="small doj text-muted">Date of Join : 1st Jan 2013</div> --}}
                                                {{-- <div class="staff-msg"><a class="btn btn-custom" href="chat.html">Send
                                                        Message</a></div> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="personal-info">
                                                <li>
                                                    <div class="title">Phone:</div>
                                                    <div class="text"><a href="javacript:void(0);">9876543210</a></div>
                                                </li>
                                                <li>
                                                    <div class="title">Email:</div>
                                                    <div class="text"><a href="javacript:void(0);"><span class="__cf_email__">{{ $user->email }}</span></a>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="title">Address:</div>
                                                    <div class="text">{{ $user->address }}</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="pro-edit"><a data-bs-target="#profile_info" data-bs-toggle="modal"
                                        class="edit-icon" href="javacript:void(0);"><i class="fa fa-pencil"></i></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card tab-box">
                <div class="row user-tabs">
                    <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                        <ul class="nav nav-tabs nav-tabs-bottom">
                            <li class="nav-item"><a href="#working_hours" data-bs-toggle="tab"
                                    class="nav-link active">Working Hours</a>
                            </li>
                            <li class="nav-item"><a href="#social_links" data-bs-toggle="tab" class="nav-link">Our Socials</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="tab-content">
                <!-- Working Hour Tab -->
                <div class="tab-pane fade show active" id="working_hours">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Gym Working Hours</h3>
                            <form method="POST" action="{{ route('admin.profile.updateGymHours') }}">
                                @csrf
                                <div class="row">
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $index => $day)
                                        <div class="col-sm-4">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">{{ $day }} <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Open</span>
                                                    <input type="time" name="working_hours[{{ $day }}][open_time]" 
                                                        value="{{ old('working_hours.' . $day . '.open_time', isset($workingHours[$index]) ? \Carbon\Carbon::parse($workingHours[$index]->open_time)->format('H:i') : '')  }}"
                                                        class="form-control @error('working_hours.' . $day . '.open_time') is-invalid @enderror">
                                                    @error('working_hours.' . $day . '.open_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="input-group mt-2">
                                                    <span class="input-group-text">Close</span>
                                                    <input type="time" name="working_hours[{{ $day }}][close_time]" 
                                                        value="{{ old('working_hours.' . $day . '.close_time', isset($workingHours[$index]) ? \Carbon\Carbon::parse($workingHours[$index]->close_time)->format('H:i') : '')  }}"
                                                        class="form-control @error('working_hours.' . $day . '.close_time') is-invalid @enderror">
                                                    @error('working_hours.' . $day . '.close_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input type="checkbox" name="working_hours[{{ $day }}][is_closed]" 
                                                        class="form-check-input" id="closed_{{ $day }}" 
                                                        {{ old('working_hours.' . $day . '.is_closed', isset($workingHours[$index]) && $workingHours[$index]->is_closed ? 'checked' : '') }}>
                                                    <label class="form-check-label" for="closed_{{ $day }}">Closed</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn" type="submit">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Working Hour Tab -->

                <!-- Social Links Tab -->
                <div class="tab-pane fade" id="social_links">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Social Media Links</h3>
                            <form method="POST" action="{{ route('admin.profile.updateSocialLinks') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Facebook</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                                <input type="url" name="facebook" class="form-control" 
                                                    placeholder="https://facebook.com/yourprofile"
                                                    value="{{ old('facebook', $socialLinks->facebook ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Twitter</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                                <input type="url" name="twitter" class="form-control" 
                                                    placeholder="https://twitter.com/yourprofile"
                                                    value="{{ old('twitter', $socialLinks->twitter ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Instagram</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                                <input type="url" name="instagram" class="form-control" 
                                                    placeholder="https://instagram.com/yourprofile"
                                                    value="{{ old('instagram', $socialLinks->instagram ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">LinkedIn</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                                <input type="url" name="linkedin" class="form-control" 
                                                    placeholder="https://linkedin.com/in/yourprofile"
                                                    value="{{ old('linkedin', $socialLinks->linkedin ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">YouTube</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                                                <input type="url" name="youtube" class="form-control" 
                                                    placeholder="https://youtube.com/yourchannel"
                                                    value="{{ old('youtube', $socialLinks->youtube ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn" type="submit">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Social Links Tab -->
            </div>
        </div>
        <!-- /Page Content -->

        <!-- Profile Modal -->
        <div id="profile_info" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Profile Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form enctype="multipart/form-data"  id="profileUpdateForm" >
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                     <div class="profile-img-wrap edit-img">
                                        <img id="profileImagePreview" class="inline-block"
                                            src="{{ $user->getFirstMediaUrl('images', 'thumb') ?: asset('assets/img/user.jpg') }}" 
                                            alt="user">
                                        <div class="fileupload btn">
                                            <span class="btn-text">edit</span>
                                            <input class="upload" type="file" name="image" id="profileImage" accept="image/*">
                                            <p id="imageError" class="text-danger text-xs pt-1"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Name</label>
                                                <input type="text" class="form-control" value="{{ $user->name }}" name="name" id="name">
                                                <p id="nameError" class="text-danger text-xs pt-1"></p>
                                            </div>
                                            <div class="input-block mb-3">
                                                <label class="col-form-label">Address</label>
                                                <textarea class="form-control"
                                                    name="address">{{ $user->address }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Profile Modal -->
    </div>
@endsection
@push('custom-script')
    <script>
        $(document).ready(function() {
             $('#profileUpdateForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                formData.append('_method', 'PATCH'); // For Laravel Patch Request

                $.ajax({
                    url: "{{ route('admin.profile.update', $user->id) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.text-danger').text(''); // Clear any previous errors
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.success,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // Reload the page after success
                            }
                        });
                    },
                    error: function(response) {
                        let errors = response.responseJSON.errors;
                        if (errors.name) {
                            $('#nameError').text(errors.name[0]);
                        }
                        if (errors.image) {
                            $('#imageError').text(errors.image[0]);
                        }
                    }
                });
            });

            $('.upload').change(function(event) {
                var file = event.target.files[0];
                if (file) {
                    var fileType = file.type;
                    if (fileType === "image/jpeg" || fileType === "image/png" || fileType === "image/jpg") {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $(".edit-img img").attr("src", e.target.result);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert("Please upload a valid image file (jpg, jpeg, or png).");
                    }
                }
            });
        });
    </script>
@endpush
