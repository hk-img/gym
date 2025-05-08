
@extends('admin.layouts.app')
@section('content')
    <div class="account-content">
            <div class="container">

                <!-- Account Logo -->
                <div class="account-logo">
                    <a href="javascript:void(0);"><img src="{{asset('assets/img/gym_logo.png')}}" alt="Dreamguy's Technologies"></a>
                </div>
                <!-- /Account Logo -->

                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Forgot Password?</h3>
                        <p class="account-subtitle">Enter your email to get a password reset link</p>

                        <!-- Account Form -->
                        <form action="{{ route('admin.password.email') }}"  method="POST">
                            @csrf
                            <div class="input-block mb-4">
                                <label class="col-form-label">Email Address</label>
                                <input class="form-control" type="email" name="email" value="{{ old('email') ?? '' }}">
                            </div>
                             @error('email')
                                <div class="text-danger pt-2">
                                    {{$message}}
                                </div>
                            @enderror
                            <div class="input-block mb-4 text-center">
                                <button class="btn btn-primary account-btn" type="submit">Reset Password</button>
                            </div>
                            <div class="account-footer">
                                <p>Remember your password? <a href="{{route('admin.login')}}">Login</a></p>
                            </div>
                        </form>
                        <!-- /Account Form -->

                    </div>
                </div>
            </div>
        </div>
@endsection


