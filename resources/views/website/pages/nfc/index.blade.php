@php
    // Page content
    use Illuminate\Support\Facades\DB;
    $config = DB::table('config')->get();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __($title) }}</title>
    {{-- Tailwind CSS --}}
    <link rel="stylesheet" href="{{ asset('app/css/tailwind.min.css') }}">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    {{-- Not Found Page --}}
    <section class="text-center px-6">
        <div class="max-w-lg mx-auto">
            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-alert-square-rounded w-24 h-24 text-{{ $config[11]->config_value }}-500">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
                    <path d="M12 8v4" />
                    <path d="M12 16h.01" />
                </svg>
            </div>

            <h2 class="text-2xl sm:text-5xl font-bold text-gray-900 mb-4">
                {{ __($title) }}
            </h2>
            <p class="text-lg text-gray-600 mb-6">
                {{ __($description) }}
            </p>

            <a href="{{ route('user.manage.nfc.cards') }}"
                class="inline-block px-6 py-3 text-sm font-medium text-white bg-{{ $config[11]->config_value }}-500 rounded-md hover:bg-{{ $config[11]->config_value }}-600">
                {{ __('Login & Link') }}
            </a>
        </div>
    </section>

</body>

</html>
