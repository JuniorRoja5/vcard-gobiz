@extends('layouts.index', ['nav' => true, 'banner' => false, 'footer' => true, 'cookie' => true, 'setting' => true, 'title' => __('We\'ll Be Right Back!')])

@php
use Illuminate\Support\Facades\DB;
use App\Setting;

// Queries
$config = DB::table('config')->get();
$settings = Setting::where('status', 1)->first();
$supportPage = DB::table('pages')->where('page_name', 'footer')->orWhere('page_name', 'contact')->get();
@endphp

@section('content')
    <section class="lg:pt-16 pt-10 lg:pb-16 pb-2 overflow-hidden">
        <div class="container mx-auto px-4 py-5">
            <div class="flex flex-wrap items-center -m-4 lg:px-20">
                <div class="w-full lg:w-1/2 p-6">
                    <div class="lg:max-w-xl">
                        <h1 class="mb-6 font-heading text-7xl md:text-10xl xl:text-12xl text-gray-900 font-bold">
                            {{ __('We\'ll Be Right Back!') }}</h1>
                        <p class="mb-9 text-gray-600 text-lg">{{ __('We are currently undergoing maintenance. Please check back later.') }}</p>
                    </div>
                </div>
                <div class="w-full lg:w-1/2 p-6 hidden lg:block">
                    <img class="block mx-auto" src="{{ asset('img/maintenance-mode.svg') }}" alt="{{ $settings->site_name }}">
                </div>
            </div>
        </div>
    </section>
@endsection
