<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ App::isLocale('ar') || App::isLocale('ur') || App::isLocale('he') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $settings->site_name }}</title>

    <link rel="icon" href="{{ asset($settings->favicon) }}" sizes="96x96" type="image/png" />

    <!-- CSS files -->
    @if (App::isLocale('ar') || App::isLocale('ur') || App::isLocale('he'))
        <link href="{{ asset('css/tabler.rtl.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/tabler-flags.rtl.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/tabler-payments.rtl.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/tabler-vendors.rtl.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/demo.rtl.min.css') }}" rel="stylesheet" />
    @else
        <link href="{{ asset('css/tabler.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/tabler-flags.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/tabler-payments.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/tabler-vendors.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/demo.min.css') }}" rel="stylesheet" />
    @endif

    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/chart.js') }}"></script>
    @yield('css')

    @if (isset($settings) && $settings)
        {{-- Check Google Analytics is "enabled" --}}
        @if (!empty($settings->google_analytics_id) && Cookie::get('laravel_cookie_consent') === '1')
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings->google_analytics_id }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());

                gtag('config', '{{ $settings->google_analytics_id }}');
            </script>
        @endif

        @if ($settings->google_adsense_code != 'DISABLE_ADSENSE_ONLY' && Cookie::get('laravel_cookie_consent') === '1')
            {{-- AdSense code --}}
            <script async
                src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $settings->google_adsense_code }}"
                crossorigin="anonymous"></script>
        @endif
    @endif
</head>

<body data-bs-theme="{{ optional(Auth::user())->choosed_theme == 'light' ? 'light' : 'dark' }}">

    {{-- Preloader --}}
    <div id="nativecode-loader">
        <div class="nativecode-loading"></div>
    </div>

    <div id="wrapper" class="page">
        
        @if (isset($nav) && $nav)
            @include('admin.includes.sidebar')
        @endif

        @if (isset($header) && $header)
            @include('admin.includes.header')
        @endif
        
        @yield('content')
    </div>

    <!-- Tabler Core -->
    <script type="text/javascript" src="{{ asset('js/tabler.min.js') }}"></script>
    @if (isset($demo) && $demo)
    <script type="text/javascript" src="{{ asset('js/admin-delete-query.js') }}"></script>
    @endif
    <script type="text/javascript" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatable.js') }}"></script>
    {{-- Preloader --}}
    <script>
        $(document).click(function (event) {
            "use strict";

            var clickover = $(event.target);
            var $navbar = $(".navbar-collapse");
            var _opened = $navbar.hasClass("show");
            if (_opened && !clickover.closest('.navbar').length) {
                $navbar.collapse('hide');
            }
        });
        
        // Wait for the window to load
        window.onload = function() {
            "use strict";

            // Get the preloader element
            var preloader = document.getElementById('nativecode-loader');

            // Add the fade-out class to start the fade-out effect
            setTimeout(function() {
                preloader.classList.add('fade-out');

                // After the fade-out effect, remove the preloader and show the content
                setTimeout(function() {
                    preloader.style.display = 'none'; // Remove preloader from view
                    document.querySelector('.page').style.display = 'flex'; // Show content
                }, 7000); // This duration matches the CSS transition duration (2s)
            }, 100); // Optional delay to allow the page to load fully
        };

        // Choose langages
        $('#chooseLang, #selectLang').change(function () {
            "use strict";

            // set the window's location property to the value of the option the user has selected
            window.location = `?change_language=` + $(this).val();
        });

        // Close sidebar
        document.addEventListener("click", function (event) {
            "use strict";

            const sidebar = document.getElementById("sidebar-menu");
            const toggler = document.querySelector(".navbar-toggler");

            // Check if the sidebar is open
            if (sidebar.classList.contains("show")) {
                // If the click is outside the sidebar and the toggler button
                if (!sidebar.contains(event.target) && !toggler.contains(event.target)) {
                    const bsCollapse = new bootstrap.Collapse(sidebar, {
                        toggle: false
                    });
                    bsCollapse.hide(); // Close the sidebar
                }
            }
        });
    </script>
    @yield('scripts')
</body>

</html>