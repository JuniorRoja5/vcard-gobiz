<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebToolsController;
use App\Http\Controllers\SubdomainController;
use App\Http\Controllers\User\CardController;
use App\Http\Controllers\Admin\DemoController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\User\MediaController;
use App\Http\Controllers\User\StoreController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\ReadNfcCardController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\PluginController;
use App\Http\Controllers\Admin\PusherController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\CustomDomainController;
use App\Http\Controllers\User\BillingController;
use App\Http\Controllers\User\InquiryController;
use App\Http\Controllers\User\PreviewController;
use App\Http\Controllers\User\VisitorController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CouponsController;
use App\Http\Controllers\Admin\CronJobController;
use App\Http\Controllers\Admin\MailgunController;
use App\Http\Controllers\Admin\SitemapController;
use App\Http\Controllers\Payment\PaytrController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\CheckOutController;
use App\Http\Controllers\User\EditCardController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PusherNotification;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Payment\MollieController;
use App\Http\Controllers\Payment\PaddleController;
use App\Http\Controllers\Payment\PaypalController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\Payment\XenditController;
use App\Http\Controllers\User\DuplicateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BookAppointmentController;
use App\Http\Controllers\Payment\OfflineController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\PhonepeController;
use App\Http\Controllers\User\AdditionalController;
use App\Http\Controllers\Admin\TaxSettingController;
use App\Http\Controllers\Payment\CashfreeController;
use App\Http\Controllers\Payment\PaystackController;
use App\Http\Controllers\Payment\RazorpayController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Payment\ToyyibpayController;
use App\Http\Controllers\User\OrderNfcCardController;
use App\Http\Controllers\User\VerificationController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\NfcCardOrderController;
use App\Http\Controllers\Admin\TransactionsController;
use App\Http\Controllers\User\ConnectDomainController;
use App\Http\Controllers\User\ManageNfcCardController;
use App\Http\Controllers\User\VerifiedEmailController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\GoogleSettingController;
use App\Http\Controllers\Admin\NfcCardDesignController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Payment\FlutterwaveController;
use App\Http\Controllers\Payment\MercadoPagoController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\User\ActivateNfcCardController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\Admin\SubdomainSettingController;
use App\Http\Controllers\Admin\SupportActivatorController;
use App\Http\Controllers\Admin\BlogController as AdminBlog;
use App\Http\Controllers\Admin\MarketingCustomerController;
use App\Http\Controllers\User\ManageOrderNfcCardController;
use App\Http\Controllers\User\TransactionNfcCardController;
use App\Http\Controllers\User\Vcard\Create\PopUpController;
use App\Http\Controllers\User\Vcard\Create\CreateController;
use App\Http\Controllers\User\Vcard\Create\GalleryController;
use App\Http\Controllers\User\Vcard\Create\ProductController;
use App\Http\Controllers\User\Vcard\Create\ServiceController;
use App\Http\Controllers\Admin\CustomDomainRequestsController;
use App\Http\Controllers\Admin\NfcCardKeyGenerationController;
use App\Http\Controllers\User\AccountController as userAccount;
use App\Http\Controllers\Admin\ApplicationHealthCheckController;
use App\Http\Controllers\User\Vcard\Create\SocialLinkController;
use Plugins\ReferralSystem\Controllers\ReferralSystemController;
use App\Http\Controllers\Admin\NfcCardOrderTransactionController;
use App\Http\Controllers\User\Vcard\Create\AppointmentController;
use App\Http\Controllers\User\Vcard\Create\ContactFormController;
use App\Http\Controllers\User\Vcard\Create\PaymentLinkController;
use App\Http\Controllers\User\Vcard\Create\TestimonialController;
use App\Http\Controllers\User\Vcard\Create\BusinessHourController;
use App\Http\Controllers\Admin\EnableDisableNFCCardOrderController;
use App\Http\Controllers\Admin\ReferralWithdrawalRequestController;
use App\Http\Controllers\User\DashboardController as userDashboard;
use App\Http\Controllers\User\PlanController as UserPlanController;
use App\Http\Controllers\User\CustomDomainCloudflareRulesController;
use App\Http\Controllers\Admin\ReferralSystemConfigurationController;
use App\Http\Controllers\User\Vcard\Create\AdvancedSettingController;
use App\Http\Controllers\User\TransactionsController as userTransactions;
use App\Http\Controllers\Payment\NFC\PaytrController as NFCPaytrController;
use App\Http\Controllers\User\ReferralController as UserReferralController;
use App\Http\Controllers\Payment\NFC\MollieController as NFCMollieController;
use App\Http\Controllers\Payment\NFC\PaddleController as NFCPaddleController;
use App\Http\Controllers\Payment\NFC\PaypalController as NFCPaypalController;
use App\Http\Controllers\Payment\NFC\StripeController as NFCStripeController;
use App\Http\Controllers\Payment\NFC\XenditController as NFCXenditController;
use App\Http\Controllers\Payment\NFC\OfflineController as NFCOfflineController;
use App\Http\Controllers\Payment\NFC\PhonepeController as NFCPhonepeController;
use App\Http\Controllers\User\NewsletterController as UserNewsletterController;
use App\Http\Controllers\User\Vcard\Edit\PopUpController as EditPopUpController;
use App\Http\Controllers\Payment\NFC\CashfreeController as NFCCashfreeController;
use App\Http\Controllers\Payment\NFC\PaystackController as NFCPaystackController;
use App\Http\Controllers\Payment\NFC\RazorpayController as NFCRazorpayController;
use App\Http\Controllers\Payment\NFC\ToyyibpayController as NFCToyyibpayController;
use App\Http\Controllers\User\AppointmentController as BookedAppointmentController;
use App\Http\Controllers\User\Store\Edit\UpdateController as UpdateStoreController;
use App\Http\Controllers\User\Vcard\Edit\GalleryController as EditGalleryController;
use App\Http\Controllers\User\Vcard\Edit\ProductController as EditProductController;
use App\Http\Controllers\User\Vcard\Edit\ServiceController as EditServiceController;
use App\Http\Controllers\User\Store\Create\CreateController as CreateStoreController;
use App\Http\Controllers\Payment\NFC\FlutterwaveController as NFCFlutterwaveController;
use App\Http\Controllers\Payment\NFC\MercadoPagoController as NFCMercadoPagoController;
use App\Http\Controllers\Payment\NFC\PaymentController as OrderNfcCardPaymentController;
use App\Http\Controllers\User\Vcard\Edit\SocialLinkController as EditSocialLinkController;
use App\Http\Controllers\User\Store\Edit\ProductController as UpdateStoreProductController;
use App\Http\Controllers\User\Vcard\Edit\AppointmentController as EditAppointmentController;
use App\Http\Controllers\User\Vcard\Edit\ContactFormController as EditContactFormController;
use App\Http\Controllers\User\Vcard\Edit\PaymentLinkController as EditPaymentLinkController;
use App\Http\Controllers\User\Vcard\Edit\TestimonialController as EditTestimonialController;
use App\Http\Controllers\User\Store\Create\ProductController as CreateStoreProductController;
use App\Http\Controllers\User\Vcard\Edit\BusinessHourController as EditBusinessHourController;
use App\Http\Controllers\User\Vcard\Edit\AdvancedSettingController as EditAdvancedSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Installer Middleware
Route::group(['middleware' => 'Installer'], function () {
    // Subdomain Route
    Route::domain('{cardname}.' . env('MAIN_DOMAIN', env('APP_URL')))->group(function () {
        Route::get('/', [SubdomainController::class, 'subdomainProfile'])->name('subdomain.profile')->middleware('scriptsanitizer');
    });

    // Custom Domain Route
    Route::domain('{domain}')->group(function () {
        Route::get('/', [CustomDomainController::class, 'customDomain'])->name('customdomain.profile')->middleware('scriptsanitizer');
    });

    // Path to plugins directory
    $pluginsPath = base_path('plugins');

    if (File::exists($pluginsPath)) {
        foreach (File::directories($pluginsPath) as $plugin) {
            $routeFile = $plugin . '/routes.php';
            if (File::exists($routeFile)) {
                require_once $routeFile;
            }
        }
    }

    Route::group(['middleware' => 'frame.destroyer'], function () {
        Route::get('/', [HomeController::class, 'index'])->name('home-locale');

        Auth::routes(['verify' => true]);

        // Pages
        Route::get('faq', [HomeController::class, 'faq'])->name('faq');
        Route::get('about-us', [HomeController::class, 'about'])->name('about');
        Route::get('contact-us', [HomeController::class, 'contact'])->name('contact');
        Route::get('support', [HomeController::class, 'support'])->name('support');
        Route::get('privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
        Route::get('terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('terms.and.conditions');
        Route::get('refund-policy', [HomeController::class, 'refundPolicy'])->name('refund.policy');

        Route::get('maintenance', [HomeController::class, 'maintenance'])->name('maintenance');

        // Custom pages
        Route::get('/p/{id}', [HomeController::class, "customPage"])->name("custom.page");

        // Blogs
        Route::get('/blogs', [BlogController::class, "blogs"])->name("blogs")->middleware('scriptsanitizer');
        Route::get('/blog/{slug}', [BlogController::class, "viewBlog"])->name("view.blog")->middleware('scriptsanitizer');

        // Blog post share
        Route::get('/blog/{slug}/share/facebook', [ShareController::class, "shareToFacebook"])->name("sharetofacebook");
        Route::get('/blog/{slug}/share/twitter', [ShareController::class, "shareToTwitter"])->name("sharetotwitter");
        Route::get('/blog/{slug}/share/linkedin', [ShareController::class, "shareToLinkedIn"])->name("sharetolinkedin");
        Route::get('/blog/{slug}/share/instagram', [ShareController::class, "shareToInstagram"])->name("sharetoinstagram");
        Route::get('/blog/{slug}/share/whatsapp', [ShareController::class, "shareToWhatsApp"])->name("sharetowhatsapp");

        // Web Tools
        // HTML
        Route::get('html-beautifier', [WebToolsController::class, 'htmlBeautifier'])->name('web.html.beautifier');
        Route::get('html-minifier', [WebToolsController::class, 'htmlMinifier'])->name('web.html.minifier');

        // CSS
        Route::get('css-beautifier', [WebToolsController::class, 'cssBeautifier'])->name('web.css.beautifier');
        Route::get('css-minifier', [WebToolsController::class, 'cssMinifier'])->name('web.css.minifier');
        Route::post('css-minifier', [WebToolsController::class, 'resultCssMinifier'])->name('web.result.css.minifier');

        // JS
        Route::get('js-beautifier', [WebToolsController::class, 'jsBeautifier'])->name('web.js.beautifier');
        Route::get('js-minifier', [WebToolsController::class, 'jsMinifier'])->name('web.js.minifier');
        Route::post('js-minifier', [WebToolsController::class, 'resultjsMinifier'])->name('web.result.js.minifier');

        // Random Password Generator
        Route::get('random-password-generator', [WebToolsController::class, 'randomPasswordGenerator'])->name('web.random.password.generator');
        Route::post('random-password-generator', [WebToolsController::class, 'resultRandomPasswordGenerator'])->name('web.result.random.password.generator');

        // Bcrypt Password Generator
        Route::get('bcrypt-password-generator', [WebToolsController::class, 'bcryptPasswordGenerator'])->name('web.bcrypt.password.generator');
        Route::post('bcrypt-password-generator', [WebToolsController::class, 'resultBcryptPasswordGenerator'])->name('web.result.bcrypt.password.generator');

        // MD5 Password Generator
        Route::get('md5-password-generator', [WebToolsController::class, 'md5PasswordGenerator'])->name('web.md5.password.generator');
        Route::post('md5-password-generator', [WebToolsController::class, 'resultMd5PasswordGenerator'])->name('web.result.md5.password.generator');

        // Random Word Generator
        Route::get('random-word-generator', [WebToolsController::class, 'randomWordGenerator'])->name('web.random.word.generator');
        Route::post('random-word-generator', [WebToolsController::class, 'resultRandomWordGenerator'])->name('web.result.random.word.generator');

        // Text counter
        Route::get('text-counter', [WebToolsController::class, 'textCounter'])->name('web.text.counter');

        // Lorem Generator
        Route::get('lorem-generator', [WebToolsController::class, 'loremGenerator'])->name('web.lorem.generator');

        // Emojies
        Route::get('emojies', [WebToolsController::class, 'emojies'])->name('web.emojies');

        // DNS Lookup
        Route::get('dns-lookup', [WebToolsController::class, 'dnsLookup'])->name('web.dns.lookup');
        Route::post('dns-lookup', [WebToolsController::class, 'resultDnsLookup'])->name('web.result.dns.lookup');

        // IP Lookup
        Route::get('ip-lookup', [WebToolsController::class, 'ipLookup'])->name('web.ip.lookup');
        Route::post('ip-lookup', [WebToolsController::class, 'resultIpLookup'])->name('web.result.ip.lookup');

        // Whois Lookup
        Route::get('whois-lookup', [WebToolsController::class, 'whoisLookup'])->name('web.whois.lookup');
        Route::post('whois-lookup', [WebToolsController::class, 'resultWhoisLookup'])->name('web.result.whois.lookup');

        // QR code maker
        Route::get('qr-maker', [WebToolsController::class, 'qrMaker'])->name('web.qrcode');
    });

    Route::group(['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin', 'frame.destroyer', 'twofactor'], 'where' => ['locale' => '[a-zA-Z]{2}']], function () {
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Themes
        Route::get('themes', [ThemeController::class, 'themes'])->name('themes')->middleware('user.page.permission:themes');
        Route::get('active-themes', [ThemeController::class, 'activeThemes'])->name('active.themes')->middleware('user.page.permission:themes');
        Route::get('disabled-themes', [ThemeController::class, 'disabledThemes'])->name('disabled.themes')->middleware('user.page.permission:themes');
        Route::get('edit-theme/{id}', [ThemeController::class, 'editTheme'])->name('edit.theme')->middleware('user.page.permission:themes');
        Route::post('update-theme', [ThemeController::class, 'updateTheme'])->name('update.theme')->middleware(['user.page.permission:themes', 'demo.mode']);
        Route::get('update-theme-status', [ThemeController::class, 'updateThemeStatus'])->name('update.theme.status')->middleware(['user.page.permission:themes', 'demo.mode']);
        Route::get('search', [ThemeController::class, 'searchTheme'])->name('search.theme')->middleware('user.page.permission:themes');

        // Plans
        Route::get('plans', [PlanController::class, 'plans'])->name('plans')->middleware('user.page.permission:plans');
        Route::get('add-plan', [PlanController::class, 'addPlan'])->name('add.plan')->middleware('user.page.permission:plans');
        Route::post('save-plan', [PlanController::class, 'savePlan'])->name('save.plan')->middleware(['user.page.permission:plans', 'demo.mode']);
        Route::get('edit-plan/{id}', [PlanController::class, 'editPlan'])->name('edit.plan')->middleware('user.page.permission:plans');
        Route::post('update-plan', [PlanController::class, 'updatePlan'])->name('update.plan')->middleware(['user.page.permission:plans', 'demo.mode']);
        Route::get('status-plan', [PlanController::class, 'statusPlan'])->name('status.plan')->middleware(['user.page.permission:plans', 'demo.mode']);
        Route::get('delete-plan', [PlanController::class, 'deletePlan'])->name('delete.plan')->middleware(['user.page.permission:plans', 'demo.mode']);

        // Customers
        Route::get('customers', [CustomerController::class, 'customers'])->name('customers')->middleware('user.page.permission:customers');
        Route::get('edit-customer/{id}', [CustomerController::class, 'editCustomer'])->name('edit.customer')->middleware('user.page.permission:customers');
        Route::post('update-customer', [CustomerController::class, 'updateCustomer'])->name('update.customer')->middleware(['user.page.permission:customers', 'demo.mode']);
        Route::get('view-customer/{id}', [CustomerController::class, 'viewCustomer'])->name('view.customer')->middleware('user.page.permission:customers');
        Route::get('change-customer-plan/{id}', [CustomerController::class, 'ChangeCustomerPlan'])->name('change.customer.plan')->middleware('user.page.permission:customers');
        Route::post('update-customer-plan', [CustomerController::class, 'UpdateCustomerPlan'])->name('update.customer.plan')->middleware(['user.page.permission:customers', 'demo.mode']);
        Route::get('update-status', [CustomerController::class, 'updateStatus'])->name('update.status')->middleware(['user.page.permission:customers', 'demo.mode']);
        Route::get('delete-customer', [CustomerController::class, 'deleteCustomer'])->name('delete.customer')->middleware(['user.page.permission:customers', 'demo.mode']);
        Route::get('login-as/{id}', [CustomerController::class, 'authAs'])->name('login-as.customer')->middleware('user.page.permission:customers');

        // Duplicate card / store in one user account to another user account
        Route::post('assign-card', [CustomerController::class, 'assignCard'])->name('assign.card')->middleware('user.page.permission:customers');

        // Payment Gateways
        Route::get('payment-methods', [PaymentMethodController::class, 'paymentMethods'])->name('payment.methods')->middleware('user.page.permission:payment_methods');
        Route::get('add-payment-method', [PaymentMethodController::class, 'addPaymentMethod'])->name('add.payment.method')->middleware('user.page.permission:payment_methods');
        Route::post('save-payment-method', [PaymentMethodController::class, 'savePaymentMethod'])->name('save.payment.method')->middleware(['user.page.permission:payment_methods', 'demo.mode']);
        Route::get('edit-payment-method/{id}', [PaymentMethodController::class, 'editPaymentMethod'])->name('edit.payment.method')->middleware('user.page.permission:payment_methods');
        Route::post('update-payment-method', [PaymentMethodController::class, 'updatePaymentMethod'])->name('update.payment.method')->middleware(['user.page.permission:payment_methods', 'demo.mode']);
        Route::get('delete-payment-method', [PaymentMethodController::class, 'deletePaymentMethod'])->name('delete.payment.method')->middleware(['user.page.permission:payment_methods', 'demo.mode']);


        // Payment Configuration
        Route::get('configure-payment-method/{id}', [PaymentSettingController::class, 'configurePaymentMethod'])->name('configure.payment')->middleware('user.page.permission:payment_methods');
        Route::post('update-payment-configuration/{id}', [PaymentSettingController::class, 'updatePaymentConfiguration'])->name('update.payment.configuration')->middleware(['user.page.permission:payment_methods', 'demo.mode']);


        // Coupons
        Route::get('coupons', [CouponsController::class, 'indexCoupons'])->name('coupons')->middleware('user.page.permission:coupons');
        Route::get('create-coupon', [CouponsController::class, 'createCoupon'])->name('create.coupon')->middleware('user.page.permission:coupons');
        Route::post('store-coupon', [CouponsController::class, 'storeCoupon'])->name('store.coupon')->middleware(['user.page.permission:coupons', 'demo.mode']);
        Route::get('statistics-coupon/{id}', [CouponsController::class, 'statisticsCoupon'])->name('statistics.coupon')->middleware('user.page.permission:coupons');
        Route::get('edit-coupon/{id}', [CouponsController::class, 'editCoupon'])->name('edit.coupon')->middleware('user.page.permission:coupons');
        Route::post('update-coupon/{id}', [CouponsController::class, 'updateCoupon'])->name('update.coupon')->middleware(['user.page.permission:coupons', 'demo.mode']);
        Route::get('update-coupon-status', [CouponsController::class, 'updateCouponStatus'])->name('update.coupon.status')->middleware(['user.page.permission:coupons', 'demo.mode']);
        Route::get('delete-coupon', [CouponsController::class, 'deleteCoupon'])->name('delete.coupon')->middleware(['user.page.permission:coupons', 'demo.mode']);

        // Transactions
        Route::get('online/paid/transactions', [TransactionsController::class, 'onlinePaidTransactions'])->name('online.paid.transactions')->middleware('user.page.permission:transactions');
        Route::get('online/unpaid/transactions', [TransactionsController::class, 'onlineUnpaidTransactions'])->name('online.unpaid.transactions')->middleware('user.page.permission:transactions');
        Route::get('transaction-status/{id}/{status}', [TransactionsController::class, 'transactionStatus'])->name('trans.status')->middleware(['user.page.permission:transactions', 'demo.mode']);
        Route::get('offline/paid/transactions', [TransactionsController::class, 'offlinePaidTransactions'])->name('offline.paid.transactions')->middleware('user.page.permission:transactions');
        Route::get('offline/unpaid/transactions', [TransactionsController::class, 'offlineUnpaidTransactions'])->name('offline.unpaid.transactions')->middleware('user.page.permission:transactions');
        Route::get('offline-transaction-status/{id}/{status}', [TransactionsController::class, 'offlineTransactionStatus'])->name('offline.trans.status')->middleware(['user.page.permission:transactions', 'demo.mode']);
        Route::get('view-invoice/{id}', [TransactionsController::class, 'viewInvoice'])->name('view.invoice')->middleware('user.page.permission:transactions');

        // Users
        Route::get('users', [UserController::class, 'users'])->name('users')->middleware('user.page.permission:users');
        Route::get('create-user', [UserController::class, 'createUser'])->name('create.user')->middleware('user.page.permission:users');
        Route::post('save-user', [UserController::class, 'saveUser'])->name('save.user')->middleware(['user.page.permission:users', 'demo.mode']);
        Route::get('view-user/{id}', [UserController::class, 'viewUser'])->name('view.user')->middleware('user.page.permission:users');
        Route::get('edit-user/{id}', [UserController::class, 'editUser'])->name('edit.user')->middleware('user.page.permission:users');
        Route::post('update-user', [UserController::class, 'updateUser'])->name('update.user')->middleware(['user.page.permission:users', 'demo.mode']);
        Route::get('update-user-status', [UserController::class, 'updateUserStatus'])->name('update.user.status')->middleware(['user.page.permission:users', 'demo.mode']);
        Route::get('delete-user', [UserController::class, 'deleteUser'])->name('delete.user')->middleware(['user.page.permission:users', 'demo.mode']);
        Route::get('login-as-user/{id}', [UserController::class, 'authAsUser'])->name('login-as.user')->middleware('user.page.permission:users');

        // Custom domains
        Route::get('custom-domain-requests', [CustomDomainRequestsController::class, 'customDomainRequests'])->name('custom.domain.requests')->middleware('user.page.permission:custom_domain');
        Route::get('approved-custom-domain', [CustomDomainRequestsController::class, 'approvedCustomDomain'])->name('approved.custom.domain')->middleware(['user.page.permission:custom_domain', 'demo.mode']);
        Route::get('rejected-custom-domain', [CustomDomainRequestsController::class, 'rejectedCustomDomain'])->name('rejected.custom.domain')->middleware(['user.page.permission:custom_domain', 'demo.mode']);

        // Update custom domain status
        Route::get('process-custom-domain-requests', [CustomDomainRequestsController::class, 'processCustomDomainRequests'])->name('process.custom.domain.requests')->middleware('user.page.permission:custom_domain');
        Route::get('approved-custom-domain-requests', [CustomDomainRequestsController::class, 'approvedCustomDomainRequests'])->name('approved.custom.domain.requests')->middleware(['user.page.permission:custom_domain', 'demo.mode']);
        Route::get('rejected-custom-domain-requests', [CustomDomainRequestsController::class, 'rejectedCustomDomainRequests'])->name('rejected.custom.domain.requests')->middleware(['user.page.permission:custom_domain', 'demo.mode']);

        // Account Setting
        Route::get('account', [AccountController::class, 'account'])->name('account');
        Route::get('edit-account', [AccountController::class, 'editAccount'])->name('edit.account');
        Route::post('update-account', [AccountController::class, 'updateAccount'])->name('update.account')->middleware('demo.mode');
        Route::get('change-password', [AccountController::class, 'changePassword'])->name('change.password');
        Route::post('update-password', [AccountController::class, 'updatePassword'])->name('update.password')->middleware('demo.mode');

        // Change theme
        Route::get('theme/{id}', [AccountController::class, "changeTheme"])->name('change.theme');

        // Pages
        Route::get('pages', [PageController::class, "index"])->name('pages')->middleware('user.page.permission:pages');
        Route::get('custom-pages', [PageController::class, "customPagesIndex"])->name('custom.pages')->middleware('user.page.permission:pages');

        Route::get('add-page', [PageController::class, "addPage"])->name('add.page')->middleware('user.page.permission:pages');
        Route::post('save-page', [PageController::class, "savePage"])->name('save.page')->middleware(['user.page.permission:pages', 'demo.mode']);
        Route::get('custom-page/{id}', [PageController::class, "editCustomPage"])->name('edit.custom.page')->middleware('user.page.permission:pages');
        Route::post('custom-update-page', [PageController::class, "updateCustomPage"])->name('update.custom.page')->middleware(['user.page.permission:pages', 'demo.mode']);
        Route::get('status-page', [PageController::class, "statusPage"])->name('status.page')->middleware(['user.page.permission:pages', 'demo.mode']);
        Route::get('page/{id}', [PageController::class, "editPage"])->name('edit.page')->middleware('user.page.permission:pages');
        Route::post('update-page/{id}', [PageController::class, "updatePage"])->name('update.page')->middleware(['user.page.permission:pages', 'demo.mode']);
        Route::get('disable-page', [PageController::class, "disablePage"])->name('disable.page')->middleware(['user.page.permission:pages', 'demo.mode']);
        Route::get('delete-page', [PageController::class, "deletePage"])->name('delete.page')->middleware(['user.page.permission:pages', 'demo.mode']);

        // Blogs Categories
        Route::get('blog-categories', [BlogCategoryController::class, "index"])->name('blog.categories')->middleware('user.page.permission:blogs');
        Route::get('create-blog-category', [BlogCategoryController::class, "createBlogCategory"])->name('create.blog.category')->middleware('user.page.permission:blogs');
        Route::post('publish-blog-category', [BlogCategoryController::class, "publishBlogCategory"])->name('publish.blog.category')->middleware(['user.page.permission:blogs', 'demo.mode']);
        Route::get('edit-blog-category/{id}', [BlogCategoryController::class, "editBlogCategory"])->name('edit.blog.category')->middleware('user.page.permission:blogs');
        Route::post('update-blog-category/{id}', [BlogCategoryController::class, "updateBlogCategory"])->name('update.blog.category')->middleware(['user.page.permission:blogs', 'demo.mode']);
        Route::get('action-blog-category', [BlogCategoryController::class, "actionBlogCategory"])->name('action.blog.category')->middleware(['user.page.permission:blogs', 'demo.mode']);

        // Marketing Campaigns
        Route::get('marketing/campaigns', [CampaignController::class, 'index'])->name('marketing.campaigns')->middleware('user.page.permission:marketing');
        Route::get('marketing/campaigns/create', [CampaignController::class, 'createCampaign'])->name('marketing.campaigns.create')->middleware('user.page.permission:marketing');
        Route::post('marketing/campaigns/save', [CampaignController::class, 'saveCampaign'])->name('marketing.campaigns.save')->middleware(['user.page.permission:marketing', 'demo.mode']);
        Route::get('marketing/campaigns/recampaign', [CampaignController::class, 'recampaign'])->name('marketing.campaigns.recampaign')->middleware('user.page.permission:marketing');
        Route::post('marketing/campaigns/resend', [CampaignController::class, 'resendCampaign'])->name('marketing.campaigns.resend')->middleware(['user.page.permission:marketing', 'demo.mode']);
        Route::get('marketing/campaigns/status', [CampaignController::class, 'statusCampaign'])->name('marketing.campaigns.status')->middleware(['user.page.permission:marketing', 'demo.mode']);
        Route::get('marketing/campaigns/delete', [CampaignController::class, 'deleteCampaign'])->name('marketing.campaigns.delete')->middleware(['user.page.permission:marketing', 'demo.mode']);

        // Marketing Groups
        Route::get('marketing/groups', [GroupController::class, 'index'])->name('marketing.groups')->middleware('user.page.permission:marketing');
        Route::get('marketing/groups/create', [GroupController::class, 'createGroup'])->name('marketing.groups.create')->middleware('user.page.permission:marketing');
        Route::post('marketing/groups/save', [GroupController::class, 'saveGroup'])->name('marketing.groups.save')->middleware(['user.page.permission:marketing', 'demo.mode']);
        Route::get('marketing/groups/view/{id}', [GroupController::class, 'viewGroup'])->name('marketing.groups.view')->middleware('user.page.permission:marketing');
        Route::get('marketing/groups/edit/{id}', [GroupController::class, 'editGroup'])->name('marketing.groups.edit')->middleware('user.page.permission:marketing');
        Route::post('marketing/groups/update/{id}', [GroupController::class, 'updateGroup'])->name('marketing.groups.update')->middleware(['user.page.permission:marketing', 'demo.mode']);
        Route::get('marketing/groups/delete', [GroupController::class, 'deleteGroup'])->name('marketing.groups.delete')->middleware(['user.page.permission:marketing', 'demo.mode']);

        // Marketing Customers
        Route::get('marketing/customers', [MarketingCustomerController::class, 'index'])->name('marketing.customers')->middleware('user.page.permission:marketing');

        // Marketing MailGun configuration
        Route::get('marketing/mailgun', [MailgunController::class, 'index'])->name('marketing.mailgun')->middleware('user.page.permission:marketing');
        Route::post('marketing/mailgun/update', [MailgunController::class, 'update'])->name('marketing.mailgun.update')->middleware(['user.page.permission:marketing', 'demo.mode']);

        // Martketing Pusher Notification
        Route::get('marketing/pusher-notification', [PusherNotification::class, 'index'])->name('marketing.pusher.notification')->middleware('user.page.permission:marketing');
        Route::post('marketing/pusher-notification/send', [PusherNotification::class, 'send'])->name('marketing.pusher.notification.send')->middleware(['user.page.permission:marketing', 'demo.mode']);

        // Marketing Pusher configuration
        Route::get('marketing/pusher', [PusherController::class, 'index'])->name('marketing.pusher')->middleware('user.page.permission:marketing');
        Route::post('marketing/pusher/update', [PusherController::class, 'update'])->name('marketing.pusher.update')->middleware(['user.page.permission:marketing', 'demo.mode']);

        // NFC Card Design
        Route::get('nfc-card/designs', [NfcCardDesignController::class, "index"])->name('designs')->middleware('user.page.permission:nfc_card_design');
        Route::get('nfc-card/create-design', [NfcCardDesignController::class, "create"])->name('create.design')->middleware('user.page.permission:nfc_card_design');
        Route::post('nfc-card/save-design', [NfcCardDesignController::class, "store"])->name('save.design')->middleware(['user.page.permission:nfc_card_design', 'demo.mode']);
        Route::get('nfc-card/edit-design/{id}', [NfcCardDesignController::class, "edit"])->name('edit.design')->middleware('user.page.permission:nfc_card_design');
        Route::post('nfc-card/update-design', [NfcCardDesignController::class, "update"])->name('update.design')->middleware(['user.page.permission:nfc_card_design', 'demo.mode']);
        Route::get('nfc-card/action-design', [NfcCardDesignController::class, "action"])->name('action.design')->middleware(['user.page.permission:nfc_card_design', 'demo.mode']);

        // Manage NFC Card Orders
        Route::get('nfc-card/orders', [NfcCardOrderController::class, 'index'])->name('orders')->middleware('user.page.permission:nfc_card_orders');
        Route::get('nfc-card/order/{order}', [NfcCardOrderController::class, 'show'])->name('order.show')->middleware('user.page.permission:nfc_card_orders');
        Route::get('nfc-card/write-in-nfc-card/{order}', [NfcCardOrderController::class, 'writeToNfcCard'])->name('write.to.nfc.card')->middleware(['user.page.permission:nfc_card_orders', 'demo.mode']);
        Route::get('nfc-card/update-order/{order}', [NfcCardOrderController::class, 'updateOrder'])->name('update.order')->middleware(['user.page.permission:nfc_card_orders']);
        Route::post('nfc-card/updated', [NfcCardOrderController::class, 'updatedOrder'])->name('updated.order')->middleware(['user.page.permission:nfc_card_orders', 'demo.mode']);

        // Greeting Letter
        Route::get('nfc-card/greeting-letter/{order}', [NfcCardOrderController::class, 'greetingLetter'])->name('greeting.letter')->middleware(['user.page.permission:nfc_card_orders', 'demo.mode']);
        Route::post('nfc-card/update-greeting-letter/{order}', [NfcCardOrderController::class, 'updateGreetingLetter'])->name('update.greeting.letter')->middleware(['user.page.permission:nfc_card_orders', 'demo.mode']);

        // NFC Card Order Transaction
        Route::get('nfc-card/transactions', [NfcCardOrderTransactionController::class, 'index'])->name('transactions')->middleware('user.page.permission:nfc_card_order_transactions');
        Route::get('nfc-card/transaction/{transaction}', [NfcCardOrderTransactionController::class, 'show'])->name('transaction.show')->middleware('user.page.permission:nfc_card_order_transactions');
        Route::post('nfc-card/transaction/{transaction}', [NfcCardOrderTransactionController::class, 'update'])->name('transaction.update')->middleware(['user.page.permission:nfc_card_order_transactions', 'demo.mode']);
        Route::get('nfc-card/view-transaction/{transaction}', [NfcCardOrderTransactionController::class, 'view'])->name('view.transaction')->middleware(['user.page.permission:nfc_card_order_transactions', 'demo.mode']);
        Route::get('nfc-card/action-transaction', [NfcCardOrderTransactionController::class, 'action'])->name('action.transaction')->middleware(['user.page.permission:nfc_card_order_transactions', 'demo.mode']);

        // NFC Card Key Generation
        Route::get('nfc-card/key-generations', [NfcCardKeyGenerationController::class, 'index'])->name('key.generations')->middleware('user.page.permission:nfc_card_key_generations');
        Route::get('nfc-card/create-key-generation', [NfcCardKeyGenerationController::class, 'create'])->name('create.key.generation')->middleware('user.page.permission:nfc_card_key_generations');
        Route::post('nfc-card/save-key-generation', [NfcCardKeyGenerationController::class, 'store'])->name('save.key.generation')->middleware(['user.page.permission:nfc_card_key_generations', 'demo.mode']);
        Route::get('nfc-card/link-key/{id}', [NfcCardKeyGenerationController::class, 'link'])->name('link.key')->middleware(['user.page.permission:nfc_card_key_generations', 'demo.mode']);
        Route::post('nfc-card/update-link-key', [NfcCardKeyGenerationController::class, 'updateLinkKey'])->name('update.link.key')->middleware(['user.page.permission:nfc_card_key_generations', 'demo.mode']);
        Route::get('nfc-card/action-key-generation', [NfcCardKeyGenerationController::class, 'action'])->name('action.key.generation')->middleware(['user.page.permission:nfc_card_key_generations', 'demo.mode']);

        // Write to NFC Card (Key Generation)
        Route::get('nfc-card/key-write-to-nfc-card/{key}', [NfcCardKeyGenerationController::class, 'keyWriteToNfcCard'])->name('key.write.to.nfc.card')->middleware(['user.page.permission:nfc_card_orders', 'demo.mode']);

        // Greeting Letter (Key Generation)
        Route::get('nfc-card/key-greeting-letter/{key}', [NfcCardKeyGenerationController::class, 'keyGreetingLetter'])->name('key.greeting.letter')->middleware(['user.page.permission:nfc_card_orders', 'demo.mode']);
        Route::post('nfc-card/update-key-greeting-letter/{key}', [NfcCardKeyGenerationController::class, 'updateKeyGreetingLetter'])->name('update.key.greeting.letter')->middleware(['user.page.permission:nfc_card_orders', 'demo.mode']);

        // Get customer cards
        Route::post('get-customer-cards', [NfcCardKeyGenerationController::class, 'getCustomerCards'])->name('get.customer.cards')->middleware(['user.page.permission:nfc_card_key_generations', 'demo.mode']);

        // Email Templates (Edit & Update)
        Route::get('email-templates/{id}', [EmailTemplateController::class, 'emailTemplatesIndex'])->name('email.templates.index')->middleware('user.page.permission:email_templates');
        Route::post('update-email-template-content', [EmailTemplateController::class, 'updateEmailTemplateContent'])->name('update.email.template.content')->middleware(['user.page.permission:email_templates', 'demo.mode']);

        // Plugins
        Route::get('plugins', [PluginController::class, 'index'])->name('plugins')->middleware('user.page.permission:plugins');
        Route::post('notify-me', [PluginController::class, 'notifyMe'])->name('notify.me')->middleware(['user.page.permission:plugins', 'demo.mode']);
        Route::delete('/plugins/{pluginName}', [PluginController::class, 'deletePlugin'])->name('plugins.delete')->middleware(['user.page.permission:plugins', 'demo.mode']);
        Route::post('plugin/upload', [PluginController::class, 'upload'])->name('plugin.upload')->middleware(['user.page.permission:plugins', 'demo.mode']);

        // Blogs
        Route::get('blogs', [AdminBlog::class, "index"])->name('blogs')->middleware('user.page.permission:blogs');
        Route::get('create-blog', [AdminBlog::class, "createBlog"])->name('create.blog')->middleware('user.page.permission:blogs');
        Route::post('publish-blog', [AdminBlog::class, "publishBlog"])->name('publish.blog')->middleware(['user.page.permission:blogs', 'demo.mode']);
        Route::get('edit-blog/{id}', [AdminBlog::class, "editBlog"])->name('edit.blog')->middleware('user.page.permission:blogs');
        Route::post('update-blog/{id}', [AdminBlog::class, "updateBlog"])->name('update.blog')->middleware(['user.page.permission:blogs', 'demo.mode']);
        Route::get('action-blog', [AdminBlog::class, "actionBlog"])->name('action.blog')->middleware(['user.page.permission:blogs', 'demo.mode']);

        // Referrals
        Route::get('referrals', [ReferralController::class, 'index'])->name('referrals')->middleware('user.page.permission:referral_system');

        // Referral Withdrawal request
        Route::get('referral-withdrawal-requests', [ReferralWithdrawalRequestController::class, "index"])->name('referral.withdrawal.request')->middleware('user.page.permission:referral_system');

        // Withdrawal request accepted / rejected / transfer
        Route::get('update-withdrawal-request-status', [ReferralWithdrawalRequestController::class, "updateWithdrawalRequestStatus"])->name('update.withdrawal.request.status')->middleware(['user.page.permission:referral_system', 'demo.mode']);

        // Withdrawal request transfer
        Route::post('transfer-withdrawal-request', [ReferralWithdrawalRequestController::class, "transfer"])->name('transfer.withdrawal.request')->middleware(['user.page.permission:referral_system', 'demo.mode']);

        // Referral system configuration
        Route::get('referral-system-configuration', [ReferralSystemConfigurationController::class, "referralSystemConfiguration"])->name('referral.system.configuration')->middleware('user.page.permission:referral_system');
        Route::post('update-referral-system-configuration', [ReferralSystemConfigurationController::class, "updateReferralSystemConfiguration"])->name('update.referral.system.configuration')->middleware(['user.page.permission:referral_system', 'demo.mode']);

        // Settings
        Route::get('settings', [SettingsController::class, 'settings'])->name('settings')->middleware('user.page.permission:general_settings');
        Route::post('change-general-settings', [SettingsController::class, "changeGeneralSettings"])->name('change.general.settings')->middleware(['user.page.permission:general_settings', 'demo.mode']);
        Route::post('change-website-settings', [WebsiteSettingController::class, "index"])->name('change.website.settings')->middleware(['user.page.permission:general_settings', 'demo.mode']);
        Route::post('change-google-settings', [GoogleSettingController::class, "index"])->name('change.google.settings')->middleware(['user.page.permission:general_settings', 'demo.mode']);
        Route::post('change-email-settings', [EmailSettingController::class, "index"])->name('change.email.settings')->middleware(['user.page.permission:general_settings', 'demo.mode']);
        Route::get('test-email', [EmailSettingController::class, 'testEmail'])->name('test.email')->middleware('user.page.permission:general_settings');
        Route::post('change-subdomain-settings', [SubdomainSettingController::class, "index"])->name('change.subdomain.settings')->middleware(['user.page.permission:general_settings', 'demo.mode']);
        Route::post('update-custom-script', [SettingsController::class, "updateCustomScript"])->name('update.custom.script')->middleware(['user.page.permission:general_settings', 'demo.mode']);

        // Maintenance Mode
        Route::get('site/maintenance', [MaintenanceController::class, 'siteMaintenance'])->name('site.maintenance')->middleware('user.page.permission:maintenance_mode');
        Route::post('site/maintenance/toggle', [MaintenanceController::class, 'maintenanceToggle'])->name('maintenance.toggle')->middleware(['user.page.permission:maintenance_mode', 'demo.mode']);

        // Demo Mode
        Route::get('site/demo', [DemoController::class, 'siteDemo'])->name('site.demo')->middleware('user.page.permission:demo_mode');
        Route::post('site/demo/toggle', [DemoController::class, 'demoToggle'])->name('demo.toggle')->middleware(['user.page.permission:demo_mode', 'demo.mode']);

        // Tax and email template settings
        Route::get('settings/tax-setting', [TaxSettingController::class, 'taxSetting'])->name('tax.setting')->middleware('user.page.permission:invoice_tax');
        Route::post('settings/update-tex-setting', [TaxSettingController::class, 'updateTaxSetting'])->name('update.tax.setting')->middleware(['user.page.permission:invoice_tax', 'demo.mode']);
        Route::post('settings/update-email-setting', [TaxSettingController::class, 'updateEmailSetting'])->name('update.email.setting')->middleware(['user.page.permission:invoice_tax', 'demo.mode']);

        // Settingup cron jobs
        Route::get('cron/cron-jobs', [CronJobController::class, 'index'])->name('cron.jobs')->middleware('user.page.permission:general_settings');
        Route::post('cron/cron-jobs/update', [CronJobController::class, 'update'])->name('update.cron.jobs')->middleware(['user.page.permission:general_settings', 'demo.mode']);

        // Test Reminder
        Route::get('cron/test-reminder', [CronJobController::class, 'testReminder'])->name('test.reminder')->middleware('user.page.permission:general_settings');

        // Set cronjob time
        Route::post('cron/set-cronjob-time', [CronJobController::class, 'setCronjobTime'])->name('set.cronjob.time')->middleware(['user.page.permission:general_settings', 'demo.mode']);

        // Clear cache
        Route::get('clear/cache', [SettingsController::class, 'clearCache'])->name('clear.cache')->middleware(['user.page.permission:general_settings', 'demo.mode']);

        // Generating a sitemap
        Route::get('sitemap', [SitemapController::class, 'index'])->name('sitemap')->middleware(['user.page.permission:sitemap']);
        Route::post('generate-sitemap', [SitemapController::class, 'generate'])->name('generate.sitemap')->middleware(['user.page.permission:sitemap', 'demo.mode']);

        // Backup
        Route::get('backups', [BackupController::class, 'index'])->name('backups')->middleware('user.page.permission:backup');
        Route::get('backups/get-database-backup', [BackupController::class, 'getDatabaseBackup'])->name('get.database.backup')->middleware('user.page.permission:backup');
        Route::get('backups/create-file-backup', [BackupController::class, 'createFileBackup'])->name('create.file.backup')->middleware(['user.page.permission:backup', 'demo.mode']);
        Route::get('backups/create-database-backup', [BackupController::class, 'createDatabaseBackup'])->name('create.database.backup')->middleware(['user.page.permission:backup', 'demo.mode']);
        Route::get('backups/restore-backup', [BackupController::class, 'restore'])->name('backup.restore')->middleware(['user.page.permission:backup', 'demo.mode']);
        Route::get('backups/download-backup', [BackupController::class, 'download'])->name('backup.download')->middleware(['user.page.permission:backup', 'demo.mode']);
        Route::get('backups/delete-backup', [BackupController::class, 'delete'])->name('backup.delete')->middleware(['user.page.permission:backup', 'demo.mode']);

        // Check update
        Route::get('check', [UpdateController::class, 'check'])->name('check')->middleware('user.page.permission:software_update');
        Route::post('check-update', [UpdateController::class, 'checkUpdate'])->name('check.update')->middleware(['user.page.permission:software_update', 'demo.mode']);
        Route::post('update-code', [UpdateController::class, 'updateCode'])->name('update.code')->middleware(['user.page.permission:software_update', 'demo.mode']);
    });

    Route::group(['as' => 'user.', 'prefix' => 'user', 'namespace' => 'User', 'middleware' => ['auth', 'user', 'frame.destroyer', 'twofactor'], 'where' => ['locale' => '[a-zA-Z]{2}']], function () {

        // Dashboard
        Route::get('dashboard', [userDashboard::class, 'index'])->name('dashboard');
        Route::get('calendar/appointments', [userDashboard::class, 'fetchAppointments'])->name('calendar.appointments');

        // Business Cards
        Route::get('cards', [CardController::class, 'index'])->name('cards');
        Route::get('card-status/{id}', [CardController::class, 'cardStatus'])->name('card.status');

        // Newsletter
        Route::get('newsletter/{id}', [UserNewsletterController::class, 'index'])->name('newsletter');

        // Connect with custom domain
        Route::get('connect-domain/{id}', [ConnectDomainController::class, 'connectDomain'])->name('connect.domain');
        Route::post('new-domain-request', [ConnectDomainController::class, 'newDomainRequest'])->name('new.domain.request');
        Route::get('unlink-domain', [ConnectDomainController::class, 'unlinkDomain'])->name('unlink.domain');

        // Choose Business type
        Route::get('choose-card-type', [CardController::class, 'chooseCardType'])->name('choose.card.type');

        // Create vcard
        Route::get('create-card', [CreateController::class, 'CreateCard'])->name('create.card');
        Route::post('save-business-card', [CreateController::class, 'saveBusinessCard'])->name('save.business.card');

        // Search theme
        Route::get('search', [CardController::class, 'searchTheme'])->name('search.theme');

        // Cropped image 
        Route::post('vcard-cropped-image', [CreateController::class, 'vcardCroppedImage'])->name('vcard.cropped.image');

        // Check link
        Route::post('check-link', [CreateController::class, 'checkLink'])->name('check.link');

        // Social Links (Create)
        Route::get('social-links/{id}', [SocialLinkController::class, 'socialLinks'])->name('social.links');
        Route::post('save-social-links/{id}', [SocialLinkController::class, 'saveSocialLinks'])->name('save.social.links');

        // Payment links (Create)
        Route::get('payment-links/{id}', [PaymentLinkController::class, 'paymentLinks'])->name('payment.links');
        Route::post('save-payment-links/{id}', [PaymentLinkController::class, 'savePaymentLinks'])->name('save.payment.links');

        // Vcard services (Create)
        Route::get('services/{id}', [ServiceController::class, 'services'])->name('services');
        Route::post('save-services/{id}', [ServiceController::class, 'saveServices'])->name('save.services');

        // Vcard products (Create)
        Route::get('vproducts/{id}', [ProductController::class, 'vProducts'])->name('vproducts');
        Route::post('save-vproducts/{id}', [ProductController::class, 'saveVProducts'])->name('save.vproducts');

        // Galleries (Create)
        Route::get('galleries/{id}', [GalleryController::class, 'galleries'])->name('galleries');
        Route::post('save-galleries/{id}', [GalleryController::class, 'saveGalleries'])->name('save.galleries');

        // Testimonials (Create)
        Route::get('testimonials/{id}', [TestimonialController::class, 'testimonials'])->name('testimonials');
        Route::post('save-testimonial/{id}', [TestimonialController::class, 'saveTestimonial'])->name('save.testimonial');

        // Popups (Create)
        Route::get('popups/{id}', [PopUpController::class, 'popups'])->name('popups');
        Route::post('save-popups/{id}', [PopUpController::class, 'savePopups'])->name('save.popups');

        // Business hours (Create)
        Route::get('business-hours/{id}', [BusinessHourController::class, 'businessHours'])->name('business.hours');
        Route::post('save-business-hours/{id}', [BusinessHourController::class, 'saveBusinessHours'])->name('save.business.hours');

        // Appointment
        Route::get('appointment/{id}', [AppointmentController::class, 'Appointment'])->name('appointment');
        Route::post('save-appointment/{id}', [AppointmentController::class, 'saveAppointment'])->name('save.appointment');

        // Contact form (Create)
        Route::get('contact-form/{id}', [ContactFormController::class, 'contactForm'])->name('contact.form');
        Route::post('save-contact-form/{id}', [ContactFormController::class, 'saveContactForm'])->name('save.contact.form');

        // Advanced settings (Create)
        Route::get('advanced-setting/{id}', [AdvancedSettingController::class, 'advancedSetting'])->name('advanced.setting');
        Route::post('save-advanced-setting/{id}', [AdvancedSettingController::class, 'saveAdvancedSetting'])->name('save.advanced.setting')->middleware('scriptsanitizer');

        // Inquiries
        Route::get('inquiries/{id}', [InquiryController::class, 'index'])->name('enquiries');

        // Visitors
        Route::get('visitors/{id}', [VisitorController::class, 'index'])->name('visitors');

        // Appointments
        Route::get('appointments/{id}', [BookedAppointmentController::class, 'bookedAppointments'])->name('appointments');
        Route::get('accept-appointment', [BookedAppointmentController::class, 'acceptAppointments'])->name('accept.appointment');
        Route::get('cancel-appointment', [BookedAppointmentController::class, 'cancelAppointments'])->name('cancel.appointment');
        Route::post('reschedule-appointment', [BookedAppointmentController::class, 'rescheduleAppointments'])->name('reschedule.appointment');
        Route::get('complete-appointment', [BookedAppointmentController::class, 'completeAppointments'])->name('complete.appointment');
        Route::get('add-my-google-calendar', [BookedAppointmentController::class, 'addMyGoogleCalendar'])->name('add.my.google.calendar');

        // Edit Business Card
        Route::get('edit-card/{id}', [EditCardController::class, 'editCard'])->name('edit.card');
        Route::post('update-business-card/{id}', [EditCardController::class, 'updateBusinessCard'])->name('update.business.card');

        // Edit Social Links
        Route::get('edit-social-links/{id}', [EditSocialLinkController::class, 'socialLinks'])->name('edit.social.links');
        Route::post('update-social-links/{id}', [EditSocialLinkController::class, 'updateSocialLinks'])->name('update.social.links');

        // Edit Payment Links
        Route::get('edit-payment-links/{id}', [EditPaymentLinkController::class, 'paymentLinks'])->name('edit.payment.links');
        Route::post('update-payment-links/{id}', [EditPaymentLinkController::class, 'updatePaymentLinks'])->name('update.payment.links');

        // Edit Service
        Route::get('edit-services/{id}', [EditServiceController::class, 'services'])->name('edit.services');
        Route::post('save-service', [EditServiceController::class, 'saveService'])->name('save.service');
        Route::post('update-service', [EditServiceController::class, 'updateService'])->name('update.service');
        Route::delete('delete-service/{id}', [EditServiceController::class, 'deleteService'])->name('delete.service');
        Route::get('get-service/{id}', [EditServiceController::class, 'getService']);

        // Edit Product
        Route::get('get-vproducts/{id}', [EditProductController::class, 'getVProducts']);
        Route::get('edit-vproducts/{id}', [EditProductController::class, 'vProducts'])->name('edit.vproducts');
        Route::post('save-vproduct', [EditProductController::class, 'saveVProduct'])->name('save.vproduct');
        Route::post('update-vproduct', [EditProductController::class, 'updateVProduct'])->name('update.vproduct');
        Route::delete('delete-vproduct/{id}', [EditProductController::class, 'deleteVProduct'])->name('delete.vproduct');

        // Edit Gallery
        Route::get('edit-galleries/{id}', [EditGalleryController::class, 'galleries'])->name('edit.galleries');
        Route::post('update-galleries/{id}', [EditGalleryController::class, 'updateGalleries'])->name('update.galleries');

        // Edit Testimonial
        Route::get('edit-testimonials/{id}', [EditTestimonialController::class, 'editTestimonials'])->name('edit.testimonials');
        Route::post('update-testimonial/{id}', [EditTestimonialController::class, 'updateTestimonial'])->name('update.testimonial');

        // Edit Popups
        Route::get('edit-popups/{id}', [EditPopUpController::class, 'popups'])->name('edit.popups');
        Route::post('update-popups/{id}', [EditPopUpController::class, 'updatePopups'])->name('update.popups');

        // Edit Business Hour
        Route::get('edit-business-hours/{id}', [EditBusinessHourController::class, 'businessHours'])->name('edit.business.hours');
        Route::post('update-business-hours/{id}', [EditBusinessHourController::class, 'updateBusinessHours'])->name('update.business.hours');

        // Edit Contact Form
        Route::get('edit-contact-form/{id}', [EditContactFormController::class, 'editContactForm'])->name('edit.contact.form');
        Route::post('update-contact-form/{id}', [EditContactFormController::class, 'updateContactForm'])->name('update.contact.form');

        // Edit Appointment
        Route::get('edit-appointment/{id}', [EditAppointmentController::class, 'editAppointment'])->name('edit.appointment');
        Route::post('update-appointment/{id}', [EditAppointmentController::class, 'updateAppointment'])->name('update.appointment');

        // Edit Advanced Settings
        Route::get('edit-advanced-setting/{id}', [EditAdvancedSettingController::class, 'editAdvancedSetting'])->name('edit.advanced.setting');
        Route::post('update-advanced-setting/{id}', [EditAdvancedSettingController::class, 'updateAdvancedSetting'])->name('update.advanced.setting');

        // Delete vcard
        Route::get('delete-card', [CardController::class, 'deleteCard'])->name('delete.card');

        // Business Stores
        Route::get('stores', [StoreController::class, 'index'])->name('stores');

        // Create store
        Route::get('create-store', [CreateStoreController::class, 'CreateStore'])->name('create.store');
        Route::post('save-store', [CreateStoreController::class, 'saveStore'])->name('save.store');

        // Cropped image 
        Route::post('store-cropped-images', [CreateStoreController::class, 'storeCroppedImage'])->name('store.cropped.images');

        // Create store products
        Route::get('products/{id}', [CreateStoreProductController::class, 'products'])->name('products');
        Route::post('save-products/{id}', [CreateStoreProductController::class, 'saveProducts'])->name('save.products');

        // Edit Store
        Route::get('edit-store/{id}', [UpdateStoreController::class, 'editStore'])->name('edit.store');
        Route::post('update-store/{id}', [UpdateStoreController::class, 'updateStore'])->name('update.store');

        // Edit Store products
        Route::get('get-products/{id}', [UpdateStoreProductController::class, 'getProducts']);
        Route::get('edit-products/{id}', [UpdateStoreProductController::class, 'editProducts'])->name('edit.products');
        Route::post('update-products/{id}', [UpdateStoreProductController::class, 'updateProducts'])->name('update.products');
        Route::post('save-product', [UpdateStoreProductController::class, 'saveProduct'])->name('save.product');
        Route::post('update-product', [UpdateStoreProductController::class, 'updateProduct'])->name('update.product');
        Route::delete('delete-product/{id}', [UpdateStoreProductController::class, 'deleteProduct'])->name('delete.product');

        // Delete store
        Route::get('delete-store', [StoreController::class, 'deleteStore'])->name('delete.store');

        // View Preview Business Card
        Route::get('view-preview/{id}', [PreviewController::class, 'index'])->name('view.preview');

        // Categories
        Route::get('categories', [CategoryController::class, 'categories'])->name('categories');
        Route::get('create-category', [CategoryController::class, "createCategory"])->name('create.category');
        Route::post('save-category', [CategoryController::class, "saveCategory"])->name('save.category');
        Route::get('edit-category/{id}', [CategoryController::class, 'editCategory'])->name('edit.category');
        Route::post('update-category', [CategoryController::class, 'updateCategory'])->name('update.category');
        Route::get('status-category', [CategoryController::class, 'statusCategory'])->name('status.category');
        Route::get('delete-category', [CategoryController::class, 'deleteCategory'])->name('delete.category');

        // Duplicate
        Route::get('duplicate', [DuplicateController::class, 'duplicate'])->name('duplicate');

        // Business Plans
        Route::get('plans', [UserPlanController::class, 'index'])->name('plans');

        // Order NFC Cards
        Route::get('nfc-cards/order', [OrderNfcCardController::class, 'index'])->name('order.nfc.cards');
        Route::get('nfc-cards/checkout/{id}', [OrderNfcCardController::class, 'nfcCardCheckout'])->name('order.nfc.card.checkout');

        // Choose payment gateway
        Route::post('nfc-cards/place-order/{id}', [OrderNfcCardPaymentController::class, 'placeOrder'])->name('order.nfc.card.place.order')->middleware('demo.mode');
        Route::post('nfc-cards/coupon/{id}', [OrderNfcCardPaymentController::class, 'coupon'])->name('order.nfc.card.checkout.coupon');

        // Manage NFC cards orders
        Route::get('orders', [ManageOrderNfcCardController::class, 'index'])->name('manage.nfc.orders');
        Route::get('order/{id}', [ManageOrderNfcCardController::class, 'viewOrder'])->name('order.nfc.card.view');
        Route::get('upload-nfc-card-logo/{id}', [ManageOrderNfcCardController::class, 'uploadNfcCardLogo'])->name('upload.nfc.card.logo');
        Route::post('update-nfc-card-logo/{id}', [ManageOrderNfcCardController::class, 'updateNfcCardLogo'])->name('update.nfc.card.logo')->middleware('demo.mode');

        // Manage NFC Cards
        Route::get('manage-nfc-cards', [ManageNfcCardController::class, 'index'])->name('manage.nfc.cards');
        Route::get('link-nfc-card/{id}', [ManageNfcCardController::class, 'linkNfcCard'])->name('link.nfc.card');
        Route::post('nfc-cards/update-link-key', [ManageNfcCardController::class, 'updateCardLink'])->name('update.card.link')->middleware('demo.mode');
        Route::get('nfc-cards/action-key-generation', [ManageNfcCardController::class, 'action'])->name('action.key.generation')->middleware('demo.mode');

        // Manage NFC cards transactions
        Route::get('nfc-cards/transactions', [TransactionNfcCardController::class, 'index'])->name('transaction.nfc.cards');
        Route::get('nfc-cards/transaction/{id}', [TransactionNfcCardController::class, 'viewTransaction'])->name('transaction.nfc.card.view');
        Route::get('nfc-cards/transaction/invoice/{id}', [TransactionNfcCardController::class, 'viewTransactionInvoice'])->name('transaction.nfc.card.view.invoice');

        // Active NFC Card
        Route::get('activate-nfc-card', [ActivateNfcCardController::class, 'index'])->name('activate.nfc.card');
        Route::post('activated-nfc-card', [ActivateNfcCardController::class, 'store'])->name('activated.nfc.card')->middleware('demo.mode');

        // Referral
        Route::get('referrals', [UserReferralController::class, 'index'])->name('referrals');
        Route::get('referrals/withdrawal-request', [UserReferralController::class, 'withdrawalRequest'])->name('referrals.withdrawal.request');

        // Update Bank Details
        Route::post('update-bank-details', [UserReferralController::class, 'updateBankDetails'])->name('update.bank.details');

        // New Withdrawal Request
        Route::get('new-withdrawal-request', [UserReferralController::class, 'newWithdrawalRequest'])->name('new.withdrawal.request');
        Route::post('save-withdrawal-request', [UserReferralController::class, 'saveWithdrawalRequest'])->name('save.withdrawal.request');

        // Media
        Route::get('media', [MediaController::class, 'media'])->name('media');
        Route::get('media-data', [MediaController::class, 'getMediaData'])->name('media.data');
        Route::get('add-media', [MediaController::class, 'addMedia'])->name('add.media');
        Route::post('upload-media', [MediaController::class, 'uploadMedia'])->name('upload.media');
        Route::get('delete-media', [MediaController::class, 'deleteMedia'])->name('media.delete');

        // Upload media images
        Route::post('multiple', [MediaController::class, 'multipleImages'])->name('multiple');

        //Addtional Tootls -> QR Maker
        Route::get('tools/qr-maker', [AdditionalController::class, 'qrMaker'])->name('qr-maker');
        Route::get('tools/whois-lookup', [AdditionalController::class, 'whoisLookup'])->name('whois-lookup');
        Route::post('tools/whois-lookup', [AdditionalController::class, 'resultWhoisLookup'])->name('result.whois-lookup');
        Route::get('tools/dns-lookup', [AdditionalController::class, 'dnsLookup'])->name('dns-lookup');
        Route::post('tools/dns-lookup', [AdditionalController::class, 'resultDnsLookup'])->name('result.dns-lookup');
        Route::get('tools/ip-lookup', [AdditionalController::class, 'ipLookup'])->name('ip-lookup');
        Route::post('tools/ip-lookup', [AdditionalController::class, 'resultIpLookup'])->name('result.ip-lookup');

        // Cloudflare custom domain rules
        Route::get('custom-domain-cloudflare-rules', [CustomDomainCloudflareRulesController::class, 'index'])->name('custom.domain.cloudflare.rules');

        // Transactions
        Route::get('transactions', [userTransactions::class, 'indexTransactions'])->name('transactions');
        Route::get('view-invoice/{id}', [userTransactions::class, 'viewInvoice'])->name('view.invoice');

        // Billing
        Route::get('billing/{id}', [BillingController::class, 'billing'])->name('billing');
        Route::post('update-billing', [BillingController::class, 'updateBilling'])->name('update.billing');

        // Checkout
        Route::get('checkout/{id}', [CheckOutController::class, 'index'])->name('checkout');
        Route::post('checkout-coupon/{id}', [CheckOutController::class, 'checkoutCoupon'])->name('checkout.coupon')->middleware(['demo.mode']);

        // Save Upgrade Plan
        Route::post('save-upgrade/{id}', [CardController::class, 'saveUpgrade'])->name('save.upgrade.plan')->middleware(['demo.mode']);

        // Resend Email Verfication
        Route::get('verify-email-verification', [VerificationController::class, "verifyEmailVerification"])->name('verify.email.verification');
        Route::get('resend-email-verification', [VerificationController::class, "resendEmailVerification"])->name('resend.email.verification');

        // Account Setting
        Route::get('account', [userAccount::class, 'account'])->name('account');
        Route::post('update-account', [userAccount::class, 'updateAccount'])->name('update.account')->middleware(['demo.mode']);
        Route::get('change-password', [userAccount::class, 'changePassword'])->name('change.password');
        Route::post('update-password', [userAccount::class, 'updatePassword'])->name('update.password')->middleware(['demo.mode']);
        Route::get('settings', [userAccount::class, 'settings'])->name('settings');
        Route::get('update-settings', [userAccount::class, 'updateSettings'])->name('update.settings')->middleware(['demo.mode']);

        // Change theme
        Route::get('theme/{id}', [AccountController::class, "changeTheme"])->name('change.theme');
    });

    // Choose Payment Gateway
    Route::post('/prepare-payment/{planId}', [PaymentController::class, 'preparePaymentGateway'])->name('prepare.payment.gateway')->middleware('demo.mode');

    // PayPal Payment Gateway
    Route::get('/payment-paypal/{planId}/{couponId}', [PaypalController::class, 'paywithpaypal'])->name('paywithpaypal');
    Route::get('/payment/status', [PaypalController::class, 'paypalPaymentStatus'])->name('paypalPaymentStatus');

    // RazorPay
    Route::get('payment-razorpay/{planId}/{couponId}', [RazorpayController::class, 'prepareRazorpay'])->name('paywithrazorpay');
    Route::get('razorpay-payment-status/{oid}/{paymentId}', [RazorpayController::class, 'razorpayPaymentStatus'])->name('razorpay.payment.status');

    // Phonepe
    Route::get('/payment-phonepe/{planId}/{couponId}', [PhonepeController::class, 'preparePhonpe'])->name('paywithphonepe');
    Route::any('/phonepe-payment-status', [PhonepeController::class, 'phonepePaymentStatus'])->name('phonepe.payment.status');

    // Stripe
    Route::get('/payment-stripe/{planId}/{couponId}', [StripeController::class, 'stripeCheckout'])->name('paywithstripe');
    Route::get('/stripe-payment-status/{paymentId}', [StripeController::class, 'stripePaymentStatus'])->name('stripe.payment.status');
    Route::get('/stripe-payment-cancel/{paymentId}', [StripeController::class, 'stripePaymentCancel'])->name('stripe.payment.cancel');

    // Paystack
    Route::get('/payment-paystack/{planId}/{couponId}', [PaystackController::class, "paystackCheckout"])->name('paywithpaystack');
    Route::get('/paystack-payment/callback', [PaystackController::class, 'paystackHandleGatewayCallback'])->name('paystack.handle.gateway.callback');

    // Mollie
    Route::get('/payment-mollie/{planId}/{couponId}', [MollieController::class, "prepareMollie"])->name('paywithmollie');
    Route::get('/mollie-payment-status', [MollieController::class, "molliePaymentStatus"])->name('mollie.payment.status');

    // Mercado Pago
    Route::get('/payment-mercadopago/{planId}/{couponId}', [MercadoPagoController::class, "prepareMercadoPago"])->name('paywithmercadopago');
    Route::get('/mercadopago-payment-status', [MercadoPagoController::class, "mercadoPagoPaymentStatus"])->name('mercadopago.payment.status');
    Route::get('/mercadopago-payment-failure', [MercadoPagoController::class, "mercadoPagoPaymentFailure"])->name('mercadopago.payment.failure');
    Route::get('/mercadopago-payment-pending', [MercadoPagoController::class, "mercadoPagoPaymentPending"])->name('mercadopago.payment.pending');
    Route::get('/mercadopago-callback', [MercadoPagoController::class, "mercadoPagoCallback"])->name('mercadopago.callback');

    // Toyyibpay
    Route::get('/payment-toyyibpay/{planId}/{couponId}', [ToyyibpayController::class, "prepareToyyibpay"])->name('prepare.toyyibpay');
    Route::get('/toyyibpay-payment-status', [ToyyibpayController::class, "toyyibpayPaymentStatus"])->name('toyyibpay.payment.status');
    Route::get('/toyyibpay-payment-success', [ToyyibpayController::class, 'toyyibpayPaymentSuccess'])->name('toyyibpay.payment.success');

    // Flutterwave
    Route::get('/payment-flutterwave/{planId}/{couponId}', [FlutterwaveController::class, "prepareFlutterwave"])->name('prepare.flutterwave');
    Route::get('/flutterwave-payment-status', [FlutterwaveController::class, "flutterwavePaymentStatus"])->name('flutterwave.payment.status');

    // Paddle
    Route::get('/paddle/generate-payment-link/{planId}/{couponId}', [PaddleController::class, 'generatePaymentLink'])->name('prepare.paddle');
    Route::get('/paddle-payment-status', [PaddleController::class, 'paddlePaymentStatus'])->name('paddle.payment.status');
    Route::get('/paddle-payment-webhook', [PaddleController::class, 'paddlePaymentWebhook'])->name('paddle.payment.webhook');

    // Paytr
    Route::get('/paytr/generate-payment-link/{planId}/{couponId}', [PaytrController::class, 'generatePaymentLink'])->name('prepare.paytr');
    Route::post('/paytr-payment-status', [PaytrController::class, 'paytrPaymentStatus'])->name('paytr.payment.status');
    Route::post('/paytr-payment-failure', [PaytrController::class, "paytrPaymentFailure"])->name('paytr.payment.failure');
    Route::post('/paytr-payment-webhook', [PaytrController::class, 'paytrPaymentWebhook'])->name('paytr.payment.webhook');

    // Xendit
    Route::get('/xendit/generate-payment-link/{planId}/{couponId}', [XenditController::class, 'generatePaymentLink'])->name('prepare.xendit');
    Route::get('/xendit-payment-status/{transactionId}', [XenditController::class, 'xenditPaymentStatus'])->name('xendit.payment.status');
    Route::get('/xendit-payment-failure/{transactionId}', [XenditController::class, "xenditPaymentFailure"])->name('xendit.payment.failure');
    Route::get('/xendit-payment-webhook', [XenditController::class, 'xenditPaymentWebhook'])->name('xendit.payment.webhook');

    // Cashfree
    Route::get('/cashfree/generate-payment-link/{planId}/{couponId}', [CashfreeController::class, 'generatePaymentLink'])->name('prepare.cashfree');
    Route::get('/cashfree-payment-status', [CashfreeController::class, 'cashfreePaymentStatus'])->name('cashfree.payment.status');

    // Offline
    Route::get('/payment-offline/{planId}/{couponId}', [OfflineController::class, 'offlineCheckout'])->name('paywithoffline');
    Route::post('/mark-offline-payment', [OfflineController::class, 'markOfflinePayment'])->name('mark.payment.payment');

    // NFC Card
    // PayPal Payment Gateway
    Route::get('nfc/payment-paypal/{nfcId}/{couponId}', [NFCPaypalController::class, 'nfcPaywithpaypal'])->name('nfcpaywithpaypal');
    Route::get('nfc/payment/status', [NFCPaypalController::class, 'nfcPaypalPaymentStatus'])->name('nfcpaypalPaymentStatus');

    // RazorPay
    Route::get('nfc/payment-razorpay/{nfcId}/{couponId}', [NFCRazorpayController::class, 'nfcPrepareRazorpay'])->name('nfcpaywithrazorpay');
    Route::get('nfc/razorpay-payment-status/{oid}/{paymentId}', [NFCRazorpayController::class, 'nfcRazorpayPaymentStatus'])->name('nfc.razorpay.payment.status');

    // Phonepe
    Route::get('nfc/payment-phonepe/{nfcId}/{couponId}', [NFCPhonepeController::class, 'nfcPreparePhonpe'])->name('nfcpaywithphonepe');
    Route::any('nfc/phonepe-payment-status', [NFCPhonepeController::class, 'nfcPhonepePaymentStatus'])->name('nfc.phonepe.payment.status');

    // Stripe
    Route::get('nfc/payment-stripe/{nfcId}/{couponId}', [NFCStripeController::class, 'nfcStripeCheckout'])->name('nfcpaywithstripe');
    Route::get('nfc/stripe-payment-status/{paymentId}', [NFCStripeController::class, 'nfcStripePaymentStatus'])->name('nfc.stripe.payment.status');
    Route::get('nfc/stripe-payment-cancel/{paymentId}', [NFCStripeController::class, 'nfcStripePaymentCancel'])->name('nfc.stripe.payment.cancel');

    // Paystack
    Route::get('nfc/payment-paystack/{nfcId}/{couponId}', [NFCPaystackController::class, "nfcPaystackCheckout"])->name('nfcpaywithpaystack');
    Route::get('nfc/paystack-payment/callback', [NFCPaystackController::class, 'nfcPaystackHandleGatewayCallback'])->name('nfc.paystack.handle.gateway.callback');

    // Mollie
    Route::get('nfc/payment-mollie/{nfcId}/{couponId}', [NFCMollieController::class, "nfcPrepareMollie"])->name('nfcpaywithmollie');
    Route::get('nfc/mollie-payment-status', [NFCMollieController::class, "nfcMolliePaymentStatus"])->name('nfc.mollie.payment.status');

    // Mercado Pago
    Route::get('nfc/payment-mercadopago/{nfcId}/{couponId}', [NFCMercadoPagoController::class, "nfcPrepareMercadoPago"])->name('nfcpaywithmercadopago');
    Route::get('nfc/mercadopago-payment-status', [NFCMercadoPagoController::class, "nfcMercadoPagoPaymentStatus"])->name('nfc.mercadopago.payment.status');
    Route::get('nfc/mercadopago-payment-failure', [NFCMercadoPagoController::class, "nfcMercadoPagoPaymentFailure"])->name('nfc.mercadopago.payment.failure');
    Route::get('nfc/mercadopago-payment-pending', [NFCMercadoPagoController::class, "nfcMercadoPagoPaymentPending"])->name('nfc.mercadopago.payment.pending');
    Route::get('nfc/mercadopago-callback', [NFCMercadoPagoController::class, "nfcMercadoPagoCallback"])->name('nfc.mercadopago.callback');

    // Toyyibpay
    Route::get('nfc/payment-toyyibpay/{nfcId}/{couponId}', [NFCToyyibpayController::class, "nfcPrepareToyyibpay"])->name('nfc.prepare.toyyibpay');
    Route::get('nfc/toyyibpay-payment-status', [NFCToyyibpayController::class, "nfcToyyibpayPaymentStatus"])->name('nfc.toyyibpay.payment.status');
    Route::get('nfc/toyyibpay-payment-success', [NFCToyyibpayController::class, 'nfcToyyibpayPaymentSuccess'])->name('nfc.toyyibpay.payment.success');

    // Flutterwave
    Route::get('nfc/payment-flutterwave/{nfcId}/{couponId}', [NFCFlutterwaveController::class, "nfcPrepareFlutterwave"])->name('nfc.prepare.flutterwave');
    Route::get('nfc/flutterwave-payment-status', [NFCFlutterwaveController::class, "nfcFlutterwavePaymentStatus"])->name('nfc.flutterwave.payment.status');

    // Paddle
    Route::get('nfc/paddle/generate-payment-link/{nfcId}/{couponId}', [NFCPaddleController::class, 'nfcGeneratePaymentLink'])->name('nfc.prepare.paddle');
    Route::get('nfc/paddle-payment-status', [NFCPaddleController::class, 'nfPaddlePaymentStatus'])->name('nfc.paddle.payment.status');
    Route::get('nfc/paddle-payment-webhook', [NFCPaddleController::class, 'nfPaddlePaymentWebhook'])->name('nfc.paddle.payment.webhook');

    // Paytr
    Route::get('nfc/paytr/generate-payment-link/{nfcId}/{couponId}', [NFCPaytrController::class, 'nfcGeneratePaymentLink'])->name('nfc.prepare.paytr');
    Route::post('nfc/paytr-payment-status', [NFCPaytrController::class, 'nfcPaytrPaymentStatus'])->name('nfc.paytr.payment.status');
    Route::post('nfc/paytr-payment-failure', [NFCPaytrController::class, "nfcPaytrPaymentFailure"])->name('nfc.paytr.payment.failure');
    Route::post('nfc/paytr-payment-webhook', [NFCPaytrController::class, 'nfcPaytrPaymentWebhook'])->name('nfc.paytr.payment.webhook');

    // Xendit
    Route::get('nfc/xendit/generate-payment-link/{nfcId}/{couponId}', [NFCXenditController::class, 'nfcGeneratePaymentLink'])->name('nfc.prepare.xendit');
    Route::get('nfc/xendit-payment-status/{transactionId}', [NFCXenditController::class, 'nfcXenditPaymentStatus'])->name('nfc.xendit.payment.status');
    Route::get('nfc/xendit-payment-failure/{transactionId}', [NFCXenditController::class, "nfcXenditPaymentFailure"])->name('nfc.xendit.payment.failure');
    Route::get('nfc/xendit-payment-webhook', [NFCXenditController::class, 'nfcXenditPaymentWebhook'])->name('nfc.xendit.payment.webhook');

    // Cashfree
    Route::get('nfc/cashfree/generate-payment-link/{nfcId}/{couponId}', [NFCCashfreeController::class, 'nfcGeneratePaymentLink'])->name('nfc.prepare.cashfree');
    Route::get('nfc/cashfree-payment-status', [NFCCashfreeController::class, 'nfcCashfreePaymentStatus'])->name('nfc.cashfree.payment.status');

    // Offline
    Route::get('nfc/payment-offline/{nfcId}/{couponId}', [NFCOfflineController::class, 'nfcOfflineCheckout'])->name('nfcpaywithoffline');
    Route::post('nfc/mark-offline-payment', [NFCOfflineController::class, 'nfcMarkOfflinePayment'])->name('nfc.mark.payment.payment');



    // Google Auth
    Route::get('/google-login', [LoginController::class, 'redirectToProvider'])->name('login.google');
    Route::get('/sign-in-with-google', [LoginController::class, 'handleProviderCallback']);

    // Profile
    Route::group(['middleware' => ['scriptsanitizer', 'redirect.subdomain']], function () {
        Route::get('{id}', [ProfileController::class, 'profile', 'ShareWidget'])->name('profile');
    });

    // Get day wise available time slots
    Route::post('get-available-time-slots', [BookAppointmentController::class, 'getAvailableTimeSlots'])->name('get.available.time.slots');

    // Save appointment
    Route::post('book-appointment', [BookAppointmentController::class, 'bookAppointment'])->name('book.appointment');

    Route::get('dynamic-card/{id}', [SubdomainController::class, 'dynamicCard'])->name('dynamic.card');

    Route::post('check-password/{id}', [ProfileController::class, 'checkPwd'])->name('check.pwd');

    Route::post('sent-enquiry', [ProfileController::class, 'sentEnquiry'])->name('sent.enquiry');

    Route::post('subscribe/newsletter', [NewsletterController::class, 'subscribe'])->name('subscribe.newsletter');

    Route::get('/download/{id}', [ProfileController::class, 'downloadVcard'])->name('download.vCard');

    Route::post('/set-locale', [ProfileController::class, 'setLocale'])->name('set.locale');

    // Read NFC Card
    Route::get('nfc/{id}', [ReadNfcCardController::class, 'readNfcCard'])->name('read.nfc.card');
});
