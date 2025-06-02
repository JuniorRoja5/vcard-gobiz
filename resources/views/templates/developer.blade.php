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

    <meta name="theme-color" content="#FFF7ED" />

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="application-name" content="{{ $card_details->title }}">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-title" content="{{ $card_details->title }}">

    <!-- Tile for Win8 -->
    <meta name="msapplication-TileColor" content="#FFF7ED">
    <meta name="msapplication-TileImage" content="{{ url($business_card_details->profile) }}">

    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! JsonLd::generate() !!}

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS --}}
    <link rel="stylesheet" href="{{ url('templates/css/developer.css') }}">

    {{-- Fontawesome --}}
    <link rel="stylesheet" href="{{ url('css/fontawesome.min.css') }}">

    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="{{ url('css/swiper-bundle.min.css') }}">

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="{{ url('css/flatpickr.min.css') }}">

    {{-- QRious --}}
    <script src="{{ url('js/qrious.min.js') }}"></script>

    {{-- Custom CSS --}}
    <style>
        body {
            font-family: "Outfit", sans-serif;
        }
        @media (prefers-color-scheme: dark) {
            @media (min-width: 768px) {
                .m-5 {
                    margin: 2rem;
                }
            }
        }
        @media (prefers-color-scheme: dark) {
            @media (min-width: 768px) {
                .md-top-6 {
                    top: 2.8rem;
                }
            }
        }
    </style>

    {{-- Check business details --}}
    @if ($business_card_details != null)
        @php
            $custom_css = $business_card_details->custom_css;
            $custom_js = $business_card_details->custom_js;

            // Ensure <style> tags for custom CSS
            if (strpos($custom_css, '<style>') === false && strpos($custom_css, '</style>') === false) {
                $custom_css = "<style>" . $custom_css . "</style>";
            }

            // Ensure <script> tags for custom JS
            if (strpos($custom_js, '<script>') === false && strpos($custom_js, '</script>') === false) {
                $custom_js = "<script>" . $custom_js . "</script>";
            }
        @endphp

        {!! $custom_css !!}
        {!! $custom_js !!}
    @endif

    {{-- Check PWA --}}
    @if ($plan_details != null)
        @if ($plan_details['pwa'] == 1)
            @laravelPWA

            <!-- Web Application Manifest -->
            <link rel="manifest" href="{{ $manifest }}">
        @endif
    @endif

    @php
        use Illuminate\Support\Facades\Session;
    @endphp
</head>

