@extends('layouts.index', ['nav' => true, 'banner' => false, 'footer' => true, 'cookie' => true, 'setting' => true, 'title' => __('QR Code Maker - Web Tools')])

{{-- Check Google Adsense is "enabled" --}}
@section('custom-script')
    <script src="{{ asset('js/clipboard.min.js') }}"></script>
    @if ($settings->google_adsense_code != 'DISABLE_ADSENSE_ONLY')
        {{-- AdSense code --}}
        <script async
            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $settings->google_adsense_code }}"
            crossorigin="anonymous"></script>
    @endif
@endsection

@section('content')
    <section class="text-gray-700">
        <div class="container px-5 py-12 mx-auto">
            <div class="max-w-full mx-auto py-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6 md:text-left">
                    {{ __('QR Code Maker') }}
                </h1>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- QR Text Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('QR Text') }}</label>
                        <input type="text" id="qrtext" onkeyup="updateQr()"
                            class="w-full p-3 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="{{ __('Input placeholder') }}">
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Choose Logo') }}</label>
                        <input type="file" id="logo" name="logo" onchange="updateQr()" accept="image/*"
                            class="w-full p-3 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Logo Size -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Logo Size') }}</label>
                        <select id="logoSize" onchange="updateQr()"
                            class="w-full p-3 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="extra-small">{{ __('Extra Small') }}</option>
                            <option value="small">{{ __('Small') }}</option>
                            <option value="medium">{{ __('Medium') }}</option>
                            <option value="large">{{ __('Large') }}</option>
                        </select>
                    </div>

                    <!-- Text & Background Colors -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Text Color') }}</label>
                            <input type="color" id="textColor" value="#000000" oninput="updateQr()"
                                class="mt-1 w-full h-10 rounded-md border-gray-300 shadow-sm cursor-pointer">
                        </div>
                        <div class="w-full sm:w-1/2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Background Color') }}</label>
                            <input type="color" id="bg" value="#ffffff" oninput="updateQr()"
                                class="mt-1 w-full h-10 rounded-md border-gray-300 shadow-sm cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- QR Code Display -->
                <div class="mt-8 p-6 bg-gray-100 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-800 text-center md:text-left">{{ __('QR Image') }}</h3>
                    <div class="flex flex-col items-center justify-center mt-4">
                        <div id="canvasContainer" class="border p-4 bg-white rounded-lg shadow">
                            <canvas id="qrCanvas" class="max-w-full mx-auto"></canvas>
                        </div>
                        <button id="download"
                            class="mt-4 px-4 py-2 text-white font-medium rounded-lg shadow bg-gradient-to-r from-{{ $config[11]->config_value }}-400 to-{{ $config[11]->config_value }}-500 transition">
                            {{ __('Download QR') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Custom JS --}}
@section('custom-js')
    <script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
    <script src="{{ url('js/qrious.min.js') }}"></script>
    <script>
        "use strict";

        function updateQr() {
            "use strict";

            var qrParams = {
                text: $("#qrtext").val(),
                fontcolor: $("#textColor").val(),
                background: $("#bg").val()
            };

            var qr = new QRious({
                element: document.getElementById('qrCanvas'),
                value: qrParams.text,
                level: 'H',
                size: 500,
                foreground: qrParams.fontcolor,
                background: qrParams.background
            });

            // Load and overlay the logo onto the QR code canvas
            var logo = document.getElementById('logo');
            if (logo.files && logo.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = new Image();
                    img.src = e.target.result;
                    img.onload = function() {
                        var canvas = document.getElementById('qrCanvas');
                        var ctx = canvas.getContext('2d');
                        var logoSize = getLogoSize();
                        var logoX = (canvas.width - logoSize) / 2;
                        var logoY = (canvas.height - logoSize) / 2;
                        ctx.drawImage(img, logoX, logoY, logoSize, logoSize);
                    };
                };
                reader.readAsDataURL(logo.files[0]);
            }

            $("#download").show();
        }

        function getLogoSize() {
            "use strict";

            var canvas = document.getElementById('qrCanvas');
            var logoSize = 0;
            var selectedSize = $("#logoSize").val();
            switch (selectedSize) {
                case "extra-small":
                    logoSize = canvas.width * 0.1; // Adjust multiplier as needed
                    break;
                case "small":
                    logoSize = canvas.width * 0.2; // Adjust multiplier as needed
                    break;
                case "medium":
                    logoSize = canvas.width * 0.3; // Adjust multiplier as needed
                    break;
                case "large":
                    logoSize = canvas.width * 0.4; // Adjust multiplier as needed
                    break;
                default:
                    logoSize = canvas.width * 0.2; // Default to small size
                    break;
            }
            return logoSize;
        }

        // Function to download the QR code
        function downloadQr() {
            "use strict";
            
            var canvas = document.getElementById("qrCanvas");
            var dataURL = canvas.toDataURL("image/png");
            var a = document.createElement("a");
            a.href = dataURL;
            a.download = "qr_code.png";
            a.click();
        }

        // Call updateQr function when page loads and when input changes
        $(document).ready(updateQr);
        $("#mode, #font, #qrtext, #textColor, #fill, #bg").on("change input", updateQr);

        // Call downloadQr function when download button is clicked
        $("#download").on("click", downloadQr);
    </script>

    <script>
        // Array of element selectors
        var elementSelectors = ['.logoSize'];

        // Function to initialize TomSelect and enforce the "required" attribute
        function initializeTomSelectWithRequired(el) {
            new TomSelect(el, {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                maxOptions: null,
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties +
                                '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties +
                                '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            });

            // Ensure the "required" attribute is enforced
            el.addEventListener('change', function() {
                if (el.value) {
                    el.setCustomValidity('');
                } else {
                    el.setCustomValidity('This field is required');
                }
            });

            // Trigger validation on load
            el.dispatchEvent(new Event('change'));
        }

        // Loop through each element ID
        elementSelectors.forEach(function(id) {
            // Check if the element exists
            var elements = document.querySelectorAll(id);
            if (elements) {
                // Apply TomSelect and enforce the "required" attribute
                elements.forEach(function(el) {
                    initializeTomSelectWithRequired(el);
                });
            }
        });
    </script>
@endsection
@endsection
