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

@php
    use Illuminate\Support\Facades\Auth;
    use App\Transaction;
    use Carbon\Carbon;

    // Fetch current user's details
    $user = Auth::user();
    $allowedPermissions = json_decode($user->permissions, true);

    // Ensure `$allowedPermissions` is an array (to handle cases where permissions are null or malformed)
    if (!is_array($allowedPermissions)) {
        $allowedPermissions = [];
    }

    // Add or update missing permissions
    $defaultPermissions = [
        'coupons' => 1,
        'custom_domain' => 1,
        'marketing' => 1,
        'maintenance_mode' => 1,
        'demo_mode' => 1,
        'backup' => 1,
        'nfc_card_design' => 1,
        'nfc_card_orders' => 1,
        'nfc_card_order_transactions' => 1,
        'nfc_card_key_generations' => 1,
        'email_templates' => 1,
        'plugins' => 1,
        'referral_system' => 1,
    ];

    // Merge default permissions with the current ones (current values take precedence)
    $allowedPermissions = array_merge($defaultPermissions, $allowedPermissions);

    // Update user details if permissions were changed
    if ($allowedPermissions !== json_decode($user->permissions, true)) {
        $user->permissions = json_encode($allowedPermissions);
        $user->updated_at = Carbon::now(); // Update timestamp explicitly
        $user->save(); // Save changes to the database
    }

    // Fetch updated permissions
    $allowedPermissions = json_decode($user->permissions, true);
