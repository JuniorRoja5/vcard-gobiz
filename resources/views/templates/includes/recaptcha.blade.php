@php
// Queries
use App\Setting;
use Illuminate\Support\Facades\DB;

$config = DB::table('config')->get();
$settings = Setting::first();

$recaptcha_configuration = [
    'RECAPTCHA_ENABLE' => env('RECAPTCHA_ENABLE', ''),
    'RECAPTCHA_SITE_KEY' => env('RECAPTCHA_SITE_KEY', ''),
    'RECAPTCHA_SECRET_KEY' => env('RECAPTCHA_SECRET_KEY', '')
];

$settings['recaptcha_configuration'] = $recaptcha_configuration;
@endphp

{{-- Check Recaptcha --}}
@if (env('RECAPTCHA_ENABLE') == 'on')
    {!! htmlScriptTagJsApi() !!}
@endif

{{-- ReCaptcha --}}
@if ($settings->recaptcha_configuration['RECAPTCHA_ENABLE'] == 'on')
<div class="w-full p-2">
    {!! htmlFormSnippet() !!}
</div> 
@endif