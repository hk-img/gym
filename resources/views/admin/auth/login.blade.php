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
                    <h3 class="account-title">Login</h3>
                    <p class="account-subtitle">Access to our dashboard</p>

                    <!-- Account Form -->
                    <form action="{{ route('admin.login') }}"
                        method="POST">
                        @csrf
                        <div class="input-block mb-4">
                            <label class="col-form-label">Email Address</label>
                            <input class="form-control" type="text" value="{{ old('email') ?? '' }}" name="email" id="email">
                            @error('email')
                                <div class="text-danger pt-2">
                                    {{$message}}
                                </div>
                            @enderror
                            @error('status')
                                <div class="text-danger pt-2">
                                    {{ $errors->first('status') }}
                                </div>
                            @enderror
                        </div>
                        <div class="input-block mb-4">
                            <div class="row align-items-center">
                                <div class="col">
                                    <label class="col-form-label">Password</label>
                                </div>
                                <div class="col-auto">
                                    <a class="text-muted" href="{{ route('admin.password.request') }}">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>
                            <div class="position-relative">
                                <input class="form-control" type="password" value="{{ old('password') ?? '' }}" id="password" name="password">
                                <span class="fa-solid fa-eye-slash" id="toggle-password"></span>
                            </div>
                            @error('password')
                                <div class="text-danger pt-2">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="input-block mb-4">
                            <input class="form-check-input" name="remember" type="checkbox" id="rememberMe">
                            <label class="col-form-label">Remember me</label>
                        </div>
                        <div class="input-block mb-4 text-center">
                            <button class="btn btn-primary account-btn" type="submit">Login</button>
                        </div>
                    </form>
                    <!-- /Account Form -->
                </div>
            </div>
        </div>
    </div>
@endsection