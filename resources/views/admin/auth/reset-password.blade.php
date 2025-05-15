@extends('admin.layouts.app')

@section('content')
<div class="account-content py-5" style="background: #f7f7f7; min-height: 100vh;">
    <div class="container">
        <!-- Logo -->
        <div class="account-logo text-center mb-4">
            <a href="javascript:void(0);">
                <img src="{{ asset('assets/img/gym_logo.png') }}" alt="Logo" style="height: 80px;">
            </a>
        </div>

        <!-- Form Box -->
        <div class="account-box mx-auto" style="max-width: 500px; background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="account-wrapper">
                <h3 class="account-title text-center">Reset Your Password</h3>
                <p class="account-subtitle text-center mb-4">Enter your email and new password</p>

                <!-- Reset Password Form -->
                <form action="{{ route('admin.password.store') }}" method="POST">
                    @csrf

                    <!-- Hidden token -->
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', request()->email) }}" required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                   <!-- New Password -->
                    <div class="mb-3 position-relative">
                        <label class="form-label">New Password</label>
                        <input class="form-control" type="password" name="password" id="password" required>
                        <span class="position-absolute  end-0 translate-middle-y me-3" onclick="togglePassword('password', this)" style="cursor: pointer; top: 71%;">
                            <i class="fa fa-eye-slash" id="toggleIcon-password"></i>
                        </span>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4 position-relative">
                        <label class="form-label">Confirm Password</label>
                        <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required>
                        <span class="position-absolute  end-0 translate-middle-y me-3" onclick="togglePassword('password_confirmation', this)" style="cursor: pointer; top: 71%;">
                            <i class="fa fa-eye-slash" id="toggleIcon-password_confirmation"></i>
                        </span>
                        @error('password_confirmation')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- Submit Button -->
                    <div class="text-center">
                        <button class="btn btn-primary w-100" type="submit">Reset Password</button>
                    </div>
                </form>
                <!-- /Reset Password Form -->

                <div class="account-footer mt-4 text-center">
                    <p>Back to <a href="{{ route('admin.login') }}">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function togglePassword(fieldId, iconElement) {
    const input = document.getElementById(fieldId);
    const icon = iconElement.querySelector('i');

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>
@endsection
