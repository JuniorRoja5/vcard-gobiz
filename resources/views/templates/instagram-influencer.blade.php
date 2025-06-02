<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $card_details->title }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ url($business_card_details->profile) }}" sizes="512x512" type="image/png" />
    <link rel="apple-touch-icon" href="{{ url($business_card_details->profile) }}">

    <meta name="theme-color" content="#EFFAF4" />

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="application-name" content="{{ $card_details->title }}">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-title" content="{{ $card_details->title }}">

    <!-- Tile for Win8 -->
    <meta name="msapplication-TileColor" content="#EFFAF4">
    <meta name="msapplication-TileImage" content="{{ url($business_card_details->profile) }}">

    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! JsonLd::generate() !!}

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ url('templates/css/insta-influencer.css') }}">
    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="{{ url('css/swiper-bundle.min.css') }}">
    {{-- Fontawesome CSS --}}
    <link rel="stylesheet" href="{{ url('css/fontawesome.min.css') }}" />
    {{-- AOS CSS --}}
    <link rel="stylesheet" href="{{ url('css/aos.css') }}" />

    {{-- Google Fonts: Rethink --}}
    <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:wght@400;500;600&display=swap" rel="stylesheet" />

    <!-- Include the qrious library -->
    <script src="{{ url('js/qrious.min.js') }}"></script>

    {{-- AOS JS --}}
    <script src="{{ url('js/aos.js') }}"></script>

    <style>
        body {
            font-family: "Rethink Sans", sans-serif;
        }

        button:active {
            transform: scale(0.97);
        }

        a:active {
            transform: scale(0.97);
        }
    </style>

    <!-- Flatpickr CSS -->
    <link href="{{ url('css/flatpickr.min.css') }}" rel="stylesheet">
    {{-- Check business details --}}
    @if ($business_card_details != null)
        <style>
            {!! $business_card_details->custom_css !!}
        </style>
    @endif

    {{-- Check PWA --}}
    @if ($plan_details != null)
        @if ($plan_details['pwa'] == 1)
            @laravelPWA

            <!-- Web Application Manifest -->
            <link rel="manifest" href="{{ $manifest }}">
        @endif
    @endif
</head>

@php
    use Illuminate\Support\Facades\Session;
@endphp

