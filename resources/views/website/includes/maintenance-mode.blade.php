@php
    use Illuminate\Support\Facades\DB;

    // Queries
    $config = DB::table('config')->get();

    // Get the maintenance secret code from the .env file
    $secret = env('MAINTENANCE_SECRET_CODE');
@endphp

<div
    class="fixed top-0 left-0 w-full bg-{{ $config[11]->config_value }}-100 border-b-4 border-{{ $config[11]->config_value }}-500 text-{{ $config[11]->config_value }}-700 p-4 z-50">
    <div class="flex flex-col sm:flex-row sm:items-center items-start justify-between max-w-screen-lg mx-auto">
        <!-- Badge -->
        <div class="flex items-center mb-3 sm:mb-0">
            <span
                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-{{ $config[11]->config_value }}-500 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icon-tabler-eye">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                </svg>
            </span>
            <span class="ml-2 text-sm text-left">{{ __('Maintenance Mode Active') }}</span>
        </div>

        <!-- Secret Code Display -->
        @if ($secret != '')
            <span class="flex items-center lg:ml-2 text-sm text-left sm:text-center">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-{{ $config[11]->config_value }}-500 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icon-tabler-login-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                        <path d="M3 12h13l-3 -3" />
                        <path d="M13 15l3 -3" />
                    </svg>
                </span>

                <a href="{{ route('admin.settings') }}"
                    class="text-{{ $config[11]->config_value }}-600 hover:text-{{ $config[11]->config_value }}-700 ml-2">
                    {{ __('Login access to Admin') }}
                </a>
            </span>
        @endif
    </div>
</div>

{{-- Show secret code in modal --}}
@if ($secret != '')
    <div id="modal-secret-code" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black opacity-75"></div>

        <!-- Large Modal Box -->
        <div class="relative w-full max-w-5xl p-8 bg-white rounded-lg shadow-lg z-10">
            <!-- Modal content -->
            <div class="text-center text-gray-500">
                <p class="text-lg">
                    {{ __('Save the secret code below and enter it on the login page to access the Admin panel.') }}
                </p>

                {{-- Secret code --}}
                <div class="flex items-center justify-center mt-4">
                    <h2 id="secret-code" class="text-gray-900 font-semibold text-4xl">{{ $secret }}</h2>
                    <button onclick="copySecretCode()"
                        class="ml-3 p-2 text-white bg-{{ $config[11]->config_value }}-600 rounded hover:bg-{{ $config[11]->config_value }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $config[11]->config_value }}-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                            <path
                                d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                        </svg>
                    </button>
                </div>

                <!-- Tailwind Alert for Copy Notification -->
                <div id="copy-alert"
                    class="hidden fixed top-4 right-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-lg">
                    {{ __('Secret code copied to clipboard!') }}
                </div>

                {{-- For example: https://domain.com/secret-code --}}
                <p class="text-sm text-gray-500 mt-4">
                    {{ __('For example:') }} <a href="{{ config('app.url') }}/admin/settings" target="_blank"
                        rel="noopener noreferrer"><span
                            class="text-indigo-500">{{ config('app.url') }}/{{ $secret }}</span></a>
                </p>
            </div>
            <!-- Close button -->
            <button type="button"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                onclick="document.getElementById('modal-secret-code').style.display = 'none'">
                <span class="sr-only">{{ __('Close') }}</span>
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
@endif

{{-- Custom JS --}}
<script>
    // Close modal when clicking outside of the modal content
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('modal-secret-code');
        const modalContent = modal.querySelector('.relative');

        if (!modalContent.contains(event.target)) {
            modal.style.display = 'none';
        }
    });

    // Copy secret code to clipboard
    function copySecretCode() {
        // Get the secret code text
        const secretCode = document.getElementById('secret-code').innerText;

        // Copy the text to the clipboard
        navigator.clipboard.writeText(secretCode).then(function() {
            // Show the alert
            const alertBox = document.getElementById('copy-alert');
            alertBox.classList.remove('hidden');
            alertBox.classList.add('block');

            // Hide the alert after 3 seconds
            setTimeout(function() {
                alertBox.classList.remove('block');
                alertBox.classList.add('hidden');
            }, 3000);
        }).catch(function(error) {
            console.error("Copy failed", error);
        });
    }
</script>