@endphp

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
                        {{ __('Settings') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            {{-- Failed --}}
            @if(Session::has("failed"))
            <div class="alert alert-important alert-danger alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('failed')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Success --}}
            @if(Session::has("success"))
            <div class="alert alert-important alert-success alert-dismissible mb-2" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('success')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Settings --}}
            <div class="card">
                <div class="card-body">
                    <div class="accordion" id="accordion-example">
                        {{-- General Configuration Settings --}}
                        <div class="accordion-item">
                            <h4 class="accordion-header" id="heading-1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-1" aria-expanded="false">
                                    <h2>{{ __('General Configuration Settings') }}</h2>
                                </button>
                            </h4>
                            <div id="collapse-1" class="accordion-collapse collapse"
                                data-bs-parent="#accordion-example">
                                <div class="accordion-body pt-0">
                                    <form action="{{ route('admin.change.general.settings') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            {{-- Show Website Frontend? --}}
                                            <div class=" col-xl-4 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="show_website">{{
                                                        __('Show Website Front-end?') }}</label>
                                                    <select name="show_website" id="show_website" class="form-select" required>
                                                        <option value="yes" {{ $config[38]->config_value == 'yes' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                        <option value="no" {{ $config[38]->config_value == 'no' ? 'selected' : '' }}>{{ __('No') }}</option>
                                                    </select>
                                                    <small class="text-muted">{{ __('Turn on or off your website.') }}</small>
                                                </div>
                                            </div>

                                            {{-- Enable/disable registration page --}}
                                            <div class=" col-xl-4 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="registration_page">{{ __('Disable registration page?') }}</label>
                                                    <select name="registration_page" id="registration_page" class="form-select" required>
                                                        <option value="1" {{ $config[63]->config_value == '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                        <option value="0" {{ $config[63]->config_value == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                                                    </select>
                                                    <small class="text-muted">{{ __('Turn on or off registration page.') }}</small>
                                                </div>
                                            </div>

                                            {{-- Timezone --}}
                                            <div class=" col-xl-4 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="timezone">{{
                                                        __('Timezone')
                                                        }}</label>
                                                    <select name="timezone" id="timezone" class="form-select" required>
                                                        @foreach (timezone_identifiers_list() as $timezone)
                                                        <option value="{{ $timezone }}" {{ $config[2]->config_value
                                                            ==
                                                            $timezone ? 'selected' : '' }}>
                                                            {{ $timezone }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Currency --}}
                                            <div class=" col-xl-4 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="currency">{{
                                                        __('Currency')
                                                        }}</label>
                                                    <select name="currency" id="currency" class="form-select" required>
                                                        @foreach ($currencies as $currency)
                                                        <option value="{{ $currency->iso_code }}" {{ $config[1]->config_value == $currency->iso_code ? 'selected' : '' }}>
                                                            {{ $currency->name }} ({{ $currency->symbol }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Currency format type --}}
                                            <div class=" col-xl-4 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="currency_format">{{ __('Currency Format') }}</label>
                                                    <select name="currency_format" id="currency_format" class="form-select" required>
                                                        <option value="1,234,567.89" {{ $config[55]->config_value == "1,234,567.89" ? 'selected' : '' }}>{{ __("1,234,567.89") }}</option>
                                                        <option value="12,34,567.89" {{ $config[55]->config_value == "12,34,567.89" ? 'selected' : '' }}>{{ __("12,34,567.89") }}</option>
                                                        <option value="1.234.567,89" {{ $config[55]->config_value == "1.234.567,89" ? 'selected' : '' }}>{{ __("1.234.567,89") }}</option>
                                                        <option value="1 234 567,89" {{ $config[55]->config_value == "1 234 567,89" ? 'selected' : '' }}>{{ __("1 234 567,89") }}</option>
                                                        <option value="1'234'567.89" {{ $config[55]->config_value == "1'234'567.89" ? 'selected' : '' }}>{{ __("1'234'567.89") }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Currency Decimals Places --}}
                                            <div class="col-xl-4 mb-2">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="currency_decimals_place">{{ __('Decimals Places') }}</label>
                                                    <input type="number" class="form-control reduce-control" name="currency_decimals_place" id="currency_decimals_place" value="{{ $config[56]->config_value }}" placeholder="{{ __('Decimals Places') }}" min="0" step="1" max="3" required>
                                                    <small class="text-muted">{{ __('If you don\'t need decimal vale, set 0')}}</small>
                                                </div>
                                            </div>

                                            {{-- Date Time Format --}}
                                            <div class="col-xl-4 mb-2">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="date_time_format">{{ __('Date Time Format') }}</label>
                                                    <select name="date_time_format" id="date_time_format" class="form-select" required>
                                                        @php
                                                            $availableDateTimeFormats = getDateTimeFormats();
                                                        @endphp
                                                        @foreach ($availableDateTimeFormats as $key => $value)
                                                            <option value="{{ $key }}" {{ $config[75]->config_value == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Default Language --}}
                                            <div class="col-xl-4 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="default_language">{{ __('Default Language') }}</label>
                                                    <select name="default_language" id="default_language" class="form-select" required>
                                                        @php
                                                            $availableLanguages = [
                                                                'en' => __('English'), 'ar' => __('Arabic'), 'bn' => __('Bangla'), 'bg' => __('Bulgarian'), 'zh' => __('Chinese'),
                                                                'nl' => __('Dutch'), 'fr' => __('French'), 'de' => __('German'),
                                                                'ht' => __('Haitian Creole'), 'hi' => __('Hindi'), 'he' => __('Hebrew'), 'hu' => __('Hungarian'),
                                                                'id' => __('Indonesian'), 'it' => __('Italian'), 'ja' => __('Japanese'), 'lt' => __('Lithuanian'),
                                                                'ms' => __('Malay'), 'pt' => __('Portuguese'), 'pl' => __('Polish'), 'ro' => __('Romanian'),
                                                                'ru' => __('Russian'), 'es' => __('Spanish'), 'si' => __('Sinhala'), 'sv' => __('Swedish'),
                                                                'ta' => __('Tamil'), 'th' => __('Thai'), 'tr' => __('Turkish'), 'ur' => __('Urdu'),
                                                                'vi' => __('Vietnamese')
                                                            ];
                                                        @endphp

                                                        @foreach($availableLanguages as $code => $name)
                                                            <option value="{{ $code }}" {{ $defaultLanguage == $code ? 'selected' : '' }}>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Website Languages --}}
                                            <div class="col-xl-4 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label required" for="language">{{ __('Website Languages') }}</label>
                                                    <select name="languages[]" id="languages" class="form-select" required multiple>
                                                        @php
                                                            $availableLanguages = [
                                                                'en' => __('English'), 'ar' => __('Arabic'), 'bn' => __('Bangla'), 'bg' => __('Bulgarian'), 'zh' => __('Chinese'),
                                                                'nl' => __('Dutch'), 'fr' => __('French'), 'de' => __('German'),
                                                                'ht' => __('Haitian Creole'), 'hi' => __('Hindi'), 'he' => __('Hebrew'), 'hu' => __('Hungarian'),
                                                                'id' => __('Indonesian'), 'it' => __('Italian'), 'ja' => __('Japanese'), 'lt' => __('Lithuanian'),
                                                                'ms' => __('Malay'), 'pt' => __('Portuguese'), 'pl' => __('Polish'), 'ro' => __('Romanian'),
                                                                'ru' => __('Russian'), 'es' => __('Spanish'), 'si' => __('Sinhala'), 'sv' => __('Swedish'),
                                                                'ta' => __('Tamil'), 'th' => __('Thai'), 'tr' => __('Turkish'), 'ur' => __('Urdu'),
                                                                'vi' => __('Vietnamese')
                                                            ];
                                                        @endphp

                                                        @foreach($availableLanguages as $code => $name)
                                                            <option value="{{ $code }}" @if(in_array($code, $selectedLanguages ?? [])) selected @endif>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Default Plan Term Detting --}}
                                            <div class="col-xl-4">
                                                <h2 class="page-title my-3">
                                                    {{ __('Default Plan Term Settings') }}
                                                </h2>
                                                <div class="mb-3">
                                                    <label class="form-label required" for="term">{{ __('Default Plan Term')
                                                        }}</label>
                                                    <select name="term" id="term" class="form-select" required>
                                                        <option value="monthly" {{ $config[8]->config_value ==
                                                            'monthly'
                                                            ? '
                                                           selected' : '' }}>
                                                            {{ __('Monthly') }}</option>
                                                        <option value="yearly" {{ $config[8]->config_value ==
                                                            'yearly' ?
                                                            '
                                                           selected' : '' }}>
                                                            {{ __('Yearly') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Cookie Consent Settings --}}
                                            <div class="col-xl-4">
                                                <h2 class="page-title my-3">
                                                    {{ __('Cookie Consent Settings') }}
                                                </h2>
                                                <div class="mb-3">
                                                    <label class="form-label required" for="cookie">{{ __('Cookie Consent') }}</label>
                                                    <select name="cookie" id="cookie" class="form-select" required>
                                                        <option value="true" {{ env('COOKIE_CONSENT_ENABLED') == true ? 'selected' : '' }}>{{ __('Enable') }}</option>
                                                        <option value="false" {{ env('COOKIE_CONSENT_ENABLED') == false || env('COOKIE_CONSENT_ENABLED') == '' ? 'selected' : '' }}>{{ __('Disable') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Image Upload Limit --}}
                                            <div class="col-xl-4 mb-2">
                                                <h2 class="page-title my-3">
                                                    {{ __('Image Upload Limit') }}
                                                </h2>
                                                <div class="mb-3">
                                                    <label class="form-label" for="image_limit">{{ __('Size in Kilobytes') }}
                                                    </label>
                                                    <input type="number" class="form-control reduce-control" name="image_limit"
                                                        value="{{ $settings->image_limit['SIZE_LIMIT'] }}"
                                                        placeholder="{{ __('Size') }}" min="1024">
                                                    <small class="text-muted">{{ __('For example, if you want to limit the size to 5MB, set 5120')}}</small>
                                                </div>
                                            </div>

                                            <div class="row">                                             
                                                {{-- Tiny Cloud API Key --}}
                                                <div class="col-md-6 col-xl-6 mb-2 d-none">
                                                    <h2 class="page-title my-3">
                                                        {{ __('Tiny Cloud (Text Editor) Configuration Settings') }}
                                                    </h2>
                                                    <div class="mb-3">
                                                        <label class="form-label required" for="tiny_api_key">{{
                                                            __('Tiny Cloud API Key') }}
                                                        </label>
                                                        <input type="text" class="form-control" name="tiny_api_key"
                                                            value="{{ $config[39]->config_value }}"
                                                            placeholder="{{ __('Tiny Cloud API Key (Eg: ytf5**************************)') }}"
                                                            required>
                                                        <span>{{ __('If you did not get a Tiny Cloud API Key, create a')
                                                            }} <a href="https://www.tiny.cloud/my-account/dashboard"
                                                                rel="nofollow" target="_blank">{{
                                                                __('new API Key.') }}</a> </span>
                                                    </div>
                                                </div>
                                            </div>                                        

                                            {{-- Share Content Settings --}}
                                            <div class="row">
                                                <!-- Section Header -->
                                                <div class="col-12">
                                                    <h2 class="page-title fw-bold mb-4">{{ __('Share Content Settings') }}</h2>
                                                </div>

                                                <!-- Share Content Input -->
                                                <div class="col-lg-6">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-header bg-light">
                                                            <h5 class="card-title mb-0 text-dark">{{ __('Share Content') }}</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <label for="share_content" class="form-label fw-semibold">
                                                                {{ __('Content to Share') }} <span class="text-danger">*</span>
                                                            </label>
                                                            <textarea 
                                                                class="form-control shadow-sm" 
                                                                name="share_content" 
                                                                id="share_content" 
                                                                rows="5" 
                                                                placeholder="{{ __('Enter content to share') }}" 
                                                                required>{{ $config[30]->config_value }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Short Codes Section -->
                                                <div class="col-lg-6">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-header bg-light">
                                                            <h5 class="card-title mb-0 text-dark">{{ __('Available Short Codes') }}</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <p class="text-muted mb-2">{{ __('Use the following short codes in your content:') }}</p>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item">
                                                                    <strong>{ business_name }</strong> - {{ __('Business Name') }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>{ business_url }</strong> - {{ __('Business URL or Address') }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>{ appName }</strong> - {{ __('App Name') }}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Update button --}}
                                            <div class="text-end bottom-fix">
                                                <div class="d-flex">
                                                    <button type="submit" class="btn btn-primary btn-md ms-auto">
                                                        {{ __('Update') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Website Configuration Settings --}}
                        <div class="accordion-item">
                            <h4 class="accordion-header" id="heading-2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-2" aria-expanded="false">
                                    <h2>{{ __('Website Configuration Settings') }}</h2>
                                </button>
                            </h4>
                            <div id="collapse-2" class="accordion-collapse collapse"
                                data-bs-parent="#accordion-example">
                                <div class="accordion-body pt-0">
                                    <form action="{{ route('admin.change.website.settings') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">

                                            {{-- Theme Colors --}}
                                            <div class="col-md-12 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label required">{{ __('Theme Colors')
                                                        }}</label>
                                                    <div class="row g-2">

                                                        <div class="col-auto">
                                                            <label class="form-colorinput">
                                                                <input name="app_theme" type="radio" value="blue"
                                                                    class="form-colorinput-input" {{
                                                                    $config[11]->config_value == 'blue' ? 'checked'
                                                                : ''
                                                                }}
                                                                />
                                                                <span
                                                                    class="form-colorinput-color rounded-circle bg-blue"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-auto">
                                                            <label class="form-colorinput form-colorinput-light">
                                                                <input name="app_theme" type="radio" value="indigo"
                                                                    class="form-colorinput-input" {{
                                                                    $config[11]->config_value == 'indigo' ?
                                                                'checked' :
                                                                ''
                                                                }} />
                                                                <span
                                                                    class="form-colorinput-color rounded-circle bg-indigo"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-auto">
                                                            <label class="form-colorinput">
                                                                <input name="app_theme" type="radio" value="green"
                                                                    class="form-colorinput-input" {{
                                                                    $config[11]->config_value == 'green' ? 'checked'
                                                                :
                                                                '' }}
                                                                />
                                                                <span
                                                                    class="form-colorinput-color rounded-circle bg-green"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-auto">
                                                            <label class="form-colorinput">
                                                                <input name="app_theme" type="radio" value="yellow"
                                                                    class="form-colorinput-input" {{
                                                                    $config[11]->config_value == 'yellow' ?
                                                                'checked' :
                                                                ''
                                                                }} />
                                                                <span
                                                                    class="form-colorinput-color rounded-circle bg-yellow"></span>
                                                            </label>
                                                        </div>

                                                        <div class="col-auto">
                                                            <label class="form-colorinput">
                                                                <input name="app_theme" type="radio" value="red"
                                                                    class="form-colorinput-input" {{
                                                                    $config[11]->config_value == 'red' ? 'checked' :
                                                                ''
                                                                }}
                                                                />
                                                                <span
                                                                    class="form-colorinput-color rounded-circle bg-red"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-auto">
                                                            <label class="form-colorinput">
                                                                <input name="app_theme" type="radio" value="purple"
                                                                    class="form-colorinput-input" {{
                                                                    $config[11]->config_value == 'purple' ?
                                                                'checked' :
                                                                ''
                                                                }} />
                                                                <span
                                                                    class="form-colorinput-color rounded-circle bg-purple"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-auto">
                                                            <label class="form-colorinput">
                                                                <input name="app_theme" type="radio" value="pink"
                                                                    class="form-colorinput-input" {{
                                                                    $config[11]->config_value == 'pink' ? 'checked'
                                                                : ''
                                                                }}
                                                                />
                                                                <span
                                                                    class="form-colorinput-color rounded-circle bg-pink"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-auto">
                                                            <label class="form-colorinput form-colorinput-light">
                                                                <input name="app_theme" type="radio" value="gray"
                                                                    class="form-colorinput-input" {{
                                                                    $config[11]->config_value == 'gray' ? 'checked'
                                                                : ''
                                                                }}
                                                                />
                                                                <span
                                                                    class="form-colorinput-color rounded-circle bg-muted"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Themes Slider on/off in home page --}}
                                            <div class="col-xl-4">
                                                <div class="mb-3">
                                                    <label class="form-label required">{{ __('Show themes slider in home page?') }}</label>
                                                    <select name="show_home_slider" id="show_home_slider" class="form-select" required>
                                                        <option value="1" {{ $config[87]->config_value == '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                        <option value="0" {{ $config[87]->config_value == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Home Banner Image --}}
                                            <div class="col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Banner Image') }}</div>
                                                    <input type="file" class="form-control" name="primary_image"
                                                        placeholder="{{ __('Banner Image') }}"
                                                        accept=".png,.jpg,.jpeg,.gif,.webp,.svg" />
                                                    <small class="text-muted">
                                                        {{ __('Recommended size : 1000 x 667') }}</small>
                                                </div>
                                            </div>

                                            {{-- Signup/Signin Image --}}
                                            <div class="col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Signup/Signin Image') }}</div>
                                                    <input type="file" class="form-control" name="secondary_image"
                                                        placeholder="{{ __('Signup/Signin Image') }}"
                                                        accept=".png,.jpg,.jpeg,.gif,.webp,.svg" />
                                                    <small class="text-muted">
                                                        {{ __('Recommended size : 486 x 605') }}</small>
                                                </div>
                                            </div>

                                            {{-- Logo ({{ __('Dark') }}) --}}
                                            <div class="col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Logo') }} ({{ __('Dark') }})</div>
                                                    <input type="file" class="form-control" name="site_logo"
                                                        placeholder="{{ __('Logo') }}"
                                                        accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                    <small class="text-muted">{{ __('Recommended size : 200 x 90') }}</small>
                                                </div>
                                            </div>

                                            {{-- Logo ({{ __('Light') }}) --}}
                                            <div class="col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Logo') }} ({{ __('Light') }})</div>
                                                    <input type="file" class="form-control" name="site_logo_light"
                                                        placeholder="{{ __('Logo') }}"
                                                        accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                    <small class="text-muted">{{ __('Recommended size : 200 x 90') }}</small>
                                                </div>
                                            </div>

                                            {{-- Favicon --}}
                                            <div class="col-xl-4">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('Favicon') }}</div>
                                                    <input type="file" class="form-control" name="favi_icon"
                                                        placeholder="{{ __('Favicon') }}"
                                                        accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                    <small class="text-muted">
                                                        {{ __('Recommended size : 200 x 200') }}</small>
                                                </div>
                                            </div>

                                            {{-- App Name --}}
                                            <div class="col-xl-4">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('App Name') }}</label>
                                                    <input type="text" class="form-control" name="app_name"
                                                        value="{{ config('app.name') }}" maxlength="50"
                                                        placeholder="{{ __('App Name') }}">
                                                </div>
                                            </div>

                                            {{-- Site Name --}}
                                            <div class="col-xl-4">
                                                <div class="mb-3">
                                                    <label class="form-label required">{{ __('Site Name') }}</label>
                                                    <input type="text" class="form-control" name="site_name"
                                                        value="{{ $settings->site_name }}"
                                                        placeholder="{{ __('Site Name') }}" required>
                                                </div>
                                            </div>

                                            {{-- Update button --}}
                                            <div class="text-end bottom-fix">
                                                <div class="d-flex">
                                                    <button type="submit" class="btn btn-primary btn-md ms-auto">
                                                        {{ __('Update') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Update Subdomain Settings --}}
                        <div class="accordion-item">
                            <h4 class="accordion-header" id="heading-6">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-6" aria-expanded="false">
                                    <h2>{{ __('Subdomain (vCard and Store) Settings') }}</h2>
                                </button>
                            </h4>
                            <div id="collapse-6" class="accordion-collapse collapse"
                                data-bs-parent="#accordion-example">
                                <div class="accordion-body pt-0">
                                    <form action="{{ route('admin.change.subdomain.settings') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">

                                            {{-- Enable subdomain in vcard and store? --}}
                                            <div class="row">
                                                <h2 class="page-title my-3">
                                                    {{ __('Enable subdomain in vcard and store?') }}
                                                </h2>
                                                <div class="col-xl-6 col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label required" for="enable_subdomain">{{
                                                            __('Enable subdomain in vcard and store?') }}</label>
                                                        <select name="enable_subdomain" id="enable_subdomain" class="form-select" required>
                                                            <option value="1" {{ $config[46]->config_value == '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                            <option value="0" {{ $config[46]->config_value == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Update button --}}
                                            <div class="text-end bottom-fix">
                                                <div class="d-flex"> 
                                                    <button type="submit" class="btn btn-primary btn-md ms-auto">
                                                        {{ __('Update') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if ($allowedPermissions['maintenance_mode'])
                            {{-- Maintenance Mode --}}
                            <div class="accordion-item">
                                <h4 class="accordion-header" id="heading-7">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-7" aria-expanded="false">
                                        <h2>{{ __('Maintenance Mode Configuration Settings') }}</h2>
                                    </button>
                                </h4>
                                <div id="collapse-7" class="accordion-collapse collapse"
                                    data-bs-parent="#accordion-example">
                                    <div class="accordion-body pt-0">
                                        <form action="{{ route('admin.maintenance.toggle') }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                
                                                {{-- Maintenance Mode --}}
                                                <div class="col-xl-4 mb-2">
                                                    <div class="mb-3">
                                                        <label class="form-label required" for="maintenance_mode">
                                                            {{ __('Do you want to enable maintenance mode?') }}
                                                        </label>
                                                        <select name="maintenance_mode" id="maintenance_mode" class="form-select" required>
                                                            <option value="1" {{ app()->isDownForMaintenance() ? 'selected' : '' }}>
                                                                {{ __('Yes') }}
                                                            </option>
                                                            <option value="0" {{ app()->isDownForMaintenance() ? '' : 'selected' }}>
                                                                {{ __('No') }}
                                                            </option>
                                                        </select>
                                                        {{-- Message --}}
                                                        @if (app()->isDownForMaintenance())
                                                            <small class="text-muted fw-bold">{{ __('The site is currently in maintenance mode.') }}</small>
                                                        @else
                                                            <small class="text-muted fw-bold">{{ __('The site is currently not in maintenance mode.') }}</small>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Update button --}}
                                                <div class="text-end bottom-fix">
                                                    <div class="d-flex">
                                                        <button type="submit" class="btn btn-primary btn-md ms-auto">
                                                            {{ __('Update') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
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
function validatePort(input) {
    "use strict";
    
    const maxLength = 5; // Set your desired max length
    if (input.value.length > maxLength) {
    input.value = input.value.slice(0, maxLength);
    }
}
</script>
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
</script>
<script>
    // Array of element IDs
    var elementSelectors = ['show_website', 'registration_page', 'show_home_slider', 'timezone', 'languages', 'date_time_format', 'default_language', 'currency', 'currency_format', 'term', 'cookie', 'show_whatsapp_chatbot', 'paypal_mode', 'toyyibpay_mode', 'paddle_environment', 'paytr_mode', 'recaptcha_enable', 'google_auth_enable', 'mail_encryption', 'disable_user_email_verification', 'enable_subdomain', 'maintenance_mode'];
    
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
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
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