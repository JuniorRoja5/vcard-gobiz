@php
// Page content
use Illuminate\Support\Facades\DB;
use App\Page;

$config = DB::table('config')->get();
$supportPage = DB::table('pages')->where('page_name', 'footer')->orWhere('page_name', 'contact')->get();
$pages = Page::get();

// Default
$navbar = true;
$footer = true;

if ($config[38]->config_value == "no") { 
    // $navbar = false;
    $footer = false;
}
@endphp

@extends('layouts.index', ['nav' => $navbar, 'banner' => false, 'footer' => $footer, 'cookie' => false, 'setting' => true, 'title' => true, 'title' => __('Sign Up')])

@section('content')
{{-- Register page --}}
<section class="pt-12 lg:pb-20 overflow-hidden">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap items-center -m-6">
            <div class="w-full md:w-1/2 p-6 lg:block hidden">
                <div class="p-1 mx-auto max-w-max overflow-hidden rounded-full">
                    <img class="object-cover rounded-full" src="{{ asset($config[13]->config_value) }}" alt="{{ $config[0]->config_value }}">
                </div>
            </div>
            <div class="w-full md:w-1/2 p-6">
                <div class="md:max-w-md">
                    <h2 class="mb-3 font-heading font-bold text-6xl sm:text-7xl">{{ __('Sign Up') }}</h2>
                    <p class="mb-8 text-lg">{{ __('Join the digital business card revolution and simplify your networking.') }}</p>

                    {{-- Register form --}}
                    <form method="POST" action="{{ route('register') }}">
                        @csrf  
                        <div class="flex flex-wrap -m-2 mb-6">
                            {{-- Name --}}
                            <div class="w-full p-2">
                                <p class="mb-2.5 font-medium text-base">{{ __('Full Name') }} <span class="text-red-500">*</span></p>
                                <div
                                    class="p-px bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 focus:ring-4 focus:ring-{{ $config[11]->config_value }}-300 rounded-lg">
                                    <input class="w-full px-6 py-4 placeholder-gray-500 text-base outline-none rounded-lg @error('name') is-invalid @enderror"
                                        type="text" placeholder="{{ __('Your name') }}" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                </div>
                            </div>

                            @error('name')
                            <span class="invalid-feedback mx-2 text-red-500" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            {{-- Email --}}
                            <div class="w-full p-2">
                                <p class="mb-2.5 font-medium text-base">{{ __('Email address') }} <span class="text-red-500">*</span></p>
                                <div
                                    class="p-px bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 focus:ring-4 focus:ring-{{ $config[11]->config_value }}-300 rounded-lg @error('email') is-invalid @enderror">
                                    <input class="w-full px-6 py-4 placeholder-gray-500 text-base outline-none rounded-lg"
                                        type="email" placeholder="{{ __('Your email address') }}" name="email" value="{{ old('email') }}" required autocomplete="email">
                                </div>
                            </div>

                            @error('email')
                            <span class="invalid-feedback mx-2 text-red-500" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            {{-- Mobile Number with country code --}}
                            <div class="w-full p-2">
                                <p class="mb-2.5 font-medium text-base">{{ __('Mobile Number with country code') }}</p>
                                <div
                                    class="p-px bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 focus:ring-4 focus:ring-{{ $config[11]->config_value }}-300 rounded-lg">
                                    <input class="w-full px-6 py-4 placeholder-gray-500 text-base outline-none rounded-lg @error('mobile_number') is-invalid @enderror"
                                        type="number" placeholder="880123456789" name="mobile_number" id="mobile_number" autocomplete="mobile_number">
                                </div>
                            </div>

                            @error('mobile_number')
                            <span class="invalid-feedback mx-2 text-red-500" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            {{-- Password --}}
                            <div class="w-full p-2">
                                <p class="mb-2.5 font-medium text-base">{{ __('Password') }} <span class="text-red-500">*</span></p>
                                <div
                                    class="p-px bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 focus:ring-4 focus:ring-{{ $config[11]->config_value }}-300 rounded-lg">
                                    <input class="w-full px-6 py-4 placeholder-gray-500 text-base outline-none rounded-lg @error('password') is-invalid @enderror"
                                        type="password" placeholder="********" name="password" id="password" required autocomplete="new-password">
                                </div>
                            </div>
                            {{-- Show password --}}
                            <div class="mb-4">
                                <a class="ml-2 text-xs text-gray-800 font-medium float-right cursor-pointer" title="Show password"
                                    data-bs-toggle="tooltip" onclick="showPassword()">{{ __('Show / Hide Password')}}</a>
                            </div>

                            @error('password')
                            <span class="invalid-feedback mx-2 text-red-500" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            {{-- Confirm Password --}}
                            <div class="w-full p-2">
                                <p class="mb-2.5 font-medium text-base">{{ __('Confirm Password') }} <span class="text-red-500">*</span></p>
                                <div
                                    class="p-px bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 focus:ring-4 focus:ring-{{ $config[11]->config_value }}-300 rounded-lg">
                                    <input class="w-full px-6 py-4 placeholder-gray-500 text-base outline-none rounded-lg @error('password') is-invalid @enderror"
                                        type="password" placeholder="********" name="password_confirmation" id="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            @error('password')
                            <span class="invalid-feedback mx-2 text-red-500" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            @if ($config[80]->config_value == '1')
                                {{-- Referral Code --}}
                                <div class="w-full p-2">
                                    <p class="mb-2.5 font-medium text-base">{{ __('Referral Code') }}</p>
                                    <div
                                        class="p-px bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 focus:ring-4 focus:ring-{{ $config[11]->config_value }}-300 rounded-lg">
                                        <input class="w-full px-6 py-4 placeholder-gray-500 text-base outline-none rounded-lg uppercase @error('referral_code') is-invalid @enderror"
                                            type="text" placeholder="FNKLJ2156DV" name="referral_code" id="referral_code" value="{{ request()->get('ref') }}">
                                    </div>
                                </div>

                                @error('referral_code')
                                <span class="invalid-feedback mx-2 text-red-500" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            @endif

                            {{-- Recaptcha --}}
                            @if ($settings->recaptcha_configuration['RECAPTCHA_ENABLE'] == 'on')
                            <div class="w-full p-2">
                                {!! htmlFormSnippet() !!}
                            </div>
                            @endif
                        </div>
                        <div class="flex flex-wrap -m-1.5 mb-1">
                            <div class="w-auto p-1.5">
                                <input class="w-4 h-4" type="checkbox" name="terms" id="terms" checked>
                            </div>
                            <div class="flex-1 p-1.5">
                                <p class="text-gray-500 text-sm">
                                    <span>{{ __('I agree to the') }}</span>
                                    @if ($pages[108]->page_name == 'terms' && $pages[108]->status == 'active')
                                    <a class="hover:text-gray-800 hover:font-bold" href="{{ route('terms.and.conditions') }}">{{ __('Terms & Conditions') }}</a>
                                    @else
                                    <a class="hover:text-gray-800 hover:font-bold" href="#">{{ __('Terms & Conditions') }}</a>
                                    @endif
                                    <span>{{ __('of') }} {{ config('app.name') }}.</span>
                                </p>
                            </div>
                        </div>

                        @error('terms')
                            <span class="invalid-feedback mx-2 text-red-500" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        
                        <div class="group relative md:max-w-max my-5">
                            <div
                                class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 opacity-0 group-hover:opacity-50 rounded-full transition ease-out duration-300">
                            </div>
                            <button
                                class="p-1 w-full font-heading font-semibold text-xs text-white uppercase tracking-px overflow-hidden rounded-full">
                                <div class="relative py-5 px-14 bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 overflow-hidden rounded-full">
                                    <div
                                        class="absolute top-0 left-0 transform -translate-y-full group-hover:-translate-y-0 h-full w-full bg-white transition ease-in-out duration-500">
                                    </div>
                                    <p class="relative z-10 group-hover:text-gray-900">{{ __('Sign Up') }}</p>
                                </div>
                            </button>
                        </div>
                    </form>
                    {{-- Signin with Google --}}
                    @if ($settings->google_configuration['GOOGLE_ENABLE'] == 'on')
                    <h2 class="mb-3 font-heading font-bold text-2xl sm:text-2xl text-center sm:text-left">{{ __('Or sign up instantly with Google') }}</h2>
                        <div class="group relative md:max-w-max mb-5">
                            <div
                                class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 opacity-0 group-hover:opacity-50 rounded-full transition ease-out duration-300">
                            </div>
                            <a href="{{ route('login.google') }}">
                                <button
                                    class="p-1 w-full font-heading font-semibold text-xs text-white uppercase tracking-px overflow-hidden rounded-full">
                                    <div class="relative flex py-5 px-14 bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 overflow-hidden rounded-full">
                                        <i class="ti ti-brand-google brand-google items-center px-3"></i> {{ __('Continue with Google') }}
                                    </div>
                                </button>
                            </a>
                        </div>
                    @endif
                    <p class="text-gray-500 text-sm">
                        <span>{{ __('Already have an account?') }}</span>
                        <a class="hover:text-gray-800 hover:font-bold" href="{{ route('login') }}">{{ __('Login now') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Custom JS --}}
@section('custom-js')
<script>
function showPassword() {
    "use strict";
    var password = document.getElementById("password");
    var confirmPassword = document.getElementById("password_confirmation");
    if (password.type === "password") {
        password.type = "text";
        confirmPassword.type = "text";
    } else {
        password.type = "password";
        confirmPassword.type = "password";
    }
}
</script>
@endsection
@endsection