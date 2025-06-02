@extends('admin.layouts.index', ['header' => true, 'nav' => true, 'demo' => true])

{{-- Custom CSS --}}
@section('css')
<script type="text/javascript" src="{{ asset('js/tom-select.base.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/tinymce.min.js" integrity="sha512-KGtsnWohFUg0oksKq7p7eDgA1Rw2nBfqhGJn463/rGhtUY825dBqGexj8eP04LwfnsSW6dNAHAlOqKJKquHsnw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
    .ts-control {
        line-height: 1.7 !important;
    }
    .reduce-control {
        line-height: 1.7 !important;
    }
    .list-group-item {
        padding: 0.9rem 0rem !important;
    }
</style>
@endsection

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
                        <h2 class="page-title">
                            {{ __($gateway_details->payment_gateway_name) }} {{ __('Configuration') }}
                        </h2>
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

                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <form
                            action="{{ route('admin.update.payment.configuration', $gateway_details->payment_gateway_id) }}"
                            method="post" class="card">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    {{-- Paypal --}}
                                    @if ($gateway_details->payment_gateway_id == '60964401751ab')
                                        {{-- Mode --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Mode') }}</label>
                                                <select type="text" class="form-select"
                                                    placeholder="{{ __('Select a payment mode') }}" id="paypal_mode"
                                                    name="paypal_mode" required>
                                                    <option value="sandbox"
                                                        {{ $config[3]->config_value == 'sandbox' ? 'selected' : '' }}>
                                                        {{ __('Sandbox') }}</option>
                                                    <option value="live"
                                                        {{ $config[3]->config_value == 'live' ? 'selected' : '' }}>
                                                        {{ __('Live') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Client Key --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Client Key') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paypal_client_key" value="{{ $config[4]->config_value }}"
                                                    placeholder="{{ __('Client Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Secret --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label" required>{{ __('Secret') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paypal_secret" value="{{ $config[5]->config_value }}"
                                                    placeholder="{{ __('Secret') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Razorpay --}}
                                    @if ($gateway_details->payment_gateway_id == '60964410731d9')
                                        {{-- Client Key --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Client Key') }}</label>
                                                <input type="text" class="form-control" name="razorpay_client_key"
                                                    value="{{ $config[6]->config_value }}"
                                                    placeholder="{{ __('Client Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Secret --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Secret') }}</label>
                                                <input type="text" class="form-control" name="razorpay_secret"
                                                    value="{{ $config[7]->config_value }}"
                                                    placeholder="{{ __('Secret') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- PhonePe --}}
                                    @if ($gateway_details->payment_gateway_id == '19065566166715')
                                        {{-- Client ID --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Client ID') }}</label>
                                                <input type="text" class="form-control" name="clientId"
                                                    value="{{ $config[77]->config_value }}"
                                                    placeholder="{{ __('Client ID') }}" required>
                                            </div>
                                        </div>

                                        {{-- Client Version --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Client Version') }}</label>
                                                <input type="text" class="form-control" name="clientVersion"
                                                    value="{{ $config[78]->config_value }}"
                                                    placeholder="{{ __('Client Version') }}" required>
                                            </div>
                                        </div>

                                        {{-- Client Secret --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Client Secret') }}</label>
                                                <input type="text" class="form-control" name="clientSecret"
                                                    value="{{ $config[79]->config_value }}"
                                                    placeholder="{{ __('Client Secret') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Stripe --}}
                                    @if ($gateway_details->payment_gateway_id == '60964410732t9')
                                        {{-- Publishable Key --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Publishable Key') }}</label>
                                                <input type="text" class="form-control" name="stripe_publishable_key"
                                                    value="{{ $config[9]->config_value }}"
                                                    placeholder="{{ __('Publishable Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Secret --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Secret') }}</label>
                                                <input type="text" class="form-control" name="stripe_secret"
                                                    value="{{ $config[10]->config_value }}"
                                                    placeholder="{{ __('Secret') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Paystack --}}
                                    @if ($gateway_details->payment_gateway_id == '60964410736592')
                                        {{-- Publishable Key --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Public Key') }}</label>
                                                <input type="text" class="form-control" name="paystack_public_key"
                                                    value="{{ $config[33]->config_value }}"
                                                    placeholder="{{ __('Public Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Secret --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Secret Key') }}</label>
                                                <input type="text" class="form-control" name="paystack_secret"
                                                    value="{{ $config[34]->config_value }}"
                                                    placeholder="{{ __('Secret Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Merchant Email --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Merchant Email') }}</label>
                                                <input type="text" class="form-control" name="merchant_email"
                                                    value="{{ $config[36]->config_value }}"
                                                    placeholder="{{ __('Merchant Email') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Mollie --}}
                                    @if ($gateway_details->payment_gateway_id == '6096441071589632')
                                        {{-- Key --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Key') }}</label>
                                                <input type="text" class="form-control" name="mollie_key"
                                                    value="{{ $config[37]->config_value }}"
                                                    placeholder="{{ __('Key') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Mercadopago --}}
                                    @if ($gateway_details->payment_gateway_id == '776111730465')
                                        {{-- Public Key --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Public Key') }}</label>
                                                <input type="text" class="form-control" name="mercado_pago_public_key"
                                                    value="{{ $config[47]->config_value }}"
                                                    placeholder="{{ __('Public Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Access Token --}}
                                        <div class=" col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Access Token') }}</label>
                                                <input type="text" class="form-control"
                                                    name="mercado_pago_access_token"
                                                    value="{{ $config[48]->config_value }}"
                                                    placeholder="{{ __('Access Token') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Toyyibpay --}}
                                    @if ($gateway_details->payment_gateway_id == '767510608137')
                                        {{-- Mode --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Mode') }}</label>
                                                <select type="text" class="form-select"
                                                    placeholder="{{ __('Select a payment mode') }}" id="toyyibpay_mode"
                                                    name="toyyibpay_mode" required>
                                                    <option value="sandbox"
                                                        {{ $config[54]->config_value == 'sandbox' ? 'selected' : '' }}>
                                                        {{ __('Sandbox') }}</option>
                                                    <option value="live"
                                                        {{ $config[54]->config_value == 'live' ? 'selected' : '' }}>
                                                        {{ __('Live') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        {{-- Public Key --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('API Key') }}</label>
                                                <input type="text" class="form-control" name="toyyibpay_api_key"
                                                    value="{{ $config[49]->config_value }}"
                                                    placeholder="{{ __('API Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Category Code --}}
                                        <div class=" col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Category Code') }}</label>
                                                <input type="text" class="form-control" name="toyyibpay_category_code"
                                                    value="{{ $config[50]->config_value }}"
                                                    placeholder="{{ __('Category Code') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Flutterwave --}}
                                    @if ($gateway_details->payment_gateway_id == '754201940107')
                                        {{-- Public Key --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Public Key') }}</label>
                                                <input type="text" class="form-control" name="flw_public_key"
                                                    value="{{ $config[51]->config_value }}"
                                                    placeholder="{{ __('Public Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Secret Key --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Secret Key') }}</label>
                                                <input type="text" class="form-control" name="flw_secret_key"
                                                    value="{{ $config[52]->config_value }}"
                                                    placeholder="{{ __('Secret Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Encryption Key --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Encryption Key') }}</label>
                                                <input type="text" class="form-control" name="flw_encryption_key"
                                                    value="{{ $config[53]->config_value }}"
                                                    placeholder="{{ __('Encryption Key') }}" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Paddle --}}
                                    @if ($gateway_details->payment_gateway_id == '5992737427969')
                                        {{-- Paddle Environment --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Paddle Environment') }}</label>
                                                <select name="paddle_environment" id="paddle_environment"
                                                    class="form-select" required>
                                                    <option value="true"
                                                        {{ $config[64]->config_value == 'true' ? 'selected' : '' }}>
                                                        {{ __('Sandbox') }}</option>
                                                    <option value="false"
                                                        {{ $config[64]->config_value == 'false' ? 'selected' : '' }}>
                                                        {{ __('Production') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Paddle Seller ID --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Paddle Seller ID') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paddle_seller_id" value="{{ $config[65]->config_value }}"
                                                    placeholder="{{ __('Paddle Seller ID') }}" autocomplete="off"
                                                    required>
                                            </div>
                                        </div>

                                        {{-- Paddle API Key --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Paddle API Key') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paddle_api_key" value="{{ $config[66]->config_value }}"
                                                    placeholder="{{ __('Paddle API Key') }}" autocomplete="off" required>
                                            </div>
                                        </div>

                                        {{-- Paddle Client Side Token --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label required">{{ __('Paddle Client Side Token') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paddle_client_side_token"
                                                    value="{{ $config[67]->config_value }}"
                                                    placeholder="{{ __('Paddle Client Side Token') }}" autocomplete="off"
                                                    required>
                                            </div>
                                        </div>

                                        {{-- Paddle callback url (disabled) --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Paddle Callback Url') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paddle_callback_url"
                                                    value="{{ route('paddle.payment.status') }}"
                                                    placeholder="{{ __('Paddle Callback Url') }}" autocomplete="off"
                                                    disabled>
                                            </div>
                                        </div>

                                        {{-- Paddle Webhook Url (disabled) --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Paddle Webhook Url') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paddle_webhook_url"
                                                    value="{{ route('paddle.payment.webhook') }}"
                                                    placeholder="{{ __('Paddle Webhook Url') }}" autocomplete="off"
                                                    disabled>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- PayTR --}}
                                    @if ($gateway_details->payment_gateway_id == '5992737427970')
                                        {{-- PayTR Environment --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Mode') }}</label>
                                                <select name="paytr_mode" id="paytr_mode" class="form-select" required>
                                                    <option value="1"
                                                        {{ $config[71]->config_value == '1' ? 'selected' : '' }}>
                                                        {{ __('Sandbox') }}</option>
                                                    <option value="0"
                                                        {{ $config[71]->config_value == '0' ? 'selected' : '' }}>
                                                        {{ __('Production') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- PayTR Merchant ID --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Merchant ID') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paytr_merchant_id" value="{{ $config[68]->config_value }}"
                                                    placeholder="{{ __('Merchant ID') }}" autocomplete="off" required>
                                            </div>
                                        </div>

                                        {{-- PayTR Merchant Key --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Merchant Key') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paytr_merchant_key" value="{{ $config[69]->config_value }}"
                                                    placeholder="{{ __('Merchant Key') }}" autocomplete="off" required>
                                            </div>
                                        </div>

                                        {{-- PayTR merchant Salt Key --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Merchant Salt Key') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paytr_merchant_salt_key"
                                                    value="{{ $config[70]->config_value }}"
                                                    placeholder="{{ __('Merchant Salt Key') }}" autocomplete="off"
                                                    required>
                                            </div>
                                        </div>

                                        {{-- PayTR Callback URL --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Callback URL') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paytr_callback_url"
                                                    value="{{ route('paytr.payment.status') }}"
                                                    placeholder="{{ __('Callback URL') }}" autocomplete="off" disabled>
                                            </div>
                                        </div>

                                        {{-- PayTR Failure URL --}}
                                        <div class="col-md-6 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Failure URL') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="paytr_failure_url"
                                                    value="{{ route('paytr.payment.failure') }}"
                                                    placeholder="{{ __('Failure URL') }}" autocomplete="off" disabled>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Xendit --}}
                                    @if ($gateway_details->payment_gateway_id == '278523098674')
                                        {{-- Xendit Secret Key --}}
                                        <div class="col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Xendit Secret Key') }}</label>
                                                <input type="text" class="form-control reduce-control"
                                                    name="xendit_secret_key" value="{{ $config[72]->config_value }}"
                                                    placeholder="{{ __('Xendit Secret Key') }}" autocomplete="off"
                                                    required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Cashfree Settings --}}
                                    @if ($gateway_details->payment_gateway_id == '278523098675')
                                        {{-- Cashfree Settings --}}
                                        <div class="col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Cashfree Mode') }}</label>
                                                <select class="form-control" name="cashfree_mode" id="cashfree_mode"
                                                    required>
                                                    <option value="live"
                                                        @if ($config[84]->config_value == 'live') selected @endif>
                                                        {{ __('Live') }}
                                                    </option>
                                                    <option value="test"
                                                        @if ($config[84]->config_value == 'test') selected @endif>
                                                        {{ __('Test') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Cashfree API ID --}}
                                        <div class="col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Cashfree App ID') }}</label>
                                                <input type="text" class="form-control reduce-control"  name="cashfree_app_id"
                                                    value="{{ $config[85]->config_value }}" placeholder="{{ __('Cashfree App ID') }}"
                                                    autocomplete="off" required>
                                            </div>
                                        </div>

                                        {{-- Cashfree Secret Key --}}
                                        <div class="col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Cashfree Secret Key') }}</label>
                                                <input type="text" class="form-control reduce-control"  name="cashfree_secret_key"
                                                    value="{{ $config[86]->config_value }}" placeholder="{{ __('Cashfree Secret Key') }}"
                                                    autocomplete="off" required>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Offline (Bank Transfer) Settings --}}
                                    @if ($gateway_details->payment_gateway_id == '659644107y2g5')
                                        {{-- Offline (Bank Transfer) Settings --}}
                                        <div class="col-xl-12">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label required">{{ __('Offline (Bank Transfer) Details') }}</label>
                                                <textarea class="form-control" name="bank_transfer" id="bank_transfer" rows="3"
                                                    placeholder="{{ __('Offline (Bank Transfer) Details') }}" required>{{ $config[31]->config_value }}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        @include('admin.includes.footer')
    </div>

    {{-- Custom JS --}}
@section('scripts')
    <script>
        tinymce.init({
            selector: 'textarea#bank_transfer',
            plugins: 'code preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | pagebreak | link',
            toolbar_sticky: true,
            height: 200,
            menubar: false,
            statusbar: false,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        });

        // Array of element IDs
        var elementSelectors = ['paypal_mode', 'toyyibpay_mode', 'paddle_environment', 'paytr_mode', 'cashfree_mode'];

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
            var el = document.getElementById(id);
            if (el) {
                // Apply TomSelect and enforce the "required" attribute
                initializeTomSelectWithRequired(el);
            }
        });
    </script>
@endsection
@endsection
