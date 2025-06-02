@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

@section('content')
    <div class="page-wrapper">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                            {{ __('Overview') }}
                        </div>
                        <h2 class="page-title mb-2">
                            {{ __('Plugins') }}
                        </h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            {{-- Request Plugin --}}
                            <a href="https://forms.gle/TrWzqQDfNiRRsSS97" target="_blank" class="btn btn-white text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bulb">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12h1m8 -9v1m8 8h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7" />
                                    <path d="M9 16a5 5 0 1 1 6 0a3.5 3.5 0 0 0 -1 3a2 2 0 0 1 -4 0a3.5 3.5 0 0 0 -1 -3" />
                                    <path d="M9.7 17l4.6 0" />
                                </svg>
                                {{ __('Request') }}
                            </a>

                            {{-- Upload Plugin --}}
                            <button class="btn btn-primary" onclick="openFileManager()">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                    <path d="M7 9l5 -5l5 5" />
                                    <path d="M12 4l0 12" />
                                </svg>{{ __('Upload') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-fluid">
                {{-- Failed --}}
                @if (Session::has('failed'))
                    <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                        <div class="d-flex">
                            <div>
                                {{ Session::get('failed') }}
                            </div>
                        </div>
                        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Success --}}
                @if (Session::has('success'))
                    <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                        <div class="d-flex">
                            <div>
                                {{ Session::get('success') }}
                            </div>
                        </div>
                        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Plugins --}}
                {{-- check is empty --}}
                @if (empty($plugins))
                    <div class="empty">
                        <div class="empty-img">
                            <img src="{{ asset('img/plugins.svg') }}" height="256" alt="Plugins"
                                style="width: 100%; height: 250px;">
                        </div>
                        <p class="empty-title">{{ __('Coming Soon!') }}</p>
                        <p class="empty-subtitle text-secondary">
                            {{ __('Plugins are used to add extra functionality to GoBiz.') }}
                            <br>
                            {{ __('You can install plugins from the GoBiz Plugins Store.') }}
                        </p>
                        {{-- Notify Me --}}
                        <div class="empty-action">
                            <a href="https://zcmp.in/zThd?ref={{ urlencode(config('app.url')) }}&size=source"
                                target="_blank" class="btn btn-primary btn-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                    <path d="M3 7l9 6l9 -6" />
                                </svg>
                                {{ __('Notify Me') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="row">
                        @foreach ($plugins as $plugin)
                            <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                <div class="card h-100 d-flex flex-column">
                                    <div class="card-body flex-grow-1">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="{{ asset('img/plugins/' . $plugin['img']) }}" class=""
                                                style="width: 13%">
                                            <h3 class="card-title ms-3">{{ __($plugin['name']) }}</h3>
                                        </div>
                                        <p class="text-secondary">{{ __($plugin['description']) }}
                                        </p>
                                    </div>
                                    <!-- Card footer -->

                                    <div class="d-flex align-items-center gap-2 justify-content-end me-3 mb-2">
                                        <form action="{{ route('admin.plugins.delete', $plugin['plugin_id']) }}"
                                            method="POST" id="deleteForm{{ $plugin['plugin_id'] }}">
                                            @csrf
                                            @method('DELETE')
                                            @if (
                                                $plugin['plugin_id'] != 'TawkChat' &&
                                                    $plugin['plugin_id'] != 'WhatsappChatButton' &&
                                                    $plugin['plugin_id'] != 'GoogleRecaptcha' &&
                                                    $plugin['plugin_id'] != 'GoogleOAuth' &&
                                                    $plugin['plugin_id'] != 'GoogleAnalytics' &&
                                                    $plugin['plugin_id'] != 'GoogleAdSense' &&
                                                    $plugin['plugin_id'] != 'SMTP' &&
                                                    $plugin['plugin_id'] != 'OrderNFCSystem' &&
                                                    $plugin['plugin_id'] != 'ReferralSystem')
                                                <button type="button" style="width:42px; height:42px;"
                                                    class="btn btn-danger btn-icon text-white d-flex justify-content-center align-items-center"
                                                    data-bs-toggle="modal"
                                                    onclick="confirmationModel('{{ $plugin['plugin_id'] }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        style="width:28px; height:28px;" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </form>
                                        <a href="{{ route($plugin['main_route']) }}" style="width:42px; height:42px;"
                                            class="btn btn-white btn-icon d-flex align-items-center gap-2 justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                style="width:28px; height:28px;" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                            </svg>

                                        </a>
                                    </div>

                                    {{-- Version --}}
                                    <p class="text-secondary ms-3">
                                        {{ __('v') }}{{ $plugin['version'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>
    <!-- Confirmation Modal -->
    <div class="modal modal-blur fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">{{ __('Confirm Delete') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to remove this plugin? This action cannot be undone.') }}</div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(this)"
                        id="confirmDeleteBtn">{{ __('Remove') }}</button>
                </div>
            </div>
        </div>
    </div>
@section('scripts')
    <script type="text/javascript">
        function confirmationModel(pluginId) {
            // trugger alert
            // alert(pluginId);
            $('#confirmDeleteModal').modal('show');

            let btn = document.getElementById('confirmDeleteBtn');
            // add custom value to btn
            btn.setAttribute('data-plugin-id', pluginId);

        }

        function confirmDelete(btn) {
            let pluginId = btn.getAttribute('data-plugin-id');
            let form = document.getElementById('deleteForm' + pluginId);
            form.submit();
        }


        function openFileManager() {
            let input = document.createElement('input');
            input.type = 'file';
            input.accept = '.zip'; // Allow only ZIP files
            input.onchange = function(event) {
                let file = event.target.files[0];
                if (file) {
                    sendZipFile(file);
                }
            };
            input.click();
        }

        function sendZipFile(file) {
            let formData = new FormData();
            formData.append('zip_file', file);

            fetch("{{ route('admin.plugin.upload') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Plugin installation success!') {
                        window.location.reload(true);
                    } else if (data.message === 'Plugin Installation failed!') {
                        window.location.reload(true);
                    }
                })
                .catch(error => {});
        }
    </script>
@endsection
@endsection