<body class="bg-gray-50 min-h-screen"
    dir="{{ App::isLocale('ar') || App::isLocale('ur') || App::isLocale('he') ? 'rtl' : 'ltr' }}">
    <div class="container max-w-2xl mx-auto lg:py-8">
        {{-- Start Check password protected --}}
        @if ($business_card_details->password == null || Session::get('password_protected') == true)
            {{-- Check business details --}}
            @if ($business_card_details != null)
                <div class="bg-white lg:rounded-2xl shadow-[0_0_4px_rgba(0,0,0,0.1)] overflow-hidden"
                    data-aos="fade-up">
                    <div class="relative -mt-0.5 pb-28 lg:pb-6">
                        <!-- Profile Info -->

                        <div class="pb-2 pt-12 flex justify-center items-center border-b border-b-gray-300">
                            <img src="{{ url('img/templates/insta-influencer/head.png') }}" alt=""
                                class="w-56 object-cover" />
                        </div>

                        {{-- Profile Image --}}
                        <div class="relative z-20 bg-transparent pt-8 flex px-6 lg:px-8">
                            <div class="w-4/12">
                                <div class="w-32 h-32 lg:w-40 lg:h-40 relative -ml-1 ">
                                    <!-- Background Image -->
                                    <div style="background-image: url('{{ asset('img/templates/insta-influencer/profile-border.png') }}');"
                                        class="w-full h-full bg-cover absolute top-0 left-0 z-20">
                                    </div>
                                    <!-- Foreground Image -->
                                    <img src="{{ url($business_card_details->profile) }}"
                                        alt="{{ $business_card_details->title }}"
                                        class="w-[125px] h-[125px] md:w-40 md:h-40 rounded-full object-cover absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10" />
                                </div>
                            </div>
                            <div
                                class="flex flex-col justify-start items-start ltr:ml-6 rtl:mr-6 ltr:lg:ml-9 rtl:lg:mr-9 ">
                                {{-- Name --}}
                                <h1 class="text-4xl lg:text-5xl font-extrabold head">
                                    {{ $business_card_details->title }}
                                </h1>
                                {{-- Job Title --}}
                                <p class="text-black font-medium text-lg text-gray-700">
                                    {{ $card_details->sub_title }}
                                </p>
                                {{-- About --}}
                                @if (optional($business_card_details)->description || optional($business_card_details)->address)
                                    <span class="mt-2 text-md text-gray-500 line-clamp-3">
                                        {!! $business_card_details->description !!}
                                    </span>
                                @endif

                                <!-- Start Quick Contact -->
                                @if (count($feature_details) > 0)
                                    <div class="grid grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4 mt-6">
                                        {{-- Loop through the feature_details array and display the icons --}}
                                        @foreach ($feature_details as $feature)
                                            @if (in_array($feature->type, ['email', 'threads', 'instagram']))
                                                {{--  Email --}}
                                                @if ($feature->type == 'email')
                                                    <a href="mailto:{{ $feature->content }}"
                                                        class="rounded-full bg-gray-100 hover:bg-gray-200 border border-gray-300 flex items-center justify-center w-12 h-12 focus:outline-none">
                                                        <i class="{{ $feature->icon }} fa-xl text-gray-800"></i>
                                                    </a>
                                                @endif
                                                {{-- Threads --}}
                                                @if ($feature->type == 'threads')
                                                    <a href="{{ $feature->content }}" target="_blank"
                                                        class="rounded-full bg-gray-100 hover:bg-gray-200 border border-gray-300 flex items-center justify-center w-12 h-12 focus:outline-none">
                                                        <i class="{{ $feature->icon }} fa-xl text-gray-800"></i>
                                                    </a>
                                                @endif
                                                {{-- Instagram --}}
                                                @if ($feature->type == 'instagram')
                                                    <a href="{{ $feature->content }}" target="_blank"
                                                        class="rounded-full bg-gray-100 hover:bg-gray-200 border border-gray-300 flex items-center justify-center w-12 h-12 focus:outline-none">
                                                        <i class="{{ $feature->icon }} fa-xl text-gray-800"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                {{-- End Quick Contact --}}
                            </div>
                        </div>
                        <!-- End Profile Info -->

                        <!-- Start Cover Image Section -->
                        @if ($business_card_details->cover_type == 'none')
                            <div class="lg:h-80 h-60 relative pt-6 px-6 lg:px-8" id="profile">
                                {{-- Cover Image --}}
                                <img src="{{ url('img/templates/insta-influencer/banner.png') }}"
                                    alt="{{ $business_card_details->title }}"
                                    class="w-full h-full object-cover rounded-xl" />
                            </div>
                        @endif
                        <!-- End Cover Image Section -->

                        <!-- Start Cover Image Section -->
                        @if ($business_card_details->cover_type == 'photo')
                            <div class="lg:h-80 h-60 relative pt-6 px-6 lg:px-8" id="profile">
                                {{-- Cover Image --}}
                                <img src="{{ $business_card_details->cover ? url($business_card_details->cover) : asset('images/default-cover.png') }}"
                                    alt="{{ $business_card_details->title }}"
                                    class="w-full h-full object-cover rounded-xl" />
                            </div>
                        @endif
                        <!-- End Cover Image Section -->

                        <!-- Start Cover Video Section (Vimeo AP) -->
                        @if ($business_card_details->cover_type == 'vimeo-ap')
                            <div class="relative lg:h-80 h-60 w-full mt-6" id="profile">
                                {{-- Cover Video --}}
                                <iframe
                                    src="https://player.vimeo.com/video/{{ $business_card_details->cover }}?autoplay=1&loop=1&autopause=0&muted=1&controls=0"
                                    id="vid-player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen class="absolute top-0 left-0 w-full h-full px-6 lg:px-8 rounded-xl">
                                </iframe>
                            </div>
                        @endif
                        <!-- End Cover Video Section (Vimeo AP) -->

                        <!-- Start Cover Video Section (Vimeo) -->
                        @if ($business_card_details->cover_type == 'vimeo')
                            <div class="relative lg:h-80 h-60 w-full mt-6" id="profile">
                                {{-- Cover Video --}}
                                <iframe
                                    src="https://player.vimeo.com/video/{{ $business_card_details->cover }}?autoplay=0&loop=1&autopause=0&muted=0&controls=1"
                                    id="vid-player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen class="absolute top-0 left-0 w-full h-full px-6 lg:px-8 rounded-xl">
                                </iframe>
                            </div>
                        @endif
                        <!-- End Cover Video Section (Vimeo) -->

                        <!-- Start Cover Video Section (Youtube AP) -->
                        @if ($business_card_details->cover_type == 'youtube-ap')
                            <div class="relative lg:h-80 h-60 w-full mt-6" id="profile">
                                {{-- Cover Video --}}
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $business_card_details->cover }}?autoplay=1&mute=1&controls=0&loop=1"
                                    id="vid-player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    class="absolute top-0 left-0 w-full h-full px-6 lg:px-8 rounded-xl">
                                </iframe>
                            </div>
                        @endif
                        <!-- End Cover Video Section (Youtube AP) -->

                        <!-- Start Cover Video Section -->
                        @if ($business_card_details->cover_type == 'youtube')
                            <div class="relative lg:h-80 h-60 w-full mt-6" id="profile">
                                {{-- Cover Video --}}
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $business_card_details->cover }}?autoplay=0&mute=0&controls=1&loop=1"
                                    id="vid-player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    class="absolute top-0 left-0 w-full h-full px-6 lg:px-8 rounded-xl">
                                </iframe>
                            </div>
                        @endif
                        <!-- End Cover Video Section -->

                        {{-- Language Switcher --}}
                        @include('templates.includes.language-switcher')

                        <!-- Start Location Section -->
                        @if (count($feature_details) > 0)
                            @foreach ($feature_details as $feature)
                                @if (in_array($feature->type, ['address']))
                                    <div class="grid grid-cols-1 gap-4 relative px-6 lg:px-8">
                                        <div class="mt-8 w-full ">
                                            <!-- Font Awesome Icon -->
                                            <a href="https://www.google.com/maps/place/{{ urlencode($feature->content) }}"
                                                target="_blank"
                                                class="px-6 py-4 flex flex-col bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-2xl justify-center focus:outline-none">
                                                <!-- Font Awesome Icon -->
                                                <i class="{{ $feature->icon }} fa-xl text-gray-800 text-2xl py-4"></i>
                                                <!-- Title -->
                                                <h2 class="text-gray-700 text-md font-semibold">
                                                    {{ $feature->label }}
                                                </h2>
                                                <!-- Description -->
                                                <p class="text-gray-600 text-sm flex items-center">
                                                    {{ $feature->content }}
                                                </p>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        {{-- End Location Section --}}

                        <!-- Start contact with icon, title and description -->
                        @if (!empty($feature_details) && count($feature_details) > 0)
                            @php
                                // Excluded types
                                $excludedTypes = [
                                    'email',
                                    'tel',
                                    'instagram',
                                    'address',
                                    'map',
                                    'iframe',
                                    'youtube',
                                    'facebook',
                                ];

                                // Filter the feature details to only include non-excluded types
                                $visibleFeatures = collect($feature_details)->filter(function ($feature) use (
                                    $excludedTypes,
                                ) {
                                    return isset($feature->type) && !in_array($feature->type, $excludedTypes);
                                });
                            @endphp

                            @if ($visibleFeatures->isNotEmpty())
                                <div class="relative">
                                    <img src="{{ asset('img/templates/insta-influencer/1.png') }}" alt=""
                                        class="w-24 lg:w-28 absolute top-5 -left-8 -rotate-12 opacity-70" />
                                    <h2
                                        class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                        <div
                                            class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                        </div>
                                        {{ __('Social Links') }}
                                    </h2>
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 px-6 lg:px-8">
                                        @foreach ($feature_details as $feature)
                                            {{-- Generate href value dynamically --}}
                                            @php
                                                $href = $feature->content;
                                                if ($feature->type == 'wa') {
                                                    $href = 'https://wa.me/' . $feature->content;
                                                } elseif ($feature->type == 'text') {
                                                    $href = '';
                                                }
                                            @endphp
                                            @if (!in_array($feature->type, ['instagram', 'threads', 'email', 'address', 'map', 'iframe', 'youtube']))
                                                <!-- {{ $feature->label }} -->
                                                <a href="{{ $href }}" target="_blank"
                                                    class="p-4 bg-gray-100 hover:bg-gray-200 border border-gray-300 transition-colors rounded-2xl flex flex-col">
                                                    <div class="flex items-center">
                                                        <!-- Font Awesome Icon -->
                                                        <div
                                                            class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-200 border border-gray-300 -ml-1">
                                                            <i class="{{ $feature->icon }} text-gray-800 text-lg"></i>
                                                        </div>
                                                        <!-- Title -->
                                                        <h2 class="text-gray-700 text-lg font-semibold ml-2 truncate">
                                                            {{ $feature->label }}
                                                        </h2>
                                                    </div>
                                                    <!-- Description -->
                                                    <p class="text-gray-600 text-sm truncate mt-2">
                                                        {{ $feature->content }}
                                                    </p>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                        <!-- End contact with icon, title and description -->

                        <!-- Start iframe Section -->
                        @if ($feature_details->where('type', 'iframe')->count() > 0)
                            <div class="relative px-6 lg:px-8">
                                <h2
                                    class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                    <div
                                        class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                    </div>
                                    {{ __('Iframe') }}
                                </h2>
                                <div class="grid grid-cols-1 gap-4 items-center">
                                    {{-- Iframe --}}
                                    @foreach ($feature_details as $feature)
                                        @if ($feature->type == 'iframe')
                                            <div class="overflow-hidden rounded-2xl">
                                                <iframe width="100%" height="270" src="{{ $feature->content }}"
                                                    title="{{ $feature->label }}" frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen></iframe>
                                                {{-- Add Iframe title --}}
                                                @if ($feature->label != null)
                                                    <div class="px-5 py-3 bg-gray-200">
                                                        <div class="text-[#121212] font-semibold text-lg text-center">
                                                            {{ $feature->label }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!-- End iframe Section -->

                        <!-- Start Services Section -->
                        @if (count($service_details) > 0)
                            <div class="relative px-6 lg:px-8">
                                <img src="{{ asset('img/templates/insta-influencer/2.png') }}" alt=""
                                    class="w-24 lg:w-28 absolute top-5 -right-7 rotate-12 opacity-70" />
                                <h2
                                    class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                    <div
                                        class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                    </div>
                                    {{ __('Services') }}
                                </h2>
                                <swiper-container
                                    breakpoints='{
                                    "640": { "slidesPerView": 1 }, 
                                    "1024": { "slidesPerView": 2 } 
                                    }'
                                    space-between="20" class="mySwiper" autoplay-delay="2500"
                                    autoplay-disable-on-interaction="false" loop="true">
                                    {{-- All services --}}
                                    @foreach ($service_details as $service_detail)
                                        <swiper-slide class="p-0.5">
                                            <!-- Service -->
                                            <div
                                                class="flex flex-col p-6 rounded-2xl shadow-[0_0_4px_rgba(0,0,0,0.1)]">
                                                {{-- Image --}}
                                                <img class="w-full h-48 object-cover rounded-2xl mb-2"
                                                    src="{{ url($service_detail->service_image) }}"
                                                    alt="{{ $service_detail->service_name }}" />
                                                {{-- Name --}}
                                                <h2 class="text-black text-md font-semibold">
                                                    {{ $service_detail->service_name }}
                                                </h2>
                                                {{-- Description --}}
                                                <p class="text-gray-500 text-sm mt-2">
                                                    {{ $service_detail->service_description }}
                                                </p>
                                                <!-- Price & Booking Section -->

                                                <!-- Enquiry Button -->

                                                @if ($enquiry_button != null)
                                                    @if ($whatsAppNumberExists == true && $whatsAppNumberExists == true && $service_detail->enable_enquiry == 'Enabled')
                                                        <a href="https://wa.me/{{ $enquiry_button }}?text={{ __('Hi, I am interested in your product/service:') }} {{ $service_detail->service_name }}. {{ __('Please provide more details.') }}"
                                                            target="_blank"
                                                            class="text-[#121212] text-center font-semibold mt-4 bg-gray-200 hover:bg-gray-300 border border-gray-300 py-2 px-4 rounded-xl">
                                                            {{ __('Enquire') }}
                                                        </a>
                                                    @endif
                                                @endif

                                                <!-- End Price & Booking Section -->
                                            </div>
                                        </swiper-slide>
                                    @endforeach
                                </swiper-container>
                            </div>
                        @endif
                        <!-- End Services Section -->

                        <!-- Start Products Section -->
                        @if (count($product_details) > 0)
                            <div class="relative px-6 lg:px-8">
                                <h2
                                    class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                    <div
                                        class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                    </div>
                                    {{ __('Products') }}
                                </h2>
                                <swiper-container
                                    breakpoints='{
                                        "640": { "slidesPerView": 1 }, 
                                        "1024": { "slidesPerView": 2 } 
                                    }'
                                    space-between="20" class="mySwiper" autoplay-delay="2500"
                                    autoplay-disable-on-interaction="false" loop="true">
                                    {{-- All products --}}
                                    @foreach ($product_details as $product_detail)
                                        <swiper-slide class="p-0.5">
                                            <!-- Product -->
                                            <div
                                                class="flex flex-col p-6 rounded-2xl shadow-[0_0_4px_rgba(0,0,0,0.1)]">
                                                {{-- Badge --}}
                                                @if (!empty($product_detail->badge))
                                                    <p
                                                        class="absolute top-9 right-9 font-medium text-[#F8F9FA] bg-gray-600 px-4 py-1 rounded-lg">
                                                        {{ $product_detail->badge }}
                                                    </p>
                                                @endif
                                                {{-- Image --}}
                                                <img class="w-full h-48 object-cover rounded-2xl mb-2"
                                                    src="{{ url($product_detail->product_image) }}"
                                                    alt="{{ $product_detail->product_name }}" />
                                                {{-- Name --}}
                                                <h2 class="text-black text-md font-semibold">
                                                    {{ $product_detail->product_name }}
                                                </h2>
                                                {{-- Description --}}
                                                <p class="text-gray-500 text-sm mt-2 ">
                                                    {{ $product_detail->product_subtitle }}
                                                </p>

                                                <!-- Price & Booking Section -->
                                                <div class="flex-flex-col mt-2">
                                                    <!-- Price -->
                                                    <div class="flex flex-col mb-4">
                                                        <div>
                                                            <h4 class="text-[#121212] text-md font-bold">
                                                                {{ __('Price:') }}
                                                                <span
                                                                    class="text-gray-500 font-bold text-base ml-1">{{ $product_detail->currency }}
                                                                    {{ formatCurrency($product_detail->sales_price) }}</span>
                                                                {{-- Check regular price is exists --}}
                                                                @if ($product_detail->sales_price != $product_detail->regular_price)
                                                                    <span
                                                                        class="line-through ml-1 text-gray-500 text-sm font-normal">{{ $product_detail->currency }}
                                                                        {{ formatCurrency($product_detail->regular_price) }}</span>
                                                                @endif
                                                            </h4>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-[#121212] text-md font-bold">
                                                                {{ __('Stock:') }}
                                                                <span
                                                                    class="text-{{ $product_detail->product_status == 'instock' ? 'gray-600' : 'red-500' }} font-medium">{{ $product_detail->product_status == 'outstock' ? __('Out of Stock') : __('In Stock') }}</span>
                                                            </h4>
                                                        </div>
                                                    </div>

                                                    <!-- Enquire -->
                                                    @if ($enquiry_button != null)
                                                        @if ($whatsAppNumberExists == true)
                                                            <a href="https://wa.me/{{ $enquiry_button }}?text={{ __('Hi, I am interested in your product:') }} {{ $product_detail->product_name }}. {{ __('Please provide more details.') }}"
                                                                target="_blank"
                                                                class="text-[#121212] font-semibold block text-center py-2 px-4 rounded-xl bg-gray-200 hover:bg-gray-300 border border-gray-300">
                                                                {{ __('Enquire') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                                <!-- End Price & Booking Section -->
                                            </div>
                                        </swiper-slide>
                                    @endforeach
                                </swiper-container>
                            </div>
                        @endif
                        <!-- End Products Section -->

                        <!-- Start Gallery Section with Swiper (Desktop 2 Slides & mobile 1 Slide) -->
                        @if (count($galleries_details) > 0)
                            <div class="relative px-6 lg:px-8">
                                <img src="{{ asset('img/templates/insta-influencer/3.png') }}" alt=""
                                    class="w-24 lg:w-28 absolute top-5 -left-7 rotate-12 opacity-70" />
                                <h2
                                    class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                    <div
                                        class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                    </div>
                                    {{ __('Gallery') }}
                                </h2>
                                <div>
                                    <swiper-container slides-per-view="1" class="mySwiper" loop="true"
                                        autoplay-delay="3000" autoplay-disable-on-interaction="false">
                                        {{-- Slider images --}}
                                        @foreach ($galleries_details as $galleries_detail)
                                            <swiper-slide class="p-0.5">
                                                <!-- Gallery -->
                                                <div
                                                    class="flex flex-col items-center justify-center bg-white p-6 shadow-[0_0_4px_rgba(0,0,0,0.1)] rounded-2xl">
                                                    <img class="w-full h-72 object-cover rounded-2xl"
                                                        src="{{ url($galleries_detail->gallery_image) }}"
                                                        alt="{{ $galleries_detail->caption }}" />
                                                    @if ($galleries_detail->caption)
                                                    <h3
                                                        class="py-3 bg-gray-200 border border-gray-300 mt-6 text-[#121212] text-lg w-full text-center rounded-2xl ">
                                                        {{ $galleries_detail->caption }}</h3>
                                                    @endif
                                                </div>
                                            </swiper-slide>
                                        @endforeach
                                    </swiper-container>
                                </div>
                            </div>
                        @endif
                        <!-- End Gallery Section -->

                        <!-- Start Youtube Video Section -->
                        @if ($feature_details->where('type', 'youtube')->count() > 0)
                            <div class="relative px-6 lg:px-8">
                                <h2
                                    class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                    <div
                                        class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                    </div>
                                    {{ __('Youtube Videos') }}
                                </h2>
                                <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-4 items-center ">
                                    {{-- Videos --}}
                                    @foreach ($feature_details as $feature)
                                        @if ($feature->type == 'youtube')
                                            <!-- Video 1 -->
                                            <div class="overflow-hidden rounded-2xl">
                                                {{-- Add Youtube title --}}
                                                @if ($feature->label != null)
                                                    <div class="px-4 py-4 bg-black">
                                                        <div class="text-white font-medium text-lg">
                                                            {{ $feature->label }}
                                                        </div>
                                                    </div>
                                                @endif
                                                <iframe width="100%" height="270"
                                                    src="https://www.youtube.com/embed/{{ $feature->content }}"
                                                    title="{{ $feature->label }}" frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen></iframe>

                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!-- End Youtube Video Section -->

                        <!-- Start Client Reviews section with Swiper JS -->
                        @if (count($testimonials) > 0)
                            <div class="relative px-6 lg:px-8">
                                <img src="{{ asset('img/templates/insta-influencer/4.png') }}" alt=""
                                    class="w-28 lg:w-32 absolute top-3 -right-4 rotate-12 opacity-70" />
                                <h2
                                    class="text-3xl lg:text-4xl font-medium text-gray-800 py-12 text-center relative head">
                                    <div
                                        class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                    </div>
                                    {{ __('Client Reviews') }}
                                </h2>
                                <swiper-container class="mySwiper" autoplay-delay="2000"
                                    autoplay-disable-on-interaction="false">
                                    {{-- Client Reviews --}}
                                    @foreach ($testimonials as $testimonial)
                                        <!-- Testimonial -->
                                        <swiper-slide class="p-1 rounded-2xl ">
                                            <div
                                                class="p-8 shadow-[0_0_4px_rgba(0,0,0,0.1)] border rounded-2xl bg-white">
                                                <div class="flex items-start justify-start">
                                                    <img src="{{ url($testimonial->reviewer_image) }}"
                                                        alt="{{ $testimonial->reviewer_name }}"
                                                        class="h-12 w-12 rounded-full object-cover" />
                                                    <div class="flex flex-col ml-3">
                                                        <p class="text-gray-800 font-medium text-xl">
                                                            {{ $testimonial->reviewer_name }}
                                                        </p>
                                                        @if ($testimonial->review_subtext)
                                                            <p class="text-gray-700 text-sm font-normal">
                                                                ({{ $testimonial->review_subtext }})
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="text-gray-500 text-lg italic mt-4 text-center">
                                                    "{{ $testimonial->review }}"
                                                </p>
                                            </div>
                                        </swiper-slide>
                                    @endforeach
                                </swiper-container>
                            </div>
                        @endif
                        <!-- End Client Reviews section with Swiper JS -->

                        {{-- Start Business Hours --}}
                        @if ($plan_details['business_hours'] == 1)
                            @if ($business_hours != null && $business_hours->is_display != 0)
                                <section class="px-6 lg:px-8">
                                    <!-- Section Header -->
                                    <h2
                                        class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                        <div
                                            class="absolute bottom-10 left-1/2 h-1 w-14 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                        </div>
                                        {{ __('Business Hours') }}
                                    </h2>

                                    <!-- Business Hours Card -->
                                    <div class="bg-white rounded-lg py-4">
                                        @if ($business_hours->is_always_open != 'Opening')
                                            <!-- Days and Hours List -->
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                                    <div class="flex items-center space-x-4">
                                                        <!-- Day Icon -->
                                                        <div
                                                            class="flex items-center justify-center w-10 h-10 bg-gray-100 border border-gray-200 text-[#121212] rounded-full">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-clock">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path
                                                                    d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" />
                                                                <path d="M16 3v4" />
                                                                <path d="M8 3v4" />
                                                                <path d="M4 11h10" />
                                                                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                                <path d="M18 16.5v1.5l.5 .5" />
                                                            </svg>
                                                        </div>
                                                        <!-- Day and Hours -->
                                                        <div>
                                                            <p class="text-sm font-semibold text-[#121212] capitalize">
                                                                {{ __($day) }}</p>
                                                            <p class="text-base text-gray-500">
                                                                {{ __($business_hours->$day ?: __('Closed')) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <!-- Always Open -->
                                            <div class="flex items-start space-x-4">
                                                <!-- Animated Icon -->
                                                <div
                                                    class="flex items-center justify-center w-12 h-12 bg-gray-200 text-[#121212]] rounded-full transform hover:scale-110 transition-transform duration-300 ease-in-out">
                                                    <svg class="w-6 h-6 animate-pulse" fill="currentColor"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 2a10 10 0 100 20 10 10 0 000-20zM10 16l6-4-6-4v8z" />
                                                    </svg>
                                                </div>
                                                <!-- Text -->
                                                <div>
                                                    <p class="text-xl font-bold text-[#121212]">
                                                        {{ __('Always Open') }}</p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ __('We’re available 24/7 to serve you!') }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </section>
                            @endif
                        @endif
                        {{-- End Business Hours --}}

                        <!-- Start an Application section -->
                        @if ($appointmentEnabled == true && isset($plan_details['appointment']) == 1)
                            <div class="relative px-6 lg:px-8">
                                <img src="{{ asset('img/templates/insta-influencer/5.png') }}" alt=""
                                    class="w-20 lg:w-24 absolute top-3 -left-7 -rotate-12 opacity-70" />
                                {{-- Check appointment slots in the calendar --}}
                                @if ($plan_details['appointment'] == 1)
                                    @if ($appointment_slots != null)
                                        <h2
                                            class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                            <div
                                                class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                            </div>
                                            {{ __('Appointment') }}
                                        </h2>

                                        <div
                                            class="shadow-[0_0_3px_rgba(0,0,0,0.1)] overflow-hidden border p-10 bg-white rounded-2xl">
                                            <!-- Error Message (hidden by default) -->
                                            <div id="errorMessage" class="text-red-500 text-sm my-2 hidden">
                                                {{ __('Please select a valid date and time slot.') }}</div>

                                            {{-- Success Message (hidden by default) --}}
                                            <div id="successMessage" class="text-green-500 text-sm my-2 hidden">
                                                {{ __('Appointment booked successfully!') }}</div>

                                            <!-- Error Message (hidden by default) -->
                                            <div id="errorSubmitMessage" class="text-red-500 text-sm my-2 hidden">
                                                {{ __('Please fill all the fields.') }}</div>

                                            <div class="flex flex-col justify-between mb-5 space-y-3">
                                                <!-- flatpickr Calendar -->
                                                <input type="text" id="appointment-date"
                                                    class="flatpickr-input rounded-2xl w-full px-4 py-3 text-[#121212] bg-white border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                                    placeholder="{{ __('Select a date') }}" required />
                                                <!-- Select time in dropdown -->
                                                <select id="time-slot-select" required
                                                    class="w-full px-4 py-3 rounded-2xl text-[#121212] bg-white border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                                    <option value="">{{ __('Select a time slot') }}
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Booking button -->
                                            <div class="flex justify-center">
                                                <button id="add-slot-button"
                                                    class="w-full p-3 bg-gray-200 hover:bg-gray-300 border border-gray-300 rounded-2xl text-[#121212] text-lg text-center font-semibold focus:outline-none"
                                                    onclick="validateAndShowModal()">
                                                    {{ __('Book Appointment') }}
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                        <!-- End an Application section -->

                        <!-- Start Payment section -->
                        @if (count($payment_details) > 0)
                            <div class="relative px-6 lg:px-8">
                                <h2
                                    class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                    <div
                                        class="absolute bottom-10 left-1/2 h-1 w-14 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                    </div>
                                    {{ __('Payment Options') }}
                                </h2>
                                <div class="grid lg:grid-cols-2 gap-4">
                                    {{-- Payment options --}}
                                    @foreach ($payment_details as $payment)
                                        <!-- {{ $payment->label }} Option -->
                                        <div class="flex flex-col rounded-2xl p-4 bg-gray-100 border border-gray-300">
                                            <div class="flex justify-between items-center">
                                                <!-- {{ $payment->label }} Icon -->
                                                <i class="{{ $payment->icon }} text-[#121212] text-4xl"></i>
                                                <!-- Payment link icon -->
                                                @if ($payment->type == 'url')
                                                    <a href="https://{{ str_replace('https://', '', $payment->content) }}"
                                                        target="_blank" rel="noopener noreferrer">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-external-link text-[#121212] h-8 w-8">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                                            <path d="M11 13l9 -9" />
                                                            <path d="M15 4h5v5" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                {{-- UPI Payment --}}
                                                @if ($payment->type == 'upi')
                                                    <a href="upi://pay?pa={{ $payment->content }}&pn={{ urlencode($payment->label) }}&cu=INR"
                                                        target="_blank" rel="noopener noreferrer">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-external-link text-[#121212] h-8 w-8">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                                            <path d="M11 13l9 -9" />
                                                            <path d="M15 4h5v5" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                            <h3
                                                class="font-medium text-[#121212] {{ $payment->type == 'text' ? 'py-3' : 'pt-3' }}">
                                                {{ $payment->label }}</h3>
                                            <!-- Bank Details (Optional) -->
                                            @if ($payment->type == 'text')
                                                <p class="text-gray-600 text-sm break-word text-base">
                                                    @foreach (explode('.', $payment->content) as $sentence)
                                                        @if (trim($sentence))
                                                            <!-- Make sure the sentence is not empty -->
                                                            {{ trim($sentence) }}
                                                            <br> <!-- Break the line after each sentence -->
                                                        @endif
                                                    @endforeach
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!-- End Payment section -->

                        <!-- Start Contact form section -->
                        @if ($plan_details['contact_form'] == 1)
                            @if ($business_card_details->enquiry_email != null)
                                <div class="relative px-6 lg:px-8">
                                    <img src="{{ asset('img/templates/insta-influencer/6.png') }}" alt=""
                                        class="w-24 lg:w-28 absolute top-3 -right-6 rotate-12 opacity-70" />
                                    <h2
                                        class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                        <div
                                            class="absolute bottom-10 left-1/2 h-1 w-14 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                        </div>
                                        {{ __('Contact Us') }}
                                    </h2>

                                    {{-- Message Alert --}}
                                    @if (Session::has('message'))
                                        <div class="px-6 py-4 bg-gray-100 shadow-md mb-6">
                                            <div class="flex items-start">
                                                <div class="mr-4">
                                                    <svg class="w-6 h-6 text-[#121212]"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path
                                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-[#121212]">
                                                        {{ Session::get('message') }}</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ __('Please wait for the reply to be sent.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <form
                                        class="w-full max-w-full border px-6 pt-9 shadow-[0_0_4px_rgba(0,0,0,0.1)] pb-6 rounded-2xl bg-white relative z-10"
                                        action="{{ route('sent.enquiry') }}" method="POST">
                                        @csrf

                                        <!-- Grid Layout -->
                                        <div class="grid grid-cols-1 gap-2 lg:gap-6 lg:grid-cols-2">
                                            <!-- Left Side Inputs -->
                                            <div class="grid grid-cols-1 gap-2">
                                                <input type="hidden" name="card_id"
                                                    value="{{ $business_card_details->card_id }}" />
                                                {{-- Name --}}
                                                <div>
                                                    <label for="name"
                                                        class="text-gray-800 font-semibold mb-2 block">{{ __('Name') }}</label>
                                                    <input type="text" name="name" placeholder="Your Name"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300" required />
                                                </div>
                                                {{-- Email --}}
                                                <div>
                                                    <label for="email"
                                                        class="text-gray-800 font-semibold mb-2 block">{{ __('Email') }}</label>
                                                    <input type="email" name="email" placeholder="Your Email"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300" required />
                                                </div>
                                                {{-- Mobile Number --}}
                                                <div>
                                                    <label for="phone"
                                                        class="text-gray-800 font-semibold mb-2 block">{{ __('Mobile Number') }}</label>
                                                    <input type="tel" name="phone"
                                                        placeholder="{{ __('Your Mobile Number') }}"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300" required />
                                                </div>
                                            </div>

                                            <!-- Right Side Textarea -->
                                            <div class="h-full pb-8">
                                                <label for="message"
                                                    class="text-gray-800 font-semibold mb-2 block">{{ __('Message') }}</label>
                                                <textarea name="message" placeholder="{{ __('Your Message') }}"
                                                    class="w-full h-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300 resize-none"
                                                    style="min-height: 10rem" required></textarea>
                                            </div>
                                                    
                                            {{-- ReCaptcha --}}
                                            @include('templates.includes.recaptcha')

                                        </div>

                                        <!-- Submit Button -->
                                        <div class="mt-6">
                                            <button type="submit"
                                                class="w-full px-4 py-3 bg-gray-200 hover:bg-gray-300 border border-gray-300 text-[#121212] text-lg font-semibold rounded-xl focus:outline-none">
                                                {{ __('Send') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endif

                        <!-- Start Location section -->
                        @if (count($feature_details) > 0 && $feature_details->contains('type', 'map'))
                            <div class="relative px-6 lg:px-8" id="location">
                                <h2
                                    class="text-3xl lg:text-4xl font-medium text-[#121212] py-12 text-center relative head">
                                    <div
                                        class="absolute bottom-10 left-1/2 h-1 w-16 bg-gradient-to-r from-[#f58529] via-[#dd2a7b] to-[#833ab4] -translate-x-1/2">
                                    </div>
                                    {{ __('Location') }}
                                </h2>
                                {{-- Google Maps --}}
                                @foreach ($feature_details as $feature)
                                    @if ($feature->type == 'map')
                                        <iframe src="https://www.google.com/maps/embed?{!! $feature->content !!}"
                                            width="100%" height="300" style="border: 0" allowfullscreen=""
                                            loading="lazy" class="rounded-t-2xl"></iframe>
                                        {{-- Map title --}}
                                        @if ($feature->label != null)
                                            <div class="px-5 py-3 bg-gray-200 rounded-b-2xl">
                                                <div class="text-[#121212] font-semibold text-lg">
                                                    {{ $feature->label }}
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <!-- End Location section -->

                        <!-- Branding Section -->
                        @if ($plan_details['hide_branding'] == 1)
                            <div class="pb-1 px-6 lg:px-8">
                                <div
                                    class="flex pt-5 px-3 m-auto font-semibold text-white text-sm flex-col md:flex-row max-w-6xl">
                                    <div class="mt-2 text-gray-500">
                                        {{ __('Copyright') }} &copy;
                                        <a class="text-gray-700" href="{{ url($card_details->card_url) }}">
                                            {{ $card_details->title }} </a>
                                        <span id="year"></span>{{ __('. All Rights Reserved.') }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="pb-1 px-6 lg:px-8">
                                <div
                                    class="flexpx-3 m-auto pt-5 font-semibold text-white text-sm flex-col md:flex-row max-w-6xl">
                                    <div class="mt-2 text-gray-500">
                                        {{ __('Made with') }}
                                        <a class="text-gray-700" href="{{ env('APP_URL') }}">
                                            {{ config('app.name') }} </a>
                                        <span id="year"></span>{{ __('. All Rights Reserved.') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!-- Branding Section -->
                    </div>
                </div>
            @endif

            <!-- Start Floating icon button bar section -->
            <div
                class="fixed w-full mx-auto left-1/2 box-content bottom-0 px-2 bg-white border-t border-t-gray-300 py-6 flex lg:hidden md:hidden transform -translate-x-1/2 z-50">
                <!-- Profile Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <a href="#profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-user text-[#121212] h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                    </a>
                </div>

                <!-- Send Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <button onclick="toggleWhatsAppModal(true)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-send text-[#121212] h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 14l11 -11" />
                            <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                        </svg>
                    </button>
                </div>

                <!-- Download Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <a href="{{ route('download.vCard', $business_card_details->card_id) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-download text-[#121212] h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 11l5 5l5 -5" />
                            <path d="M12 4l0 12" />
                        </svg>
                    </a>
                </div>

                <!-- Scan Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <button onclick="toggleScanModal(true)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-line-scan text-[#121212] h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 8v-2a2 2 0 0 1 2 -2h2" />
                            <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                            <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                            <path d="M16 20h2a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 12h10" />
                        </svg>
                    </button>
                </div>

                <!-- Share Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <button onclick="shareToggleModal(true)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-share text-[#121212] h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M6 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M18 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M8.7 10.7l6.6 -3.4" />
                            <path d="M8.7 13.3l6.6 3.4" />
                        </svg>
                    </button>
                </div>
            </div>
            <!-- End Floating icon button bar section -->
        @endif
        {{-- End Check password protected --}}

        <!-- Start Apointment Modal (By default hidden) -->
        <div id="appointmentModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <!-- Modal Content -->
            <div class="bg-white rounded-2xl w-full max-w-md p-6 mx-4 shadow-lg">
                <!-- Modal Header -->
                <div class="flex justify-center items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ __('Book Appointment') }}</h2>
                </div>

                <!-- Appointment Form -->
                <form id="appointmentForm">
                    <!-- Name Field -->
                    <div class="mb-4">
                        <label for="name"
                            class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                        <input type="text" id="name"
                            class="mt-1 p-2 border border-gray-300 focus:ring-2 focus:ring-gray-300 rounded-lg w-full"
                            required>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email"
                            class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <input type="email" id="email"
                            class="mt-1 p-2 border border-gray-300 focus:ring-2 focus:ring-gray-300 rounded-lg w-full"
                            required>
                    </div>

                    <!-- Phone Field -->
                    <div class="mb-4">
                        <label for="phone"
                            class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
                        <input type="text" id="phone"
                            class="mt-1 p-2 border border-gray-300 focus:ring-2 focus:ring-gray-300 rounded-lg w-full"
                            required>
                    </div>

                    <!-- Notes Field -->
                    <div class="mb-4">
                        <label for="notes"
                            class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                        <textarea id="notes" class="mt-1 p-2 border border-gray-300 focus:ring-2 focus:ring-gray-300 rounded-lg w-full"
                            rows="3"></textarea>
                    </div>

                    <!-- Hidden Price Field -->
                    <div class="mb-4 hidden">
                        <label for="price"
                            class="block text-sm font-medium text-gray-700">{{ __('Price') }}</label>
                        <input type="text" id="price"
                            class="mt-1 p-2 border border-gray-300 focus:ring-2 focus:ring-gray-300 rounded-lg w-full"
                            disabled>
                    </div>

                    {{-- ReCaptcha --}}
                    @include('templates.includes.recaptcha')

                    <!-- Submit and Close Buttons -->
                    <div class="flex justify-between">
                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg"
                            onclick="validateAndShowModal()">
                            {{ __('Close') }}
                        </button>
                        <button type="submit" id="bookAppointmentButton"
                            class="bg-[#121212] text-white px-4 py-2 rounded-lg">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- End Appointment Modal --}}

        <!-- Start Share Modal -->
        <div id="shareModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50"
            onclick="shareToggleModal(false)">
            <!-- Modal content -->
            <div class="bg-white rounded-2xl w-full max-w-md p-6 mx-4 space-y-6" onclick="event.stopPropagation()">
                <!-- Modal header -->
                <div class="flex justify-center items-center">
                    <h2 class="text-2xl text-center font-bold">{{ __('Share on') }}</h2>
                </div>

                <!-- QR Code Section -->
                <div class="flex justify-center">
                    <canvas id="shareQrCode"></canvas>
                </div>

                <!-- Share via Social Media -->
                <div class="flex justify-around text-[#121212]">
                    <a href="{{ $shareComponent['facebook'] }}" target="_blank">
                        <i class="fab fa-facebook fa-2x"></i>
                    </a>
                    <a href="{{ $shareComponent['twitter'] }}" target="_blank">
                        <i class="fab fa-twitter fa-2x"></i>
                    </a>
                    <a href="{{ $shareComponent['linkedin'] }}" target="_blank">
                        <i class="fab fa-linkedin fa-2x"></i>
                    </a>
                    <a href="{{ $shareComponent['whatsapp'] }}" target="_blank">
                        <i class="fab fa-whatsapp fa-2x"></i>
                    </a>
                    <a href="{{ $shareComponent['telegram'] }}" target="_blank">
                        <i class="fab fa-telegram fa-2x"></i>
                    </a>
                </div>

                <!-- Copy Link Section -->
                <div class="flex justify-center">
                    <button onclick="copyLink()"
                        class="bg-gray-200 border border-gray-300 text-[#121212] font-bold py-2 px-4 rounded-xl w-full">
                        {{ __('Copy Link') }}
                    </button>
                </div>
            </div>
        </div>
        {{-- End Share Modal --}}

        <!-- Start WhatsApp Modal -->
        <div id="whatsappModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center hidden z-50"
            onclick="toggleWhatsAppModal(false)">
            <!-- Modal content (stops propagation to prevent closing when clicking inside) -->
            <div class="rounded-2xl w-full max-w-md p-6 mx-4 space-y-6 bg-white" onclick="event.stopPropagation()">
                <!-- Input for WhatsApp number -->
                <div>
                    <label for="whatsappNumber"
                        class="block text-gray-700 font-medium">{{ __('Enter WhatsApp Number') }}:</label>
                    <input type="text" id="whatsappNumber" placeholder="e.g., +919876543210"
                        class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-gray-300" />
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button onclick="sendMessage()"
                        class="bg-gray-200 border border-gray-300 hover:bg-gray-300 text-[#121212] font-bold py-2 px-4 rounded-xl w-full focus:outline-none">
                        {{ __('Send') }}
                    </button>
                </div>
            </div>
        </div>
        <!-- End Whatsapp Modal -->

        <!-- Start Scan QR Code Modal -->
        <div id="scanModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50 qr-modal"
            onclick="toggleScanModal(false)">
            <!-- Modal content (stops propagation to prevent closing when clicking inside) -->
            <div class="rounded-2xl w-full max-w-md p-6 mx-4 space-y-6 bg-white qr-modal-overlay"
                onclick="event.stopPropagation()">
                <!-- Qr Code -->
                <div class="flex justify-center flex-col items-center">
                    <div class="qr-code mb-2"></div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button id="download"
                        onclick="downloadQr('{{ route('dynamic.card', $business_card_details->card_id) }}', 500)"
                        class="bg-gray-200 border border-gray-300 font-bold p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-download text-[#121212] h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 11l5 5l5 -5" />
                            <path d="M12 4l0 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- End Scan QR Code Modal -->

        {{-- Start Check password protected Modal --}}
        @if ($business_card_details->password != null && Session::get('password_protected') == false)
            <div class="p-4 flex items-center justify-center">
                <div x-data="{ showModal: true }">
                    <!-- Modal -->
                    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50 p-3">
                        <div class="bg-white p-6 w-96 max-w-full shadow-lg transform transition-all duration-300 rounded-2xl"
                            x-show.transition.opacity="showModal">
                            <!-- Modal Header -->
                            <div class="flex justify-between items-center border-b-2 border-gray-200 pb-4">
                                <h2 class="text-2xl font-semibold">{{ __('Password Protected') }}</h2>
                            </div>

                            <!-- Modal Content -->
                            <div class="mt-6 space-y-4">
                                <form action="{{ route('check.pwd', $business_card_details->card_id) }}"
                                    method="post">
                                    @csrf
                                    <p class="text-lg text-gray-900">{{ __('Enter your vcard Password') }}</p>
                                    <div class="flex">
                                        <input type="password" name="password"
                                            class=" bg-gray-100 text-[#121212] block flex-1 min-w-0 w-full text-sm p-2.5 rounded-2xl"
                                            placeholder="{{ __('Password') }}" required autofocus>
                                    </div>

                                    {{-- Message --}}
                                    @if (Session::has('message'))
                                        <div class="flex items-center p-4 my-4 text-sm bg-gray-100" role="alert">
                                            <svg class="flex-shrink-0 inline w-4 h-4 mr-3 text-red-500"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                            </svg>
                                            <span class="sr-only">{{ __('Failed') }}</span>
                                            <div>
                                                <span
                                                    class="font-medium text-red-500">{{ Session::get('message') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex flex-col space-y-4 mt-3">
                                        <button type="submit"
                                            class="bg-black text-white rounded-2xl px-4 py-2 mt-2 transition duration-300">{{ __('Password') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- End Check password protected Modal --}}

        <!-- Include PWA modal -->
        @if ($plan_details != null)
            {{-- Check PWA --}}
            @if ($plan_details['pwa'] == 1)
                @include('vendor.laravelpwa.new_pwa_modal', [
                    'primary_color' => 'black',
                    'img' => $business_card_details->profile,
                ])
            @endif
        @endif

        {{-- Include Newsletter Modal --}}
        @if ($business_card_details != null)
            {{-- Check Newsletter --}}
            @if ($business_card_details->is_newsletter_pop_active == 1)
                @include('templates.includes.newsletter_modal', [
                    'primary_color' => 'black',
                ])
            @endif
        @endif

        {{-- Include Information Popup Modal --}}
        @if ($business_card_details != null)
            {{-- Check Information Popup --}}
            @if ($business_card_details->is_info_pop_active == 1)
                @include('templates.includes.information_popup_modal', [
                    'primary_color' => 'black',
                ])
            @endif
        @endif
    </div>

    {{-- Jquery --}}
    <script src="{{ url('js/jquery.min.js') }}"></script>
    {{-- Smooth Scroll --}}
    <script src="{{ url('js/smooth-scroll.polyfills.min.js') }}"></script>
    {{-- Other JS --}}
    <script type="text/javascript" src="{{ url('app/js/footer.js') }}"></script>
    {{-- Flatpickr JS --}}
    <script src="{{ url('js/flatpickr.min.js') }}"></script>
    {{-- Swiper JS --}}
    <script src="{{ url('js/swiper-element-bundle.min.js') }}"></script>
    {{-- Custom JS --}}
    @yield('custom-js')
    {{-- Check business details --}}
    @if ($business_card_details != null)
        <script>
            {!! $business_card_details->custom_js !!}
        </script>
    @endif
    {{-- Flatpickr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/{{ app()->getLocale() }}.js"></script>
    <script>
        // Assuming $appointment_slots contains data like: {"monday": [...], "tuesday": [...], ...}
        const disableSlots = {!! $appointment_slots !!}; // Outputting the time slots

        document.addEventListener('DOMContentLoaded', function() {
            "use strict";

            flatpickr("#appointment-date", {
                dateFormat: "Y-m-d",
                minDate: "today",
                locale: "{{ app()->getLocale() }}",
                disable: [
                    function(date) {
                        const day = date.toLocaleString("en-us", {
                            weekday: 'long'
                        }).toLowerCase();
                        return !disableSlots[day] || disableSlots[day].length === 0;
                    }
                ],
                onChange: function(selectedDates) {
                    const selectedDate = selectedDates[0];
                    const day = selectedDate.toLocaleString("en-us", {
                        weekday: 'long'
                    }).toLowerCase();
                    // Get available time slots in Send data to Laravel route using fetch API
                    generateOption(selectedDate, day);
                }
            });
        });
    </script>
    <script>
        // Toggle the modal visibility
        function toggleModal() {
            "use strict";

            const modal = document.getElementById('appointmentModal');
            modal.classList.toggle('hidden');
        }

        // Validate appointment date and time slot
        function validateAndShowModal() {
            "use strict";

            const appointmentDate = document.getElementById('appointment-date').value;
            const timeSlotSelect = document.getElementById('time-slot-select').value;
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');

            if (appointmentDate && timeSlotSelect) {
                // If both fields are not empty, show the modal
                toggleModal();
                errorMessage.classList.add('hidden'); // Hide any previous error message
            } else {
                // If either field is empty, show an error message
                errorMessage.classList.remove('hidden');
            }
        }

        // Handle form submission
        document.getElementById('appointmentForm').addEventListener('submit', function(event) {
            "use strict";

            event.preventDefault();

            // Get the button element
            const button = document.getElementById('bookAppointmentButton');

            // Disable the button and show loader
            button.disabled = true;
            button.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-loader animate-spin h-5 w-5 text-white inline-block mr-2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 6l0 -3" />
                    <path d="M16.25 7.75l2.15 -2.15" />
                    <path d="M18 12l3 0" />
                    <path d="M16.25 16.25l2.15 2.15" />
                    <path d="M12 18l0 3" />
                    <path d="M7.75 16.25l-2.15 2.15" />
                    <path d="M6 12l-3 0" />
                    <path d="M7.75 7.75l-2.15 -2.15" />
                </svg>
                {{ __('Booking...') }}
            `;

            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');

            // Gather form data
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                notes: document.getElementById('notes').value,
                date: document.getElementById('appointment-date').value,
                time_slot: document.getElementById('time-slot-select').value,
                price: document.getElementById('price').value,
                card: `{{ $business_card_details->card_id }}`
            };

            // Send data to Laravel route using fetch API
            fetch("{{ route('book.appointment') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    body: JSON.stringify(formData)
                })
                .then(data => {
                    // Handle success or error response from the server
                    if (data.status == 200) {
                        // Reset the form fields
                        document.getElementById('email').value = "";
                        document.getElementById('phone').value = "";
                        document.getElementById('name').value = "";
                        document.getElementById('notes').value = "";
                        document.getElementById('price').value = "";

                        // Get available time slots in Send data to Laravel route using fetch API
                        generateOption("", "");

                        successMessage.classList.remove('hidden'); // Hide any previous success message
                        toggleModal(); // Close the modal on success

                        // Re-enable the button and revert its content
                        button.disabled = false;
                        button.innerHTML = "{{ __('Book Appointment') }}";

                    } else {
                        // If either field is empty, show an success message
                        errorSubmitMessage.classList.remove('hidden');
                        toggleModal(); // Close the modal on error

                        // Re-enable the button and revert its content
                        button.disabled = false;
                        button.innerHTML = "{{ __('Book Appointment') }}";
                    }
                });
        });
    </script>
    <script>
        function generateOption(selectedDate, day) {
            "use strict";

            fetch('/get-available-time-slots', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        card: `{{ $business_card_details->card_id }}`,
                        choose_date: selectedDate,
                        day: day
                    })
                }).then(response => response.json())
                .then(data => {
                    // Check response
                    if (data.success == true) {
                        // Set available time slots in select option
                        document.getElementById('time-slot-select').innerHTML =
                            `<option value="">{{ __('Select a time slot') }}`;
                        // Available time slots in JSON.parse(data.available_time_slots)
                        var available_time_slots = JSON.parse(data.available_time_slots);

                        available_time_slots.forEach(time_slot => {
                            document.getElementById('time-slot-select').innerHTML +=
                                `<option value="${time_slot}">${time_slot}</option>`;
                        });

                        // Set price
                        const priceElement = document.getElementById('price');
                        priceElement.value = data.price;
                    }
                });
        }
    </script>
    <script>
        // Generate QR Code and place in shareQrCode using qrious
        const qr = new QRious({
            element: document.getElementById('shareQrCode'),
            value: `{{ route('dynamic.card', $business_card_details->card_id) }}`, // Laravel route
            size: 200,
            background: 'white', // Background color
            foreground: 'black', // Foreground (QR code) color
            level: 'H' // Error correction level
        });

        // AOS
        AOS.init({
            duration: 1000,
            once: true,
        });

        // Share Modal
        function shareToggleModal(show) {
            "use strict";

            document
                .getElementById("shareModal")
                .classList.toggle("hidden", !show);
        }

        // Function to toggle WhatsApp modal visibility
        function toggleScanModal(show) {
            "use strict";

            document
                .getElementById("scanModal")
                .classList.toggle("hidden", !show);
        }

        // Generate QR Code
        window.onload = function() {
            "use strict";

            updateQr(`{{ route('dynamic.card', $business_card_details->card_id) }}`);
        };

        // Copy Link
        function copyLink() {
            "use strict";

            // From browser url to clipboard
            navigator.clipboard.writeText(`{{ route('dynamic.card', $business_card_details->card_id) }}`);
            alert("Link copied to clipboard!");
        }

        // Function to toggle WhatsApp modal visibility
        function toggleWhatsAppModal(show) {
            "use strict";

            document
                .getElementById("whatsappModal")
                .classList.toggle("hidden", !show);
        }

        // Function to send WhatsApp message
        function sendMessage() {
            "use strict";

            const phoneNumber = document
                .getElementById("whatsappNumber")
                .value.trim();
            const whatsappModal = document.getElementById("whatsappModal");

            if (phoneNumber) {
                const message = `{{ $shareContent }}`;
                const whatsappUrl = `https://wa.me/${phoneNumber}?text=${message}`;
                // Open the URL in a new tab
                window.open(whatsappUrl, "_blank");
                whatsappModal.classList.add("hidden"); // Close the modal
                // Reset the input field
                document.getElementById("whatsappNumber").value = "";
            } else {
                alert(`{{ __('Please enter a valid WhatsApp number.') }}`);
            }
        }
    </script>
    <script>
        // Initialize smooth scroll
        const scroll = new SmoothScroll('a[href*="#"]', {
            speed: 300, // Duration of scroll in milliseconds
            offset: 50, // Offset in pixels from the top
            easing: "easeInOutCubic", // Scroll easing function
        });
    </script>
</body>

</html>
