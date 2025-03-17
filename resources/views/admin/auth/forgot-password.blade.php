
@extends('admin.layouts.app')
@section('content')
    {{-- <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <div class="card card-plain">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="font-weight-bolder">Sign In</h4>
                                    <p class="mb-0">Enter your email and password to sign in</p>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf
                                        
                                        <div class="flex flex-col mb-3">
                                            <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') ?? '' }}" aria-label="Email">
                                            @error('email') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                        </div>
                                        <!-- Email Address -->
                                        <!-- <div>
                                            <x-input-label for="email" :value="__('Email')" />
                                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div> -->
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">{{ __('Email Password Reset Link') }}</button>
                                        </div>

                                        <!-- <div class="flex items-center justify-end mt-4">
                                            <x-primary-button>
                                                {{ __('Email Password Reset Link') }}
                                            </x-primary-button>
                                        </div> -->
                                    </form>
                                </div>
                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-4 text-sm mx-auto">
                                        Back to Sign in?
                                        <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">here</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                                style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg');
              background-size: cover;">
                                <span class="mask bg-gradient-primary opacity-6"></span>
                                <h4 class="mt-5 text-white font-weight-bolder position-relative">"Attention is the new
                                    currency"</h4>
                                <p class="text-white position-relative">The more effortless the writing looks, the more
                                    effort the writer actually put into the process.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main> --}}
    <div class="account-content">
            <div class="container">

                <!-- Account Logo -->
                <div class="account-logo">
                    <a href="admin-dashboard.html"><img src="{{asset('assets/img/gym_logo.png')}}" alt="Dreamguy's Technologies"></a>
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


