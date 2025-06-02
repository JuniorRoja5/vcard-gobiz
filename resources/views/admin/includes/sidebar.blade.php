@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use App\Transaction;
    use Carbon\Carbon;

    // Queries
    $config = DB::table('config')->get();

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

    // Get user paid transactions (Online)
    $onlinePaidTransactions = Transaction::where('payment_gateway_name', '!=', 'Offline')
        ->where('payment_status', 'SUCCESS')
        ->count();

    // Get user unpaid transactions (Offline)
    $onlineUnpaidTransactions = Transaction::where('payment_gateway_name', '!=', 'Offline')
        ->where('payment_status', '!=', 'SUCCESS')
        ->count();

    // Get user paid transactions (Offline)
    $offlinePaidTransactions = Transaction::where('payment_gateway_name', 'Offline')
        ->where('payment_status', 'SUCCESS')
        ->count();

    // Get user unpaid transactions (Offline)
    $offlineUnpaidTransactions = Transaction::where('payment_gateway_name', 'Offline')
        ->where('payment_status', '!=', 'SUCCESS')
        ->count();
@endphp

<!-- Sidebar -->
<aside class="navbar navbar-vertical navbar-expand-lg d-print-none" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('admin.dashboard') }}">
                @if (file_exists(public_path('img/logo-light.png')))
                    <img src="{{ asset('img/logo-light.png') }}" width="200" height="50"
                        alt="{{ $settings->site_name }}" class="navbar-brand-image custom-logo">
                @else
                    <img src="{{ $settings->site_logo }}" width="200" height="50" alt="{{ $settings->site_name }}"
                        class="navbar-brand-image custom-logo">
                @endif
            </a>
        </div>
        <div class="navbar-nav flex-row d-lg-none">
            {{-- Languages --}}
            @if (count(config('app.languages')) > 1)
                <div class="nav-item dropdown mx-2">
                    <div class="lang">
                        <select class="form-select small-btn" placeholder="{{ __('Select a language') }}"
                            id="selectLang">
                            @foreach (config('app.languages') as $langLocale => $langName)
                                <option value="{{ $langLocale }}"
                                    {{ app()->getLocale() == $langLocale ? 'selected' : '' }}>
                                    <strong>{{ $langName }}</strong>
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="img-rounded">
                        <img class="img-rounded"
                            src="{{ asset(auth::user()->profile_image == null ? 'profile.png' : auth::user()->profile_image) }}"
                            alt="{{ auth::user()->name }}">
                    </span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-muted">
                            {{ Auth::user()->role_id == 4 ? __('Manager') : __('Administrator') }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('admin.account') }}" class="dropdown-item">{{ __('Profile & account') }}</a>
                    {{-- Light / Dark Mode --}}
                    <a href="{{ route('admin.change.theme', 'dark') }}" class="dropdown-item hide-theme-dark"
                        data-bs-placement="bottom">
                        {{ __('Dark mode') }}
                    </a>
                    <a href="{{ route('admin.change.theme', 'light') }}" class="dropdown-item hide-theme-light"
                        data-bs-placement="bottom">
                        {{ __('Light mode') }}
                    </a>
                    <a href="{{ route('logout') }}" class="dropdown-item"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                    <form class="logout" id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav bg-dark m-0 ml-lg-auto p-3 p-lg-0 overflow-y-auto" style="z-index: 9999999 !important;">
                <li class="d-inline d-lg-none">
                    <button class="navbar-toggler float-right" type="button" data-bs-toggle="collapse"
                        data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </li>
                {{-- Dashboard --}}
                <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <polyline points="5 12 3 12 12 3 21 12 19 12" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            {{ __('Dashboard') }}
                        </span>
                    </a>
                </li>

                {{-- Themes --}}
                @if ($allowedPermissions['themes'])
                    <li
                        class="nav-item dropdown {{ request()->is('admin/themes') || request()->is('admin/active-themes') || request()->is('admin/disabled-themes') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                            role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-photo">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 8h.01" />
                                    <path
                                        d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                    <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                    <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Themes') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.themes') }}">
                                {{ __('All') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.active.themes') }}">
                                {{ __('Activated') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.disabled.themes') }}">
                                {{ __('Deactivated') }}
                            </a>
                        </div>
                    </li>
                @endif

                {{-- Plans --}}
                @if ($allowedPermissions['plans'])
                    <li
                        class="nav-item {{ request()->is('admin/plans') || request()->is('admin/add-plan') || request()->is('admin/edit-plan/*') || request()->is('admin/status-plan/*') || request()->is('admin/delete-plan') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.plans') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-coin">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path
                                        d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                    <path d="M12 7v10" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Plans') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Customers --}}
                @if ($allowedPermissions['customers'])
                    <li
                        class="nav-item {{ request()->is('admin/customers') || request()->is('admin/edit-customer/*') || request()->is('admin/update-customer/*') || request()->is('admin/view-customer/*') || request()->is('admin/change-customer-plan/*') || request()->is('admin/update-status/*') || request()->is('admin/delete-customer') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.customers') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Customers') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Custom domains --}}
                @if ($allowedPermissions['custom_domain'])
                    <li
                        class="nav-item dropdown {{ request()->is('admin/custom-domain-requests') || request()->is('admin/approved-custom-domain') || request()->is('admin/rejected-custom-domain') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                            role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-world-www">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M19.5 7a9 9 0 0 0 -7.5 -4a8.991 8.991 0 0 0 -7.484 4" />
                                    <path d="M11.5 3a16.989 16.989 0 0 0 -1.826 4" />
                                    <path d="M12.5 3a16.989 16.989 0 0 1 1.828 4" />
                                    <path d="M19.5 17a9 9 0 0 1 -7.5 4a8.991 8.991 0 0 1 -7.484 -4" />
                                    <path d="M11.5 21a16.989 16.989 0 0 1 -1.826 -4" />
                                    <path d="M12.5 21a16.989 16.989 0 0 0 1.828 -4" />
                                    <path d="M2 10l1 4l1.5 -4l1.5 4l1 -4" />
                                    <path d="M17 10l1 4l1.5 -4l1.5 4l1 -4" />
                                    <path d="M9.5 10l1 4l1.5 -4l1.5 4l1 -4" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Custom Domains') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.custom.domain.requests') }}">
                                {{ __('New requests') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.approved.custom.domain') }}">
                                {{ __('Approved requests') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.rejected.custom.domain') }}">
                                {{ __('Rejected requests') }}
                            </a>
                        </div>
                    </li>
                @endif

                {{-- NFC Card Design --}}
                @if ($config[76]->config_value == '1')
                    @if ($allowedPermissions['nfc_card_design'] || $allowedPermissions['nfc_card_orders'] || $allowedPermissions['nfc_card_order_transactions'] || $allowedPermissions['nfc_card_key_generations'])
                        <li
                            class="nav-item dropdown {{ request()->is('admin/nfc-card-design') || request()->is('admin/nfc-card*') || request()->is('admin/nfc-card-order-transactions') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-nfc">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M11 20a3 3 0 0 1 -3 -3v-11l5 5" />
                                        <path d="M13 4a3 3 0 0 1 3 3v11l-5 -5" />
                                        <path
                                            d="M4 4m0 3a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-10a3 3 0 0 1 -3 -3z" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">
                                    {{ __('NFC Cards') }}
                                </span>
                            </a>
                            <div class="dropdown-menu">
                                {{-- NFC Card Design --}}
                                @if ($allowedPermissions['nfc_card_design'])
                                    <a href="{{ route('admin.designs') }}" class="dropdown-item">{{ __('Designs') }}</a>
                                @endif

                                {{-- NFC Card Orders --}}
                                @if ($allowedPermissions['nfc_card_orders'])
                                    <a href="{{ route('admin.orders') }}"
                                        class="dropdown-item">{{ __('Manage Orders') }}</a>
                                @endif

                                {{-- Generate NFC Card Key --}}
                                @if ($allowedPermissions['nfc_card_key_generations'])
                                    <a href="{{ route('admin.key.generations') }}"
                                        class="dropdown-item">{{ __('Manage Keys') }}</a>
                                @endif

                                {{-- NFC Card Order Transaction --}}
                                @if ($allowedPermissions['nfc_card_order_transactions'])
                                    <a href="{{ route('admin.transactions') }}"
                                        class="dropdown-item">{{ __('Transactions') }}</a>
                                @endif
                            </div>
                        </li>
                    @endif
                @endif

                {{-- Referral System --}}
                @if ($config[80]->config_value == '1')
                    @if ($allowedPermissions['referral_system'])
                        <li
                            class="nav-item dropdown {{ request()->is('admin/referrals') || request()->is('admin/referral-withdrawal-requests') || request()->is('admin/referral-system-configuration') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-coin">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path
                                            d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                        <path d="M12 7v10" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">
                                    {{ __('Referral System') }}
                                </span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.referrals') }}">
                                    {{ __('Referrals') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.referral.withdrawal.request') }}">
                                    {{ __('Withdrawal Requests') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.referral.system.configuration') }}">
                                    {{ __('Configuration') }}
                                </a>
                            </div>
                        </li>
                    @endif
                @endif

                {{-- Plugins --}}
                @if ($allowedPermissions['plugins'])
                    <li
                        class="nav-item {{ request()->is('admin/plugins') || request()->is('admin/plugin*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.plugins') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-apps">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path
                                        d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path
                                        d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M14 7l6 0" />
                                    <path d="M17 4l0 6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Plugins') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Payment methods --}}
                @if ($allowedPermissions['payment_methods'])
                    <li
                        class="nav-item {{ request()->is('admin/payment-methods') || request()->is('admin/configure-payment-method*') || request()->is('admin/edit-payment-method/*') || request()->is('admin/delete-payment-method') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.payment.methods') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-building-bank" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="3" y1="21" x2="21" y2="21"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                    <polyline points="5 6 12 3 19 6"></polyline>
                                    <line x1="4" y1="10" x2="4" y2="21"></line>
                                    <line x1="20" y1="10" x2="20" y2="21"></line>
                                    <line x1="8" y1="14" x2="8" y2="17"></line>
                                    <line x1="12" y1="14" x2="12" y2="17"></line>
                                    <line x1="16" y1="14" x2="16" y2="17"></line>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Payment Methods') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Coupons --}}
                @if ($allowedPermissions['coupons'])
                    <li
                        class="nav-item {{ request()->is('admin/coupons') || request()->is('admin/create-coupon') || request()->is('admin/edit-coupon/*') || request()->is('admin/statistics-coupon/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.coupons') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-discount">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 15l6 -6" />
                                    <circle cx="9.5" cy="9.5" r=".5" fill="currentColor" />
                                    <circle cx="14.5" cy="14.5" r=".5" fill="currentColor" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Coupons') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Pages --}}
                @if ($allowedPermissions['pages'])
                    <li
                        class="nav-item {{ request()->is('admin/pages') || request()->is('admin/add-page') || request()->is('admin/page/*') || request()->is('admin/edit-page/*') || request()->is('admin/status-page/*') || request()->is('admin/disable-page/*') || request()->is('admin/delete-page') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.pages') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-html">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M13 16v-8l2 5l2 -5v8" />
                                    <path d="M1 16v-8" />
                                    <path d="M5 8v8" />
                                    <path d="M1 12h4" />
                                    <path d="M7 8h4" />
                                    <path d="M9 8v8" />
                                    <path d="M20 8v8h3" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Website') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Blogs --}}
                @if ($allowedPermissions['blogs'])
                    <li
                        class="nav-item dropdown {{ request()->is('admin/blogs') || request()->is('admin/blog-categories') || request()->is('admin/create-blog') || request()->is('admin/publish-blog') || request()->is('admin/edit-blog/*') || request()->is('admin/action-blog/*') || request()->is('admin/action-blog') || request()->is('admin/create-blog-category') || request()->is('admin/edit-blog-category/*') || request()->is('admin/action-blog-category/*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                            role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-article">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                    <path d="M7 8h10" />
                                    <path d="M7 12h10" />
                                    <path d="M7 16h10" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Blogs') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('admin.blogs') }}" class="dropdown-item">{{ __('Blogs') }}</a>
                            <a href="{{ route('admin.blog.categories') }}"
                                class="dropdown-item">{{ __('Categories') }}</a>
                        </div>
                    </li>
                @endif

                {{-- Users --}}
                @if ($allowedPermissions['users'])
                    <li
                        class="nav-item {{ request()->is('admin/users') || request()->is('admin/create-user') || request()->is('admin/view-user/*') || request()->is('admin/edit-user/*') || request()->is('admin/update-user/*') || request()->is('admin/update-user-status/*') || request()->is('admin/delete-user') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.users') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Users') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Transactions --}}
                @if ($allowedPermissions['transactions'])
                    <li
                        class="nav-item dropdown {{ request()->is('admin/online/*') || request()->is('admin/offline/*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-report-money">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    <path
                                        d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                    <path d="M14 11h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                                    <path d="M12 17v1m0 -8v1" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Transactions') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle" href="#online-transactions"
                                            data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                                            aria-expanded="false">{{ __('Online') }}</a>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('admin.online.paid.transactions') }}"
                                                class="dropdown-item">
                                                {{ __('Paid') }}
                                                <span
                                                    class="badge badge-sm bg-green-lt text-uppercase ms-auto">{{ __($onlinePaidTransactions) }}</span>
                                            </a>
                                            <a href="{{ route('admin.online.unpaid.transactions') }}"
                                                class="dropdown-item">
                                                {{ __('Unpaid') }}
                                                <span
                                                    class="badge badge-sm bg-red-lt text-uppercase ms-auto">{{ __($onlineUnpaidTransactions) }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle" href="#offline-transactions"
                                            data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                                            aria-expanded="false">{{ __('Offline') }}</a>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('admin.offline.paid.transactions') }}"
                                                class="dropdown-item">
                                                {{ __('Paid') }}
                                                <span
                                                    class="badge badge-sm bg-green-lt text-uppercase ms-auto">{{ __($offlinePaidTransactions) }}</span>
                                            </a>
                                            <a href="{{ route('admin.offline.unpaid.transactions') }}"
                                                class="dropdown-item">
                                                {{ __('Unpaid') }}
                                                <span
                                                    class="badge badge-sm bg-red-lt text-uppercase ms-auto">{{ __($offlineUnpaidTransactions) }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Translations --}}
                @if ($allowedPermissions['translations'])
                    <li
                        class="nav-item {{ request()->is('languages') || request()->is('languages/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ asset('languages') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-language">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 5h7" />
                                    <path d="M9 3v2c0 4.418 -2.239 8 -5 8" />
                                    <path d="M5 9c0 2.144 2.952 3.908 6.7 4" />
                                    <path d="M12 20l4 -9l4 9" />
                                    <path d="M19.1 18h-6.2" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Translations') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Marketing --}}
                @if ($allowedPermissions['marketing'])
                    <li
                        class="nav-item dropdown {{ request()->is('admin/marketing/campaigns') || request()->is('admin/marketing/campaigns/*') || request()->is('admin/marketing/groups') || request()->is('admin/marketing/groups/*') || request()->is('admin/marketing/customers') || request()->is('admin/marketing/customers/*') || request()->is('admin/marketing/mailgun') || request()->is('admin/marketing/mailgun/*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                            role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-mail-opened">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 9l9 6l9 -6l-9 -6l-9 6" />
                                    <path d="M21 9v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10" />
                                    <path d="M3 19l6 -6" />
                                    <path d="M15 13l6 6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Marketing') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.marketing.campaigns') }}">
                                {{ __('Campaigns') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.marketing.groups') }}">
                                {{ __('Groups') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.marketing.customers') }}">
                                {{ __('Customers') }}
                            </a>
                            {{-- Mailgun configuration --}}
                            <a class="dropdown-item" href="{{ route('admin.marketing.mailgun') }}">
                                {{ __('Mailgun Configuration') }}
                            </a>
                        </div>
                    </li>

                    {{-- Pusher Notification --}}
                    <li
                        class="nav-item dropdown {{ request()->is('admin/marketing/pusher-notification') || request()->is('admin/marketing/pusher-notification/*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                            role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 14l11 -11" />
                                    <path
                                        d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Web Notifications') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.marketing.pusher.notification') }}">
                                {{ __('Notification') }}
                            </a>
                            {{-- Pusher configuration --}}
                            <a class="dropdown-item" href="{{ route('admin.marketing.pusher') }}">
                                {{ __('Pusher Configurations') }}
                            </a>
                        </div>
                    </li>
                @endif

                {{-- Email Templates --}}
                @if ($allowedPermissions['email_templates'])
                    <li class="nav-item {{ request()->is('admin/email-templates/*') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ route('admin.email.templates.index', ['id' => '584922675196']) }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-mail-cog">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 19h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v5" />
                                    <path d="M3 7l9 6l9 -6" />
                                    <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M19.001 15.5v1.5" />
                                    <path d="M19.001 21v1.5" />
                                    <path d="M22.032 17.25l-1.299 .75" />
                                    <path d="M17.27 20l-1.3 .75" />
                                    <path d="M15.97 17.25l1.3 .75" />
                                    <path d="M20.733 20l1.3 .75" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Email Templates') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Settings --}}
                @if ($allowedPermissions['general_settings'] || $allowedPermissions['sitemap'] || $allowedPermissions['invoice_tax'])
                    <li
                        class="nav-item dropdown {{ request()->is('admin/settings') || request()->is('admin/settings/*') || request()->is('admin/cron/*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Settings') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if ($allowedPermissions['general_settings'])
                                        {{-- General Settings --}}
                                        <a href="{{ route('admin.settings') }}"
                                            class="dropdown-item">{{ __('General Settings') }}</a>
                                    @endif

                                    {{-- Cron Jobs --}}
                                    @if ($allowedPermissions['general_settings'])
                                        <a href="{{ route('admin.cron.jobs') }}"
                                            class="dropdown-item">{{ __('Cron Jobs') }}</a>
                                    @endif

                                    {{-- Sitemap --}}
                                    @if ($allowedPermissions['sitemap'])
                                        <a href="{{ route('admin.sitemap') }}"
                                            class="dropdown-item">{{ __('Generate Sitemap') }}</a>
                                    @endif

                                    {{-- Invoice & Tax --}}
                                    @if ($allowedPermissions['invoice_tax'])
                                        <a href="{{ route('admin.tax.setting') }}"
                                            class="dropdown-item">{{ __('Invoice & Tax') }}</a>
                                    @endif

                                    @if ($allowedPermissions['general_settings'])
                                        {{-- Clear Cache --}}
                                        <a href="{{ route('admin.clear.cache') }}"
                                            class="dropdown-item">{{ __('Clear Cache') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Backup --}}
                @if ($allowedPermissions['backup'])
                    <li
                        class="nav-item {{ request()->is('admin/backups') || request()->is('admin/backups/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.backups') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-zip">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M6 20.735a2 2 0 0 1 -1 -1.735v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-1" />
                                    <path
                                        d="M11 17a2 2 0 0 1 2 2v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1v-2a2 2 0 0 1 2 -2z" />
                                    <path d="M11 5l-1 0" />
                                    <path d="M13 7l-1 0" />
                                    <path d="M11 9l-1 0" />
                                    <path d="M13 11l-1 0" />
                                    <path d="M11 13l-1 0" />
                                    <path d="M13 15l-1 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Backups') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Software Update --}}
                @if ($allowedPermissions['software_update'])
                    {{-- Software Update --}}
                    <li class="nav-item {{ request()->is('admin/check') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.check') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-settings-up">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M12.501 20.93c-.866 .25 -1.914 -.166 -2.176 -1.247a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.074 .26 1.49 1.296 1.252 2.158" />
                                    <path d="M19 22v-6" />
                                    <path d="M22 19l-3 -3l-3 3" />
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Software Update') }}
                            </span>
                        </a>
                    </li>
                @endif

                {{-- My Account --}}
                <li class="nav-item {{ request()->is('admin/account') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.account') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            {{ __('My Account') }}
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