<body class="bg-orange-50 min-h-screen"
    dir="{{ App::isLocale('ar') || App::isLocale('ur') || App::isLocale('he') ? 'rtl' : 'ltr' }}">

    <div class="container max-w-2xl mx-auto lg:my-10 pb-10 lg:pb-0">
        {{-- Start Check password protected --}}
        @if ($business_card_details->password == null || Session::get('password_protected') == true)
            {{-- Check business details --}}
            @if ($business_card_details != null)
                <div class="bg-white lg:rounded-3xl shadow-[0_0_4px_rgba(0,0,0,0.1)] overflow-hidden relative">
                    <!-- Start Cover Image Section -->
                    @if ($business_card_details->cover_type == 'none')
                        <div class="h-72 lg:h-96 relative p-3 lg:p-6" id="profile">
                            <img src="{{ url('img/templates/developer/banner.png') }}" alt="{{ $business_card_details->title }}" class="w-full h-full object-cover rounded-3xl" />
                        </div>
                    @endif
                    <!-- End Cover Image Section -->

                    <!-- Start Cover Image Section -->
                    @if ($business_card_details->cover_type == 'photo')
                        <div class="h-72 lg:h-96 relative p-3 lg:p-6" id="profile">
                            <img src="{{ $business_card_details->cover ? url($business_card_details->cover) : asset('images/default-cover.png') }}" alt="{{ $business_card_details->title }}" class="w-full h-full object-cover rounded-3xl" />
                        </div>
                    @endif
                    <!-- End Cover Image Section -->

                    <!-- Start Cover Video Section (Vimeo AP) -->
                    @if ($business_card_details->cover_type == 'vimeo-ap')
                        <div class="relative w-full p-3 lg:p-6" style="padding-top: 56.25%;" id="profile">
                            <iframe
                                src="https://player.vimeo.com/video/{{ $business_card_details->cover }}?autoplay=1&loop=1&autopause=0&muted=1&controls=0"
                                id="vid-player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="absolute top-0 left-0 w-full h-full">
                            </iframe>
                        </div>
                    @endif
                    <!-- End Cover Video Section (Vimeo AP) -->

                    <!-- Start Cover Video Section (Vimeo) -->
                    @if ($business_card_details->cover_type == 'vimeo')
                        <div class="relative w-full p-3 lg:p-6" style="padding-top: 56.25%;" id="profile">
                            <iframe
                                src="https://player.vimeo.com/video/{{ $business_card_details->cover }}?autoplay=0&loop=1&autopause=0&muted=0&controls=1"
                                id="vid-player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="absolute top-0 left-0 w-full h-full">
                            </iframe>
                        </div>
                    @endif
                    <!-- End Cover Video Section (Vimeo) -->

                    <!-- Start Cover Video Section (Youtube AP) -->
                    @if ($business_card_details->cover_type == 'youtube-ap')
                        <div class="relative w-full p-3 lg:p-6" style="padding-top: 56.25%;" id="profile">
                            <iframe
                                src="https://www.youtube.com/embed/{{ $business_card_details->cover }}?autoplay=1&mute=1&controls=0&loop=1&playlist={{ $business_card_details->cover }}"
                                id="vid-player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="absolute top-0 left-0 w-full h-full">
                            </iframe>
                        </div>
                    @endif
                    <!-- End Cover Video Section (Youtube AP) -->

                    <!-- Start Cover Video Section -->
                    @if ($business_card_details->cover_type == 'youtube')
                        <div class="relative w-full p-3 lg:p-6" style="padding-top: 56.25%;" id="profile">
                            <iframe
                                src="https://www.youtube.com/embed/{{ $business_card_details->cover }}?autoplay=0&mute=1&controls=0&loop=1&playlist={{ $business_card_details->cover }}"
                                id="vid-player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="absolute top-0 left-0 w-full h-full">
                            </iframe>
                        </div>
                    @endif
                    <!-- End Cover Video Section -->

                    {{-- Language Switcher --}}
                    @include('templates.includes.language-switcher')

                    <!-- Profile Info -->
                    <div class="px-3 lg:px-6 pb-12 lg:pb-6">
                        <!-- Quick Contact Background -->
                        <div class="relative rounded-3xl">
                            {{-- Background Image --}}
                            <img src="{{ url('img/templates/developer/bg.png') }}" alt="{{ $business_card_details->title }}" class="w-full object-cover absolute top-0 left-0 z-10 rounded-t-3xl" />
                        </div>
                        <!-- End Quick Contact Background -->

                        <!-- Quick Contact -->
                        <div class="relative flex flex-col justify-center items-center mx-16">
                            {{-- Profile Image --}}
                            <img src="{{ url($business_card_details->profile) }}" alt="{{ $business_card_details->title }}" class="h-36 w-36 rounded-full object-cover z-20 m-10" />
                            <div class="flex flex-col justify-center items-center z-20 -mt-3">
                                {{-- Name --}}
                                <h1 class="text-4xl font-medium text-center">{{ $business_card_details->title }}</h1>
                                {{-- Position --}}
                                <p class="text-orange-400 font-bold text-md mt-2">{{ $card_details->sub_title }}</p>
                                {{-- About --}}
                                @if ($business_card_details->description != null)
                                    <div class="mt-3 text-sm leading-relaxed font-medium text-center">{!! $business_card_details->description !!}</div>
                                @endif
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Quick Contact -->
                        @if (count($feature_details) > 0)
                            <div class="flex gap-4 justify-center items-center mt-6">
                                {{-- Loop through the feature_details array and display the icons --}}
                                @foreach ($feature_details as $feature)
                                    @if (in_array($feature->type, ['tel', 'email', 'instagram']))
                                        {{-- Phone --}}
                                        @if ($feature->type == 'tel')
                                            <a href="tel:{{ $feature->content }}"
                                                class="bg-orange-50 p-4 rounded-full text-orange-400 hover:bg-orange-50 transition-colors border border-orange-200 z-10">
                                                <i class="{{ $feature->icon }} fa-xl"></i>
                                            </a>
                                        @endif

                                        {{-- Email --}}
                                        @if ($feature->type == 'email')
                                            <a href="mailto:{{ $feature->content }}"
                                                class="bg-orange-50 p-4 rounded-full text-orange-400 hover:bg-orange-50 transition-colors border border-orange-200 z-10">
                                                <i class="{{ $feature->icon }} fa-xl"></i>
                                            </a>
                                        @endif

                                        {{-- Instagram --}}
                                        @if ($feature->type == 'instagram')
                                            <a href="{{ $feature->content }}" target="_blank"
                                                class="bg-orange-50 p-4 rounded-full text-orange-400 hover:bg-orange-50 transition-colors border border-orange-200 z-10">
                                                <i class="{{ $feature->icon }} fa-xl"></i>
                                            </a>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <!-- Start Section location -->
                        @if (count($feature_details) > 0)
                            @foreach ($feature_details as $feature)
                                @if (in_array($feature->type, ['address']))
                                    <div class="flex justify-center relative">
                                        <img src="{{ url('img/templates/developer/2.png') }}" alt="" class="w-24 absolute -top-2 -right-10 z-20" />
                                        <div class="mt-8 bg-orange-50 hover:bg-orange-50 transition-colors rounded-2xl border border-orange-200 w-full bg-cover bg-center relative">
                                            {{-- Background image --}}
                                            <img src="{{ url('img/templates/developer/icon.png') }}" alt="" class="w-8 absolute top-4 left-4" />

                                            <!-- Font Awesome Icon -->
                                            <a href="https://www.google.com/maps/place/{{ urlencode($feature->content) }}" target="_blank" class="px-6 py-8 text-white flex flex-col">
                                                {{-- Icon --}}
                                                <i class="{{ $feature->icon }} fa-xl text-orange-400 text-2xl py-4"></i>
                                                <!-- Title -->
                                                <h2 class="text-base font-semibold mt-1.5 text-gray-800">
                                                    {{ $feature->label }}
                                                </h2>
                                                <!-- Description -->
                                                <p class="text-md flex items-center mt-2 text-gray-800">
                                                    {{ $feature->content }}
                                                </p>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        <!-- End Section location -->

                        <!-- Start Social links section -->
                        @if (!empty($feature_details) && count($feature_details) > 0)
                            @php
                                // List of excluded feature types
                                $excludedTypes = ['email', 'tel', 'instagram', 'address', 'map', 'iframe', 'youtube'];

                                // Filter the features to include only valid ones
                                $validFeatures = collect($feature_details)->filter(function ($feature) use ($excludedTypes) {
                                    return isset($feature->type) && !in_array($feature->type, $excludedTypes);
                                });
                            @endphp

                            @if ($validFeatures->isNotEmpty())
                                <div class="relative">
                                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                        {{ __('Social Links') }}
                                    </h2>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach ($validFeatures as $feature)
                                            {{-- Generate href value dynamically --}}
                                            @php
                                                $href = $feature->content;
                                                if ($feature->type == 'wa') {
                                                    $href = 'https://wa.me/' . $feature->content;
                                                } elseif ($feature->type == 'text') {
                                                    $href = 'javascript:void(0);';
                                                }
                                            @endphp
                                            <!-- {{ $feature->label }} -->
                                            <a href="{{ $href }}" target="_blank" class="px-4 pt-8 pb-4 transition-colors rounded-2xl flex flex-col border border-orange-200 bg-orange-50 relative">
                                                {{-- Background image --}}
                                                <img src="{{ url('img/templates/developer/icon.png') }}" alt="" class="w-8 absolute top-4 left-3" />
                                                <!-- Font Awesome Icon -->
                                                <div class="flex items-center justify-center border border-orange-200 bg-orange-100 w-9 h-9 rounded-full -ml-1">
                                                    <i class="{{ $feature->icon }} text-orange-400 text-xl"></i>
                                                </div>
                                                <!-- Title -->
                                                <h2 class="text-md font-semibold mt-1">{{ $feature->label }}</h2>
                                                <!-- Description -->
                                                <p class="text-sm truncate">{{ $feature->content }}</p>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                        <!-- End social links section -->

                        <!-- Start Services Section -->
                        @if (count($service_details) > 0)
                            <div class="relative">
                                <img src="{{ url('img/templates/developer/3.png') }}" alt="" class="w-24 opacity-50 absolute top-4 ltr:-right-12 rtl:-left-14 z-20" />
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                    {{ __('Services') }}
                                </h2>
                                <div class="">
                                    <swiper-container slides-per-view="1" class="mySwiper" autoplay-delay="3000"
                                        autoplay-disable-on-interaction="false" loop="true">
                                        {{-- All services --}}
                                        @foreach ($service_details as $service_detail)
                                            <swiper-slide class="p-1">
                                                <div class="flex flex-col lg:flex-row items-stretch lg:justify-between rounded-2xl px-4 pt-9 pb-5 border border-orange-200 bg-orange-50 relative shadow-[0_0_4px_rgba(0,0,0,0.1)]">
                                                    {{-- Background Image --}}
                                                    <img src="{{ url('img/templates/developer/icon.png') }}" alt="" class="w-8 absolute top-4 left-4" />
                                                    {{-- Service Image --}}
                                                    <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rtl:ml-4 rounded-2xl flex-1" style="background-image: url('{{ url($service_detail->service_image) }}')"></div>
                                                    
                                                    <div class="flex flex-col gap-1 flex-[2_2_0px] lg:ml-4 mt-4 lg:mt-0">
                                                        {{-- Service Name --}}
                                                        <p class="text-base font-bold leading-tight">
                                                            {{ $service_detail->service_name }}
                                                        </p>
                                                        {{-- Service Description --}}
                                                        <p class="text-gray-500 text-sm font-normal leading-normal mt-1">
                                                            {{ $service_detail->service_description }}
                                                        </p>
                                                        <!-- Enquiry Button -->
                                                        @if ($enquiry_button != null)
                                                            @if ($whatsAppNumberExists == true && $whatsAppNumberExists == true && $service_detail->enable_enquiry == 'Enabled')
                                                                {{-- Enquire --}}
                                                                <div class="flex items-start justify-start w-full">
                                                                    <div class="flex items-start justify-start w-12 h-12 bg-orange-50 text-orange-600 rounded-full">
                                                                        <a href="https://wa.me/{{ $enquiry_button }}?text={{ __('Hi, I am interested in your product/service:') }} {{ $service_detail->service_name }}. {{ __('Please provide more details.') }}" target="_blank" class="text-base font-medium mt-1 px-4 py-2 bg-orange-400 text-white rounded-lg hover:bg-orange-600 inline-block">{{ __('Enquire') }}</a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </swiper-slide>
                                        @endforeach
                                    </swiper-container>
                                </div>
                            </div>
                        @endif
                        <!-- End Services Section -->

                        <!-- Start Products Section -->
                        @if (count($product_details) > 0)
                            <div class="relative">
                                
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                    {{ __('Products') }}
                                </h2>
                                <swiper-container class="mySwiper" autoplay-delay="2500" autoplay-disable-on-interaction="false">
                                    {{-- All products --}}
                                    @foreach ($product_details as $product_detail)
                                        <swiper-slide class="p-1">
                                            <!-- Product -->
                                            <div class="flex flex-col justify-center bg-white p-6 rounded-2xl shadow-[0_0_4px_rgba(0,0,0,0.1)] relative">
                                                {{-- Badge --}}
                                                @if (!empty($product_detail->badge))
                                                    <p
                                                        class="absolute top-9 right-9 font-medium text-white bg-orange-400 px-4 py-1.5 rounded-full">
                                                        {{ $product_detail->badge }}
                                                    </p>
                                                @endif
                                                {{-- Image --}}
                                                <img class="w-full h-64 object-cover rounded-2xl mb-4" src="{{ url($product_detail->product_image) }}" alt="{{ $product_detail->product_name }}" />
                                                {{-- Name --}}
                                                <h2 class="text-[#1C160C] text-lg font-bold mb-2">
                                                    {{ $product_detail->product_name }}
                                                </h2>
                                                {{-- Description --}}
                                                <p class="text-gray-500 font-normal mb-4">
                                                    {{ $product_detail->product_subtitle }}
                                                </p>

                                                <!-- Price & Booking Section -->
                                                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center my-2">
                                                    <!-- Price -->
                                                    <div class="flex-flex-col">
                                                        <div>
                                                            <h4 class="text-[#000000] text-xl font-bold">
                                                                {{ __('Price:') }}
                                                                <span class="text-gray-500 font-medium">{{ $product_detail->currency }} {{ formatCurrency($product_detail->sales_price) }}</span>
                                                                {{-- Check regular price is exists --}}
                                                                @if ($product_detail->sales_price != $product_detail->regular_price)
                                                                    <span class="line-through ml-2 text-gray-500 text-base">{{ $product_detail->currency }} {{ formatCurrency($product_detail->regular_price) }}</span>
                                                                @endif
                                                            </h4>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-[#000000] text-xl font-bold">
                                                                {{ __('Stock:') }}
                                                                <span class="text-{{ $product_detail->product_status == 'instock' ? 'orange-500' : 'red-500' }} font-medium">{{ $product_detail->product_status == 'outstock' ? __('Out of Stock') : __('In Stock') }}</span>
                                                            </h4>
                                                        </div>
                                                    </div>

                                                    <!-- Enquiry Button -->
                                                    @if ($enquiry_button != null)
                                                        @if ($whatsAppNumberExists == true)
                                                            <div class="mt-4 lg:mt-0 lg:mt-0 lg:w-auto w-full">
                                                                <a href="https://wa.me/{{ $enquiry_button }}?text={{ __('Hi, I am interested in your product:') }} {{ $product_detail->product_name }}. {{ __('Please provide more details.') }}" target="_blank"
                                                                    class="text-gray-50 w-full px-12 lg:w-auto bg-orange-400 text-base font-semibold border border-orange-200 py-3 rounded-xl hover:bg-orange-600 transition-colors block text-center">
                                                                    {{ __('Enquire') }}
                                                                </a>
                                                            </div>
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
                            <div class="relative">
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                    {{ __('Gallery') }}
                                </h2>
                                <div>
                                    <swiper-container class="mySwiper" autoplay-delay="3000" autoplay-disable-on-interaction="false">
                                        {{-- Slider images --}}
                                        @foreach ($galleries_details as $galleries_detail)
                                            <swiper-slide class="p-1">
                                                <!-- Gallery -->
                                                <div class="flex items-center justify-center bg-white rounded-t-2xl {{ $galleries_detail->caption ? '' : 'rounded-b-2xl' }} px-4 pt-4 shadow-[0_0_4px_rgba(0,0,0,0.1)] h-80 overflow-hidden">
                                                    <img class="w-full h-full object-cover rounded-t-2xl" src="{{ url($galleries_detail->gallery_image) }}" alt="{{ $galleries_detail->caption }}" />
                                                </div>
                                                {{-- Title --}}
                                                @if ($galleries_detail->caption)
                                                <div class="text-center bg-orange-50 p-4 rounded-b-2xl">
                                                    <h2 class="text-base font-semibold text-gray-700">
                                                        {{ $galleries_detail->caption }}
                                                    </h2>
                                                </div>
                                                @endif
                                            </swiper-slide>
                                        @endforeach
                                    </swiper-container>
                                </div>
                            </div>
                        @endif
                        <!-- End Gallery Section -->

                        <!-- Start Youtube Video Section -->
                        @if ($feature_details->where('type', 'youtube')->count() > 0)
                            <div class="relative">
                              
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                    {{ __('Youtube Videos') }}
                                </h2>
                                <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-4 items-center">
                                    {{-- Videos --}}
                                    @foreach ($feature_details as $feature)
                                        @if ($feature->type == 'youtube')
                                            <!-- Video -->
                                            <div class="rounded-2xl overflow-hidden">
                                                <iframe width="100%" height="270" src="https://www.youtube.com/embed/{{ $feature->content }}"
                                                    title="{{ $feature->label }}" frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen></iframe>
                                                {{-- Add Youtube title --}}
                                                @if ($feature->label != null)
                                                    <div class="px-5 py-3 bg-orange-50 rounded-b-2xl">
                                                        <div class="mb-2">
                                                            <div class="text-gray-800 font-semibold text-lg mb-2">
                                                                {{ $feature->label }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!-- End Youtube Video Section -->

                        <!-- Start iframe Section -->
                        @if ($feature_details->where('type', 'iframe')->count() > 0)
                            <div class="relative">
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                    {{ __('Iframe') }}
                                </h2>
                                <div class="grid grid-cols-1 gap-4 items-center">
                                    <!-- iframe -->
                                    @foreach ($feature_details as $feature)
                                        @if ($feature->type == 'iframe')
                                            <div class="rounded-2xl overflow-hidden">
                                                {{-- Content --}}
                                                <iframe width="100%" height="270" src="{{ $feature->content }}" title="{{ $feature->label }}" frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                {{-- Add Iframe title --}}
                                                @if ($feature->label != null)
                                                    <div class="px-5 py-3 bg-orange-50">
                                                        <div class="mb-2">
                                                            <div class="text-gray-800 font-semibold text-lg mb-2">
                                                                {{ $feature->label }}
                                                            </div>
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

                        <!-- Start Client Reviews section with Swiper JS -->
                        @if (count($testimonials) > 0)
                            <div class="relative">
                                <img src="{{ url('img/templates/developer/4.png') }}" alt="" class="w-20 absolute top-2 ltr:-right-7 rtl:-left-7 opacity-40" />
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                    {{ __('Client Reviews') }}
                                </h2>
                                <swiper-container class="mySwiper" autoplay-delay="3000" autoplay-disable-on-interaction="false" loop="true">
                                    {{-- Client Reviews --}}
                                    @foreach ($testimonials as $testimonial)
                                        <div class="swiper-slide">
                                            <div class="bg-orange-50 px-6 pt-9 pb-5 relative rounded-2xl border border-orange-200">
                                                {{-- Background image --}}
                                                <img src="{{ url('img/templates/developer/icon.png') }}" alt="" class="w-8 absolute top-4 left-4" />
                                                {{-- Image --}}
                                                <img src="{{ url($testimonial->reviewer_image) }}" alt="{{ $testimonial->reviewer_name }}" class="h-16 w-16 rounded-full object-cover mb-2" />
                                                {{-- Review --}}
                                                <p class="text-gray-600 text-sm italic">"{{ $testimonial->review }}"</p>
                                                <p class="text-orange-400 font-medium text-sm mt-2 text-right mr-2">
                                                    {{-- Name --}}
                                                    - {{ $testimonial->reviewer_name }}
                                                    {{-- Position --}}
                                                    @if ($testimonial->review_subtext)
                                                        <span class="text-gray-500 text-xs font-normal">
                                                            ({{ $testimonial->review_subtext }})
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </swiper-container>
                            </div>
                        @endif
                        <!-- End Client Reviews section with Swiper JS -->

                        <!-- Start an Application section -->
                        @if ($appointmentEnabled == true && isset($plan_details['appointment']) == 1)
                            <div class="relative">
                                {{-- Check appointment slots in the calendar --}}
                                @if ($plan_details['appointment'] == 1)
                                    @if ($appointment_slots != null)
                                        {{-- Heading --}}
                                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                            <div class="absolute bottom-10 left-1/2 h-1 w-14 bg-orange-400 -mx-6"></div>
                                            {{ __('Appointment') }}
                                        </h2>

                                        <div class="shadow-[0_0_4px_rgba(0,0,0,0.1)] border border-orange-200 bg-orange-50 overflow-hidden border rounded-2xl px-8 pt-10 pb-8 relative">
                                            <!-- Error Message (hidden by default) -->
                                            <div id="errorMessage" class="text-red-500 text-sm my-2 hidden">{{ __('Please select a valid date and time slot.') }}</div>

                                            {{-- Success Message (hidden by default) --}}
                                            <div id="successMessage" class="text-green-500 text-sm my-2 hidden">{{ __('Appointment booked successfully!') }}</div>

                                            <!-- Error Message (hidden by default) -->
                                            <div id="errorSubmitMessage" class="text-red-500 text-sm my-2 hidden">{{ __('Please fill all the fields.') }}</div>

                                            {{-- Background image --}}
                                            <img src="{{ url('img/templates/developer/icon.png') }}" alt="" class="w-8 absolute top-4 left-4" />

                                            <div class="flex flex-col justify-between mb-4 space-y-2">
                                                <!-- flatpickr Calendar -->
                                                <input type="text" id="appointment-date" class="flatpickr-input w-full px-4 py-3 text-gray-800 bg-white border border-gray-300 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-600 focus:ring-opacity-50"
                                                    placeholder="{{ __('Select a date') }}" />

                                                <!-- Select time in dropdown -->
                                                <select id="time-slot-select"
                                                    class="w-full px-4 py-3 text-gray-800 bg-white border border-gray-300 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-600 focus:ring-opacity-50">
                                                    <option value="">{{ __('Select a time slot') }}</option>
                                                </select>
                                            </div>

                                            <!-- Booking button -->
                                            <div class="flex justify-center">
                                                <button id="add-slot-button"
                                                    class="w-full p-3 bg-orange-400 text-white text-lg text-center rounded-xl font-semibold border border-orange-600" onclick="validateAndShowModal()">
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
                            <div class="relative">
                                <img src="{{ url('img/templates/developer/5.png') }}" alt="" class="w-16 absolute top-1 ltr:-left-6 rtl:-right-6 opacity-20" />
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                    {{ __('Payment Options') }}
                                </h2>
                                <div class="grid lg:grid-cols-2 gap-4">
                                    {{-- Payment options --}}
                                    @foreach ($payment_details as $payment)
                                        <!-- {{ $payment->label }} -->
                                        <div class="flex flex-col border border-orange-200 bg-orange-50 rounded-2xl px-4 pt-9 pb-4 relative">
                                            {{-- Background image --}}
                                            <img src="{{ url('img/templates/developer/icon.png') }}" alt="" class="w-8 absolute top-4 left-4" />

                                            <div class="flex justify-between items-center">
                                                <!-- {{ $payment->label }} Icon -->
                                                <div class="border border-orange-200 bg-orange-100 rounded-full p-2 flex items-center justify-center">
                                                    <i class="{{ $payment->icon }} text-orange-400"></i>
                                                </div>
                                                <!-- Payment link icon -->
                                                @if ($payment->type == 'url')
                                                    <a href="https://{{ str_replace('https://', '', $payment->content) }}" target="_blank" rel="noopener noreferrer">
                                                        <div
                                                            class="border border-orange-200 bg-orange-100 rounded-full p-2 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-external-link text-orange-400 h-6 w-6">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                                                <path d="M11 13l9 -9" />
                                                                <path d="M15 4h5v5" />
                                                            </svg>
                                                        </div>
                                                    </a>
                                                @endif

                                                {{-- UPI Payment --}}
                                                @if ($payment->type == 'upi')
                                                    <a href="upi://pay?pa={{ $payment->content }}&pn={{ urlencode($payment->label) }}&cu=INR" target="_blank" rel="noopener noreferrer">
                                                        <div
                                                            class="border border-orange-200 bg-orange-100 rounded-full p-2 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-external-link text-orange-400 h-6 w-6">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                                                <path d="M11 13l9 -9" />
                                                                <path d="M15 4h5v5" />
                                                            </svg>
                                                        </div>
                                                    </a>
                                                @endif
                                            </div>
                                            <h3 class="font-medium text-gray-800 {{ $payment->type == 'text' ? 'py-3' : 'pt-3' }}">{{ $payment->label }}</h3>
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

                        {{-- Start Business Hours --}}
                        @if ($plan_details['business_hours'] == 1)
                            @if ($business_hours != null && $business_hours->is_display != 0)
                                <section class="">
                                    <!-- Section Header -->
                                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
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
                                                            class="flex items-center justify-center w-10 h-10 bg-orange-50 text-orange-600 rounded-full">
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
                                                            <p class="text-sm font-medium text-gray-700 capitalize">
                                                                {{ __($day) }}</p>
                                                            <p class="text-base text-gray-900">
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
                                                    class="flex items-center justify-center w-12 h-12 bg-orange-50 text-orange-600 rounded-full transform hover:scale-110 transition-transform duration-300 ease-in-out">
                                                    <svg class="w-6 h-6 animate-pulse" fill="currentColor"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 2a10 10 0 100 20 10 10 0 000-20zM10 16l6-4-6-4v8z" />
                                                    </svg>
                                                </div>
                                                <!-- Text -->
                                                <div>
                                                    <p class="text-xl font-bold text-orange-600">
                                                        {{ __('Always Open') }}</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ __('We’re available 24/7 to serve you!') }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </section>
                            @endif
                        @endif
                        {{-- End Business Hours --}}

                        <!-- Start Location section -->
                        @if (count($feature_details) > 0 && $feature_details->contains('type', 'map'))
                            <div class="relative">
                                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                    {{ __('Location') }}
                                </h2>
                                {{-- Google Maps --}}
                                @foreach ($feature_details as $feature)
                                    @if ($feature->type == 'map')
                                        <iframe src="https://www.google.com/maps/embed?{!! $feature->content !!}" width="100%" height="300" style="border: 0" allowfullscreen="" loading="lazy"
                                            class="rounded-t-2xl">
                                        </iframe>
                                        {{-- Map title --}}
                                        @if ($feature->label != null)
                                            <div class="px-5 py-3 bg-orange-50 rounded-b-2xl">
                                                <div class="mb-2">
                                                    <div class="text-gray-800 font-semibold text-lg mb-2">
                                                        {{ $feature->label }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <!-- End Location section -->

                        <!-- Start Contact form section -->
                        @if ($plan_details['contact_form'] == 1)
                            @if ($business_card_details->enquiry_email != null)
                                <div class="relative pb-14 lg:pb-6">
                                    {{-- Background Image --}}
                                    <img src="{{ url('img/templates/developer/6.png') }}" alt="" class="w-24 absolute top-0 ltr:-right-8 rtl:-left-8 opacity-30" />
                                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 pt-12 pb-4">
                                        {{ __('Contact Us') }}
                                    </h2>

                                    {{-- Message Alert --}}
                                    @if (Session::has('message'))
                                        <div
                                            class="px-6 py-4 bg-orange-50 border-t-4 border-orange-100 rounded-lg shadow-md mb-6">
                                            <div class="flex items-start">
                                                <div class="mr-4">
                                                    <svg class="w-6 h-6 text-gray-700"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path
                                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-700">
                                                        {{ Session::get('message') }}</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ __('Please wait for the reply to be sent.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <form class="w-full max-w-full border px-6 pt-9 pb-6 rounded-2xl bg-orange-50 border-orange-200 relative" action="{{ route('sent.enquiry') }}" method="POST">
                                        @csrf

                                        {{-- Background Image --}}
                                        <img src="{{ url('img/templates/developer/icon.png') }}" alt="" class="w-8 absolute top-4 left-4" />
                                        <!-- Grid Layout -->
                                        <div class="grid grid-cols-1 gap-2 lg:gap-6 lg:grid-cols-2">
                                            <!-- Left Side Inputs -->
                                            <div class="grid grid-cols-1 gap-2">
                                                <input type="hidden" name="card_id" value="{{ $business_card_details->card_id }}" />
                                                {{-- Name --}}
                                                <div>
                                                    <label for="name" class="text-gray-800 font-semibold mb-2 block">{{ __('Name') }}</label>
                                                    <input type="text" name="name" placeholder="{{ __('Your Name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-200 focus:ring-2 focus:ring-orange-600 focus:ring-opacity-50" required />
                                                </div>
                                                {{-- Email --}}
                                                <div>
                                                    <label for="email" class="text-gray-800 font-semibold mb-2 block">{{ __('Email') }}</label>
                                                    <input type="email" name="email" placeholder="{{ __('Your Email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-200 focus:ring-2 focus:ring-orange-600 focus:ring-opacity-50" required />
                                                </div>
                                                {{-- Mobile Number --}}
                                                <div>
                                                    <label for="phone" class="text-gray-800 font-semibold mb-2 block">{{ __('Mobile Number') }}</label>
                                                    <input type="tel" name="phone" placeholder="{{ __('Your Mobile Number') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-200 focus:ring-2 focus:ring-orange-600 focus:ring-opacity-50" required />
                                                </div>
                                            </div>

                                            <!-- Right Side Textarea -->
                                            <div class="h-full pb-8">
                                                <label for="message" class="text-gray-800 font-semibold mb-2 block">{{ __('Message') }}</label>
                                                <textarea name="message" placeholder="{{ __('Your Message') }}"
                                                    class="w-full h-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-opacity-50 focus:border-orange-200 resize-none" required
                                                    style="min-height: 10rem"></textarea>
                                            </div>
                                                    
                                            {{-- ReCaptcha --}}
                                            @include('templates.includes.recaptcha')

                                        </div>

                                        <!-- Submit Button -->
                                        <div class="mt-6">
                                            <button type="submit"
                                                class="w-full px-4 py-4 bg-orange-400 text-white text-lg font-semibold rounded-xl focus:outline-none border border-orange-600 hover:bg-orange-500">
                                                {{ __('Send') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endif
                        <!-- End Contact form section -->

                        <!-- Branding Section -->
                        @if ($plan_details['hide_branding'] == 1)
                            <div class="pb-1">
                                <div
                                    class="flex pt-5 px-3 m-auto font-semibold text-white text-sm flex-col md:flex-row max-w-6xl">
                                    <div class="mt-2 text-gray-500">
                                        {{ __('Copyright') }} &copy;
                                        <a class="text-orange-500" href="{{ url($card_details->card_url) }}">
                                            {{ $card_details->title }}</a><span id="year"></span>{{ __('. All Rights Reserved.') }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="pb-1">
                                <div
                                    class="flexpx-3 m-auto pt-5 font-semibold text-white text-sm flex-col md:flex-row max-w-6xl">
                                    <div class="mt-2 text-gray-500">
                                        {{ __('Made with') }}
                                        <a class="text-orange-500" href="{{ env('APP_URL') }}">
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
                class="fixed w-full left-1/2 bottom-0 px-2 bg-orange-100/30 border border-orange-200 backdrop-blur-md py-4 flex lg:hidden md:hidden transform -translate-x-1/2 z-50">
                <!-- Profile Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <a class="border border-orange-200 p-3 rounded-full bg-orange-100" href="#profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-user text-orange-400 h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                    </a>
                </div>

                <!-- Send Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <button class="border border-orange-200 p-3 rounded-full bg-orange-100"
                        onclick="toggleWhatsAppModal(true)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-send text-orange-400 h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 14l11 -11" />
                            <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                        </svg>
                    </button>
                </div>

                <!-- Download Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <a href="{{ route('download.vCard', $business_card_details->card_id) }}" class="border border-orange-200 p-3 rounded-full bg-orange-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-download text-orange-400 h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 11l5 5l5 -5" />
                            <path d="M12 4l0 12" />
                        </svg>
                    </a>
                </div>

                <!-- Scan Icon -->
                <div class="flex-1 flex items-center justify-center">
                    <button class="border border-orange-200 p-3 rounded-full bg-orange-100"
                        onclick="toggleScanModal(true)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-line-scan text-orange-400 h-6 w-6">
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
                    <button class="border border-orange-200 p-3 rounded-full bg-orange-100"
                        onclick="shareToggleModal(true)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-share text-orange-400 h-6 w-6">
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
        <div id="appointmentModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <!-- Modal Content -->
            <div class="bg-white rounded-xl w-full max-w-md p-6 mx-4 shadow-lg">
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
                        <input type="text" id="name" class="mt-1 p-2 border border-gray-300 rounded-lg w-full"
                            required>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email"
                            class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <input type="email" id="email" class="mt-1 p-2 border border-gray-300 rounded-lg w-full"
                            required>
                    </div>

                    <!-- Phone Field -->
                    <div class="mb-4">
                        <label for="phone"
                            class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
                        <input type="text" id="phone" class="mt-1 p-2 border border-gray-300 rounded-lg w-full"
                            required>
                    </div>

                    <!-- Notes Field -->
                    <div class="mb-4">
                        <label for="notes"
                            class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                        <textarea id="notes" class="mt-1 p-2 border border-gray-300 rounded-lg w-full" rows="3"></textarea>
                    </div>

                    <!-- Hidden Price Field -->
                    <div class="mb-4 hidden">
                        <label for="price"
                            class="block text-sm font-medium text-gray-700">{{ __('Price') }}</label>
                        <input type="text" id="price" class="mt-1 p-2 border border-gray-300 rounded-lg w-full"
                            disabled>
                    </div>

                    {{-- ReCaptcha --}}
                    @include('templates.includes.recaptcha')

                    <!-- Submit and Close Buttons -->
                    <div class="flex justify-between">
                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600"
                            onclick="validateAndShowModal()">
                            {{ __('Close') }}
                        </button>
                        <button type="submit" id="bookAppointmentButton" class="bg-orange-400 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- End Appointment Modal --}}

        <!-- Share Modal -->
        <div id="shareModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50"
            onclick="shareToggleModal(false)">
            <!-- Modal content -->
            <div class="bg-white rounded-xl w-full max-w-md p-6 mx-4 space-y-6" onclick="event.stopPropagation()">
                <!-- Modal header -->
                <div class="flex justify-center items-center">
                    <h2 class="text-2xl text-center font-bold">{{ __('Share on') }}</h2>
                </div>

                <!-- QR Code Section -->
                <div class="flex justify-center">
                    <canvas id="shareQrCode"></canvas>
                </div>

                <!-- Share via Social Media -->
                <div class="flex justify-around text-orange-400">
                    <a href="{{ $shareComponent['facebook'] }}" target="_blank" class="hover:text-orange-700">
                        <i class="fab fa-facebook fa-2x"></i>
                    </a>
                    <a href="{{ $shareComponent['twitter'] }}" target="_blank" class="hover:text-orange-700">
                        <i class="fab fa-twitter fa-2x"></i>
                    </a>
                    <a href="{{ $shareComponent['linkedin'] }}" target="_blank" class="hover:text-orange-700">
                        <i class="fab fa-linkedin fa-2x"></i>
                    </a>
                    <a href="{{ $shareComponent['whatsapp'] }}" target="_blank" class="hover:text-orange-700">
                        <i class="fab fa-whatsapp fa-2x"></i>
                    </a>
                    <a href="{{ $shareComponent['telegram'] }}" target="_blank" class="hover:text-orange-700">
                        <i class="fab fa-telegram fa-2x"></i>
                    </a>
                </div>

                <!-- Copy Link Section -->
                <div class="flex justify-center">
                    <button onclick="copyLink()" class="bg-orange-400 text-white font-bold py-2 px-4 rounded-xl w-full border border-orange-600">
                        {{ __('Copy Link') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- WhatsApp Modal -->
        <div id="whatsappModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50"
            onclick="toggleWhatsAppModal(false)">
            <!-- Modal content (stops propagation to prevent closing when clicking inside) -->
            <div class="bg-orange-50 border-orange-200 rounded-3xl w-full max-w-md p-6 mx-4 space-y-6"
                onclick="event.stopPropagation()">
                <!-- Input for WhatsApp number -->
                <div>
                    <label for="whatsappNumber" class="block text-gray-700">{{ __('Enter WhatsApp Number') }}:</label>
                    <input type="text" id="whatsappNumber" placeholder="e.g., +919876543210"
                        class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-orange-200 focus:ring-2 focus:ring-orange-600 focus:ring-opacity-50" />
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button onclick="sendMessage()"
                        class="bg-orange-400 text-white font-bold py-2 px-4 rounded-xl w-full border border-orange-600">
                        {{ __('Send') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Scan Modal -->
        <div id="scanModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center qr-modal hidden z-50"
            onclick="toggleScanModal(false)">
            <!-- Modal content (stops propagation to prevent closing when clicking inside) -->
            <div class="rounded-2xl w-full max-w-md p-6 mx-4 space-y-6 border-orange-200 bg-orange-50 qr-modal-overlay"
                onclick="event.stopPropagation()">
                <!-- Qr Code -->
                <div class="flex justify-center flex-col items-center">
                    <div class="qr-code mb-2"></div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button onclick="downloadQr('{{ route('dynamic.card', $business_card_details->card_id) }}', 500)" id="download"
                        class="bg-orange-100 border border-orange-200 font-bold p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-download text-orange-400 h-6 w-6">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 11l5 5l5 -5" />
                            <path d="M12 4l0 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Start Check password protected Modal --}}
        @if ($business_card_details->password != null && Session::get('password_protected') == false)
            <div class="p-4 flex items-center justify-center">
                <div x-data="{ showModal: true }">
                    <!-- Modal -->
                    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50 p-3">
                        <div class="bg-white rounded-lg p-6 w-96 max-w-full shadow-lg transform transition-all duration-300"
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
                                    <p class="text-lg text-gray-600">{{ __('Enter your vcard Password') }}</p>
                                    <div class="flex">
                                        <input type="password" name="password"
                                            class="rounded rounded-r-lg bg-orange-50 border text-orange-800 focus:ring-orange-100 focus:border-orange-100 block flex-1 min-w-0 w-full text-sm border-orange-100 p-2.5"
                                            placeholder="{{ __('Password') }}" required>
                                    </div>

                                    {{-- Message --}}
                                    @if (Session::has('message'))
                                        <div class="flex items-center p-4 my-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-orange-800 dark:text-red-400"
                                            role="alert">
                                            <svg class="flex-shrink-0 inline w-4 h-4 mr-3 text-gray-50" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                            </svg>
                                            <span class="sr-only">{{ __('Failed') }}</span>
                                            <div>
                                                <span class="font-medium text-gray-50">{{ Session::get('message') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex flex-col space-y-4 mt-3">
                                        <button type="submit"
                                            class="bg-orange-500 text-white px-4 py-2 mt-2 rounded-lg hover:bg-orange-500 transition duration-300">{{ __('Password') }}</button>
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
                    'primary_color' => 'orange',
                    'img' => $business_card_details->profile
                ])
            @endif
        @endif

        {{-- Include Newsletter Modal --}}
        @if ($business_card_details != null)
            {{-- Check Newsletter --}}
            @if (!empty($business_card_details->is_newsletter_pop_active) && $business_card_details->is_newsletter_pop_active == 1)
                @include('templates.includes.old_theme_newsletter_modal', [
                    'primary_color' => 'orange'
                ])
            @endif
        @endif

        {{-- Include Information Popup Modal --}}
        @if ($business_card_details != null)
            {{-- Check Information Popup --}}
            @if (!empty($business_card_details->is_info_pop_active) && $business_card_details->is_info_pop_active == 1)
                @include('templates.includes.information_popup_modal', [
                    'primary_color' => 'orange'
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
    {{-- Swiper JS --}}
    <script src="{{ url('js/swiper-element-bundle.min.js') }}"></script>
    {{-- Flatpicker --}}
    <script src="{{ url('js/flatpickr.min.js') }}"></script>
    {{-- Custom JS --}}
    @yield('custom-js')
    
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
                        button.innerHTML = `{{ __('Book Appointment') }}`;
                    } else {
                        // If either field is empty, show an success message
                        errorSubmitMessage.classList.remove('hidden');
                        toggleModal(); // Close the modal on error

                        // Re-enable the button and revert its content
                        button.disabled = false;
                        button.innerHTML = `{{ __('Book Appointment') }}`;
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
    {{-- Custom JS --}}
    <script>
        // Generate QR Code and place in shareQrCode using qrious
        const qr = new QRious({
            element: document.getElementById('shareQrCode'),
            value: `{{ route('dynamic.card', $business_card_details->card_id) }}`, 
            size: 200,
            background: 'white',      // Background color
            foreground: 'black',      // Foreground (QR code) color
            level: 'H'                // Error correction level
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
