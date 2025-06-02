<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Setting;
use App\Visitor;
use App\Category;
use App\Currency;
use Carbon\Carbon;
use App\Testimonial;
use App\StoreProduct;
use App\BusinessField;
use Jenssegers\Agent\Agent;
use App\CardAppointmentTime;
use Illuminate\Http\Request;
use App\Classes\ServiceWorker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Artesaos\SEOTools\Facades\OpenGraph;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Get the current domain or customdomain from the request
        $host = str_replace(['http://', 'https://', 'www.'], '', $request->getHost());

        if (env('MAIN_DOMAIN') == $host) {
            // Queries
            $config = DB::table('config')->get();
            $plans = Plan::where('status', 1)->where('is_private', '0')->get();

            if ($config[38]->config_value == "yes") {
                $homePage = DB::table('pages')->where('page_name', 'home')->get();
                $settings = Setting::where('status', 1)->first();
                $currency = Currency::where('iso_code', $config['1']->config_value)->first();

                // SEO setup
                $pageTitle = trans($homePage[0]->title);
                $pageDescription = trans($homePage[0]->description);

                SEOTools::setTitle($pageTitle);
                SEOTools::setDescription($pageDescription);

                SEOMeta::setTitle($pageTitle);
                SEOMeta::setDescription($pageDescription);
                SEOMeta::addMeta('article:section', ucfirst($homePage[0]->page_name) . ' - ' . $pageTitle, 'property');
                SEOMeta::addKeyword([trans($homePage[0]->keywords)]);

                OpenGraph::setTitle($pageTitle);
                OpenGraph::setDescription($pageDescription);
                OpenGraph::setUrl(URL::full());
                OpenGraph::addImage(['url' => asset($settings->site_logo), 'size' => 300]);

                JsonLd::setTitle($pageTitle);
                JsonLd::setDescription($pageDescription);
                JsonLd::addImage(asset($settings->site_logo));

                return view('website.index', compact('homePage', 'plans', 'settings', 'currency', 'config'));
            } else {
                return redirect('/login');
            }
        } else {
            // Get business details
            $card_details = DB::table('business_cards')->where('custom_domain', $host)->where('card_status', 'activated')->first();
            $currentUser = 0;

            // Check storage folder
            if (!File::isDirectory('storage')) {
                File::link(storage_path('app/public'), public_path('storage'));
            }

            if (isset($card_details)) {
                $currentUser = DB::table('users')->where('user_id', $card_details->user_id)->where('status', 1)->whereDate('plan_validity', '>=', Carbon::now())->count();
            }

            // Check if the host is the main domain (karthikeyan.com)
            if ($currentUser == 1 && $host === $card_details->custom_domain) {
                // Check whatsapp number exists
                $whatsAppNumberExists = BusinessField::where('card_id', $card_details->card_id)->where('type', 'wa')->exists();

                // Save visitor
                $clientIP = \Request::getClientIp(true);

                $agent = new Agent();
                $userAgent = $request->header('user_agent');
                $agent->setUserAgent($userAgent);

                // Device
                $device = $agent->device();
                if ($device == "" || $device == "0") {
                    $device = "Others";
                }

                // Language
                $language = "en";
                if ($agent->languages()) {
                    $language = $agent->languages()[0];
                }

                $visitor = new Visitor();
                $visitor->card_id = $card_details->card_url;
                $visitor->type = $card_details->card_type;
                $visitor->ip_address = $clientIP;
                $visitor->platform = $agent->platform();
                $visitor->device = $agent->device();
                $visitor->language = $language;
                $visitor->user_agent = $userAgent;
                $visitor->save();

                if (isset($card_details)) {
                    if ($card_details->card_type == "store") {
                        $enquiry_button = '#';

                        $business_card_details = DB::table('business_cards')->where('business_cards.card_id', $card_details->card_id)
                            ->join('users', 'business_cards.user_id', '=', 'users.user_id')
                            ->join('themes', 'business_cards.theme_id', '=', 'themes.theme_id')
                            ->select('business_cards.*', 'users.plan_details', 'themes.theme_code')
                            ->first();

                        if ($business_card_details) {

                            $products = StoreProduct::join('categories', 'store_products.category_id', '=', 'categories.category_id')
                                ->where('store_products.card_id', $card_details->card_id)
                                ->where('categories.user_id', $business_card_details->user_id)
                                ->where('store_products.product_status', 'instock')
                                ->where('categories.status', 1);

                            $products = $products->orderBy('store_products.id', 'desc');

                            if ($request->has('category')) {
                                $products->where('category_name', ucfirst($request->category));
                            }

                            $products = $products->paginate(12);

                            // Get categories
                            $getCategories = DB::table('store_products')->select('category_id')->groupBy('category_id')->where('card_id', $card_details->card_id)->where('user_id', $business_card_details->user_id);
                            $categories = Category::whereIn('category_id', $getCategories)->get();

                            $settings = Setting::where('status', 1)->first();
                            $config = DB::table('config')->get();

                            SEOTools::setTitle($business_card_details->title);
                            SEOTools::setDescription($business_card_details->sub_title);
                            SEOTools::addImages([url($business_card_details->profile)]);

                            SEOMeta::setTitle($business_card_details->title);
                            SEOMeta::setDescription($business_card_details->sub_title);
                            SEOMeta::addMeta('article:section', $business_card_details->title, 'property');
                            SEOMeta::addKeyword(["'" . $business_card_details->title . "'", "'" . $business_card_details->title . " vcard online'"]);

                            OpenGraph::setTitle($business_card_details->title);
                            OpenGraph::setDescription($business_card_details->sub_title);
                            OpenGraph::setUrl(url($business_card_details->card_url));
                            OpenGraph::addImage([url($business_card_details->profile)]);

                            JsonLd::setTitle($business_card_details->title);
                            JsonLd::setDescription($business_card_details->sub_title);
                            JsonLd::addImage([url($business_card_details->profile)]);

                            // PWA
                            $icons = [
                                '512x512' => [
                                    'path' => url($business_card_details->profile),
                                    'purpose' => 'any'
                                ]
                            ];

                            $splash = [
                                '640x1136' => url($business_card_details->profile),
                                '750x1334' => url($business_card_details->profile),
                                '828x1792' => url($business_card_details->profile),
                                '1125x2436' => url($business_card_details->profile),
                                '1242x2208' => url($business_card_details->profile),
                                '1242x2688' => url($business_card_details->profile),
                                '1536x2048' => url($business_card_details->profile),
                                '1668x2224' => url($business_card_details->profile),
                                '1668x2388' => url($business_card_details->profile),
                                '2048x2732' => url($business_card_details->profile),
                            ];

                            // Card URL
                            $customdomainURL = "https://www." . $host;

                            $shortcuts = [
                                [
                                    'name' => $business_card_details->title,
                                    'description' => $business_card_details->sub_title,
                                    'url' =>  $customdomainURL,
                                    'icons' => [
                                        "src" => url($business_card_details->profile),
                                        "purpose" => "any"
                                    ]
                                ]
                            ];

                            $startUrl = $customdomainURL;

                            $fill = [
                                "name" => $business_card_details->title,
                                "short_name" => $business_card_details->title,
                                "start_url" =>  $startUrl,
                                "theme_color" => "#ffffff",
                                "icons" => $icons,
                                "splash" => $splash,
                                "shortcuts" => $shortcuts,
                            ];

                            $out = $this->generateNew($fill);

                            Storage::disk('public')->put("manifest/cd-" . $business_card_details->card_id . '.json', json_encode($out));

                            $manifest = url("storage/manifest/cd-" . $business_card_details->card_id . '.json');

                            // Generate service worker
                            $generateServiceWorker = new ServiceWorker();
                            $generateServiceWorker->generateServiceWorker($business_card_details->card_id, $business_card_details->card_url);

                            $plan_details = json_decode($business_card_details->plan_details, true);
                            $store_details = json_decode($business_card_details->description, true);

                            if ($store_details['whatsapp_no'] != null) {
                                $enquiry_button = $store_details['whatsapp_no'];
                            }

                            $whatsapp_msg = $store_details['whatsapp_msg'];
                            $currency = $store_details['currency'];

                            $url = $host;
                            $business_name = $card_details->title;
                            $profile = $business_card_details->cover ? url($business_card_details->cover) : asset('images/default-cover.png');

                            $shareContent = $config[30]->config_value;
                            $shareContent = str_replace("{ business_name }", $business_name, $shareContent);
                            $shareContent = str_replace("{ business_url }", $url, $shareContent);
                            $shareContent = str_replace("{ appName }", $config[0]->config_value, $shareContent);

                            // If branding enabled, then show app name.
                            if ($plan_details['hide_branding'] == "1") {
                                $shareContent = str_replace("{ appName }", $business_name, $shareContent);
                            } else {
                                $shareContent = str_replace("{ appName }", $config[0]->config_value, $shareContent);
                            }

                            $url = urlencode($url);
                            $shareContent = urlencode($shareContent);

                            // Session::put('locale', strtolower($business_card_details->card_lang));
                            app()->setLocale(Session::get('locale'));

                            $qr_url = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . $url;

                            $shareComponent['facebook'] = "https://www.facebook.com/sharer/sharer.php?u=$url&quote=$shareContent";
                            $shareComponent['twitter'] = "https://twitter.com/intent/tweet?text=$shareContent";
                            $shareComponent['linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=$url";
                            $shareComponent['telegram'] = "https://telegram.me/share/url?text=$shareContent&url=$url";
                            $shareComponent['whatsapp'] = "https://api.whatsapp.com/send/?phone&text=$shareContent";

                            $datas = compact('card_details', 'plan_details', 'store_details', 'categories', 'business_card_details', 'products', 'settings', 'shareComponent', 'shareContent', 'config', 'enquiry_button', 'whatsapp_msg', 'currency', 'manifest', 'whatsAppNumberExists');
                            return view('templates.' . $business_card_details->theme_code, $datas);
                        } else {
                            return redirect()->route('user.edit.card', $card_details->id)->with('failed', trans('Please fill out the basic business details.'));
                        }
                    } else {
                        $enquiry_button = "#";

                        $business_card_details = DB::table('business_cards')->where('business_cards.card_id', $card_details->card_id)
                            ->join('users', 'business_cards.user_id', '=', 'users.user_id')
                            ->join('themes', 'business_cards.theme_id', '=', 'themes.theme_id')
                            ->select('business_cards.*', 'users.plan_details', 'themes.theme_code')
                            ->first();

                        if ($business_card_details) {

                            $feature_details = DB::table('business_fields')->where('card_id', $card_details->card_id)->orderBy('id', 'asc')->get();
                            $service_details = DB::table('services')->where('card_id', $card_details->card_id)->orderBy('id', 'asc')->get();
                            $product_details = DB::table('vcard_products')->where('card_id', $card_details->card_id)->orderBy('id', 'asc')->get();
                            $galleries_details = DB::table('galleries')->where('card_id', $card_details->card_id)->orderBy('id', 'asc')->get();
                            $testimonials = Testimonial::where('card_id', $card_details->card_id)->orderBy('id', 'asc')->get();
                            $payment_details = DB::table('payments')->where('card_id', $card_details->card_id)->get();
                            $business_hours = DB::table('business_hours')->where('card_id', $card_details->card_id)->first();
                            $make_enquiry = DB::table('business_fields')->where('card_id', $card_details->card_id)->where('type', 'wa')->first();
                            $iframes = DB::table('business_fields')->where('type', 'iframe')->where('card_id', $card_details->card_id)->orderBy('id', 'asc')->get();
                            $customTexts = DB::table('business_fields')->where('type', 'text')->where('card_id', $card_details->card_id)->orderBy('id', 'asc')->get();

                            // Appointment slots for the card
                            $appointmentSlots = CardAppointmentTime::where('card_id', $card_details->card_id)->orderBy('id', 'asc')->get();

                            // Initialize the time slots array
                            $appointmentEnabled = false;
                            $appointment_slots = [
                                'monday' => [],
                                'tuesday' => [],
                                'wednesday' => [],
                                'thursday' => [],
                                'friday' => [],
                                'saturday' => [],
                                'sunday' => [],
                            ];

                            // Iterate through the appointment slots and categorize them by day
                            foreach ($appointmentSlots as $slot) {
                                // Assuming your `CardAppointmentTime` model has a `day` attribute and a `time` attribute
                                $day = strtolower($slot->day); // Convert to lowercase to match array keys
                                $time = $slot->time_slots; // Assuming this contains the time range string like "16:00 - 17:00"

                                // Check if the day exists in the time_slots array
                                if (array_key_exists($day, $appointment_slots)) {
                                    $appointment_slots[$day][] = $time; // Add the time to the appropriate day
                                    // Get price
                                    $appointment_slots[$day][] = $slot->price;
                                }

                                $appointmentEnabled = true;
                            }

                            $appointment_slots = json_encode($appointment_slots); // Convert the array to JSON

                            if ($make_enquiry != null) {
                                $enquiry_button = $make_enquiry->content;
                            }

                            $settings = Setting::where('status', 1)->first();
                            $config = DB::table('config')->get();

                            SEOTools::setTitle($business_card_details->title);
                            SEOTools::setDescription($business_card_details->sub_title);
                            SEOTools::addImages([url($business_card_details->profile)]);

                            SEOMeta::setTitle($business_card_details->title);
                            SEOMeta::setDescription($business_card_details->sub_title);
                            SEOMeta::addMeta('article:section', $business_card_details->title, 'property');
                            SEOMeta::addKeyword(["'" . $business_card_details->title . "'", "'" . $business_card_details->title . " vcard online'"]);

                            OpenGraph::setTitle($business_card_details->title);
                            OpenGraph::setDescription($business_card_details->sub_title);
                            OpenGraph::setUrl(url($business_card_details->card_url));
                            OpenGraph::addImage([url($business_card_details->profile)]);

                            JsonLd::setTitle($business_card_details->title);
                            JsonLd::setDescription($business_card_details->sub_title);
                            JsonLd::addImage([url($business_card_details->profile)]);

                            // PWA
                            $icons = [
                                '512x512' => [
                                    'path' => url($business_card_details->profile),
                                    'purpose' => 'any'
                                ]
                            ];

                            $splash = [
                                '640x1136' => url($business_card_details->profile),
                                '750x1334' => url($business_card_details->profile),
                                '828x1792' => url($business_card_details->profile),
                                '1125x2436' => url($business_card_details->profile),
                                '1242x2208' => url($business_card_details->profile),
                                '1242x2688' => url($business_card_details->profile),
                                '1536x2048' => url($business_card_details->profile),
                                '1668x2224' => url($business_card_details->profile),
                                '1668x2388' => url($business_card_details->profile),
                                '2048x2732' => url($business_card_details->profile),
                            ];

                            // Card URL
                            $customdomainURL = "https://www." . $host;

                            $shortcuts = [
                                [
                                    'name' => $business_card_details->title,
                                    'description' => $business_card_details->sub_title,
                                    'url' => $customdomainURL,
                                    'icons' => [
                                        "src" => url($business_card_details->profile),
                                        "purpose" => "any"
                                    ]
                                ]
                            ];

                            $startUrl = $customdomainURL;

                            $fill = [
                                "name" => $business_card_details->title,
                                "short_name" => $business_card_details->title,
                                "start_url" => $startUrl,
                                "theme_color" => "#ffffff",
                                "icons" => $icons,
                                "splash" => $splash,
                                "shortcuts" => $shortcuts,
                            ];

                            $out = $this->generateNew($fill);

                            Storage::disk('public')->put("manifest/cd-" . $business_card_details->card_id . '.json', json_encode($out));

                            $manifest = url("storage/manifest/cd-" . $business_card_details->card_id . '.json');

                            // Generate service worker
                            $generateServiceWorker = new ServiceWorker();
                            $generateServiceWorker->generateServiceWorker($business_card_details->card_id, $business_card_details->card_url);

                            $plan_details = json_decode($business_card_details->plan_details, true);

                            $url = $host;
                            $business_name = $card_details->title;
                            $profile = $business_card_details->cover ? url($business_card_details->cover) : asset('images/default-cover.png');

                            $shareContent = $config[30]->config_value;
                            $shareContent = str_replace("{ business_name }", $business_name, $shareContent);
                            $shareContent = str_replace("{ business_url }", $url, $shareContent);

                            // If branding enabled, then show app name.

                            if ($plan_details['hide_branding'] == "1") {
                                $shareContent = str_replace("{ appName }", $business_name, $shareContent);
                            } else {
                                $shareContent = str_replace("{ appName }", $config[0]->config_value, $shareContent);
                            }

                            $url = urlencode($url);
                            $shareContent = urlencode($shareContent);

                            // Session::put('locale', strtolower($business_card_details->card_lang));
                            app()->setLocale(Session::get('locale'));

                            $qr_url = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . $url;

                            $shareComponent['facebook'] = "https://www.facebook.com/sharer/sharer.php?u=$url&quote=$shareContent";
                            $shareComponent['twitter'] = "https://twitter.com/intent/tweet?text=$shareContent";
                            $shareComponent['linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=$url";
                            $shareComponent['telegram'] = "https://telegram.me/share/url?text=$shareContent&url=$url";
                            $shareComponent['whatsapp'] = "https://api.whatsapp.com/send/?phone&text=$shareContent";

                            // Datas
                            $datas = compact('card_details', 'plan_details', 'business_card_details', 'feature_details', 'service_details', 'product_details', 'galleries_details', 'testimonials', 'payment_details', 'business_hours', 'appointmentEnabled', 'appointment_slots', 'settings', 'shareComponent', 'shareContent', 'config', 'enquiry_button', 'iframes', 'customTexts', 'manifest', 'whatsAppNumberExists');

                            return view('templates.' . $business_card_details->theme_code, $datas);
                        } else {
                            return redirect()->route('user.edit.card', $card_details->id)->with('failed', trans('Please fill out the basic business details.'));
                        }
                    }
                } else {
                    abort(404, trans('Vcard not found.'));
                }
            }

            // If no vCard is found, return a 404 page
            if (!$card_details) {
                abort(404, trans('Vcard not found.'));
            }

            abort(404, trans('Custom domain not activated.'));
        }
    }

    public function faq()
    {
        // Queries
        $config = DB::table('config')->get();

        // Check website
        if ($config[38]->config_value == "yes") {
            // Pages
            $page = DB::table('pages')->where('page_name', 'faq')->where('status', "active")->get();

            // Check page
            if (!$page->isEmpty()) {
                $faqPage = DB::table('pages')->where('page_name', 'faq')->get();
                $supportPage = DB::table('pages')->whereIn('page_name', ['footer', 'contact'])->get();
                $settings = Setting::where('status', 1)->first();

                // Seo Tools
                SEOTools::setTitle($faqPage[0]->title);
                SEOTools::setDescription($faqPage[0]->description);

                SEOMeta::setTitle($faqPage[0]->title);
                SEOMeta::setDescription($faqPage[0]->description);
                SEOMeta::addMeta('article:section', ucfirst($faqPage[0]->page_name) . ' - ' . $faqPage[0]->title, 'property');
                SEOMeta::addKeyword([$faqPage[0]->keywords]);

                OpenGraph::setTitle($faqPage[0]->title);
                OpenGraph::setDescription($faqPage[0]->description);
                OpenGraph::setUrl(URL::full());
                OpenGraph::addImage([asset($settings->site_logo), 'size' => 300]);

                JsonLd::setTitle($faqPage[0]->title);
                JsonLd::setDescription($faqPage[0]->description);
                JsonLd::addImage(asset($settings->site_logo));

                return view('website.pages.faq', compact('faqPage', 'supportPage', 'settings', 'config'));
            } else {
                abort(404);
            }
        } else {
            return redirect('/login');
        }
    }

    public function privacyPolicy()
    {
        // Queries
        $config = DB::table('config')->get();

        // Check website
        if ($config[38]->config_value == "yes") {
            // Pages
            $page = DB::table('pages')->where('page_name', 'privacy')->where('status', "active")->get();

            // Check page
            if (!$page->isEmpty()) {
                $privacyPage = DB::table('pages')->where('page_name', 'privacy')->get();
                $supportPage = DB::table('pages')->whereIn('page_name', ['footer', 'contact'])->get();
                $settings = Setting::where('status', 1)->first();

                // Seo Tools
                SEOTools::setTitle($privacyPage[0]->title);
                SEOTools::setDescription($privacyPage[0]->description);

                SEOMeta::setTitle($privacyPage[0]->title);
                SEOMeta::setDescription($privacyPage[0]->description);
                SEOMeta::addMeta('article:section', ucfirst($privacyPage[0]->page_name) . ' - ' . $privacyPage[0]->title, 'property');
                SEOMeta::addKeyword([$privacyPage[0]->keywords]);

                OpenGraph::setTitle($privacyPage[0]->title);
                OpenGraph::setDescription($privacyPage[0]->description);
                OpenGraph::setUrl(URL::full());
                OpenGraph::addImage([asset($settings->site_logo), 'size' => 300]);

                JsonLd::setTitle($privacyPage[0]->title);
                JsonLd::setDescription($privacyPage[0]->description);
                JsonLd::addImage(asset($settings->site_logo));

                return view('website.pages.privacy', compact('privacyPage', 'supportPage', 'settings', 'config'));
            } else {
                abort(404);
            }
        } else {
            return redirect('/login');
        }
    }

    public function refundPolicy()
    {
        // Queries
        $config = DB::table('config')->get();

        // Check website
        if ($config[38]->config_value == "yes") {
            // Pages
            $page = DB::table('pages')->where('page_name', 'refund')->where('status', "active")->get();

            // Check page
            if (!$page->isEmpty()) {
                $refundPage = DB::table('pages')->where('page_name', 'refund')->get();
                $supportPage = DB::table('pages')->whereIn('page_name', ['footer', 'contact'])->get();
                $settings = Setting::where('status', 1)->first();

                // Seo Tools
                SEOTools::setTitle($refundPage[0]->title);
                SEOTools::setDescription($refundPage[0]->description);

                SEOMeta::setTitle($refundPage[0]->title);
                SEOMeta::setDescription($refundPage[0]->description);
                SEOMeta::addMeta('article:section', ucfirst($refundPage[0]->page_name) . ' - ' . $refundPage[0]->title, 'property');
                SEOMeta::addKeyword([$refundPage[0]->keywords]);

                OpenGraph::setTitle($refundPage[0]->title);
                OpenGraph::setDescription($refundPage[0]->description);
                OpenGraph::setUrl(URL::full());
                OpenGraph::addImage([asset($settings->site_logo), 'size' => 300]);

                JsonLd::setTitle($refundPage[0]->title);
                JsonLd::setDescription($refundPage[0]->description);
                JsonLd::addImage(asset($settings->site_logo));

                return view('website.pages.refund', compact('refundPage', 'supportPage', 'settings', 'config'));
            } else {
                abort(404);
            }
        } else {
            return redirect('/login');
        }
    }

    public function termsAndConditions()
    {
        // Queries
        $config = DB::table('config')->get();

        // Check website
        if ($config[38]->config_value == "yes") {
            // Pages
            $page = DB::table('pages')->where('page_name', 'terms')->where('status', "active")->get();

            // Check page
            if (!$page->isEmpty()) {
                $termsPage = DB::table('pages')->where('page_name', 'terms')->get();
                $supportPage = DB::table('pages')->whereIn('page_name', ['footer', 'contact'])->get();
                $settings = Setting::where('status', 1)->first();

                // Seo Tools
                SEOTools::setTitle($termsPage[0]->title);
                SEOTools::setDescription($termsPage[0]->description);

                SEOMeta::setTitle($termsPage[0]->title);
                SEOMeta::setDescription($termsPage[0]->description);
                SEOMeta::addMeta('article:section', ucfirst($termsPage[0]->page_name) . ' - ' . $termsPage[0]->title, 'property');
                SEOMeta::addKeyword([$termsPage[0]->keywords]);

                OpenGraph::setTitle($termsPage[0]->title);
                OpenGraph::setDescription($termsPage[0]->description);
                OpenGraph::setUrl(URL::full());
                OpenGraph::addImage([asset($settings->site_logo), 'size' => 300]);

                JsonLd::setTitle($termsPage[0]->title);
                JsonLd::setDescription($termsPage[0]->description);
                JsonLd::addImage(asset($settings->site_logo));

                return view('website.pages.terms', compact('termsPage', 'supportPage', 'settings', 'config'));
            } else {
                abort(404);
            }
        } else {
            return redirect('/login');
        }
    }

    public function about()
    {
        // Queries
        $config = DB::table('config')->get();

        // Check website
        if ($config[38]->config_value == "yes") {
            // Pages
            $page = DB::table('pages')->where('page_name', 'about')->where('status', "active")->get();

            // Check page
            if (!$page->isEmpty()) {
                $aboutPage = DB::table('pages')->where('page_name', 'about')->get();
                $supportPage = DB::table('pages')->whereIn('page_name', ['footer', 'contact'])->get();
                $settings = Setting::where('status', 1)->first();

                // Seo Tools
                SEOTools::setTitle($aboutPage[0]->title);
                SEOTools::setDescription($aboutPage[0]->description);

                SEOMeta::setTitle($aboutPage[0]->title);
                SEOMeta::setDescription($aboutPage[0]->description);
                SEOMeta::addMeta('article:section', ucfirst($aboutPage[0]->page_name) . ' - ' . $aboutPage[0]->title, 'property');
                SEOMeta::addKeyword([$aboutPage[0]->keywords]);

                OpenGraph::setTitle($aboutPage[0]->title);
                OpenGraph::setDescription($aboutPage[0]->description);
                OpenGraph::setUrl(URL::full());
                OpenGraph::addImage([asset($settings->site_logo), 'size' => 300]);

                JsonLd::setTitle($aboutPage[0]->title);
                JsonLd::setDescription($aboutPage[0]->description);
                JsonLd::addImage(asset($settings->site_logo));

                return view('website.pages.about', compact('aboutPage', 'supportPage', 'settings', 'config'));
            } else {
                abort(404);
            }
        } else {
            return redirect('/login');
        }
    }

    public function contact()
    {
        // Queries
        $config = DB::table('config')->get();

        // Check website
        if ($config[38]->config_value == "yes") {
            // Pages
            $page = DB::table('pages')->where('page_name', 'contact')->where('status', "active")->get();

            // Check page
            if (!$page->isEmpty()) {
                $contactPage = DB::table('pages')->where('page_name', 'contact')->get();
                $supportPage = DB::table('pages')->whereIn('page_name', ['footer', 'contact'])->get();
                $settings = Setting::where('status', 1)->first();

                // Seo Tools
                SEOTools::setTitle($contactPage[0]->title);
                SEOTools::setDescription($contactPage[0]->description);

                SEOMeta::setTitle($contactPage[0]->title);
                SEOMeta::setDescription($contactPage[0]->description);
                SEOMeta::addMeta('article:section', ucfirst($contactPage[0]->page_name) . ' - ' . $contactPage[0]->title, 'property');
                SEOMeta::addKeyword([$contactPage[0]->keywords]);

                OpenGraph::setTitle($contactPage[0]->title);
                OpenGraph::setDescription($contactPage[0]->description);
                OpenGraph::setUrl(URL::full());
                OpenGraph::addImage([asset($settings->site_logo), 'size' => 300]);

                JsonLd::setTitle($contactPage[0]->title);
                JsonLd::setDescription($contactPage[0]->description);
                JsonLd::addImage(asset($settings->site_logo));

                return view('website.pages.contact', compact('contactPage', 'supportPage', 'settings', 'config'));
            } else {
                abort(404);
            }
        } else {
            return redirect('/login');
        }
    }

    // Custom pages
    public function customPage($id)
    {
        // Queries
        $config = DB::table('config')->get();

        // Check website
        if ($config[38]->config_value == "yes") {
            // Get page details
            $page = DB::table('pages')->where('section_title', $id)->where('status', "active")->first();

            if (!empty($page)) {
                $supportPage = DB::table('pages')->whereIn('page_name', ['footer', 'contact'])->get();
                $settings = Setting::where('status', 1)->first();

                // Seo Tools
                SEOTools::setTitle($page->title);
                SEOTools::setDescription($page->description);

                SEOMeta::setTitle($page->title);
                SEOMeta::setDescription($page->description);
                SEOMeta::addMeta('article:section', $page->title, 'property');
                SEOMeta::addKeyword([$page->keywords]);

                OpenGraph::setTitle($page->title);
                OpenGraph::setDescription($page->description);
                OpenGraph::setUrl(URL::full());
                OpenGraph::addImage([asset($settings->site_logo), 'size' => 300]);

                JsonLd::setTitle($page->title);
                JsonLd::setDescription($page->description);
                JsonLd::addImage(asset($settings->site_logo));

                // View page
                return view("website.pages.custom-page", compact('page', 'supportPage', 'config', 'settings'));
            } else {
                return abort(404);
            }
        } else {
            return redirect('/login');
        }
    }

    // Generate Json for manifest.json
    public function generateNew($fill)
    {
        $basicManifest = [
            'name' => $fill['name'],
            'short_name' => $fill['short_name'],
            'start_url' => $fill['start_url'],
            'background_color' => '#ffffff',
            'theme_color' => '#000000',
            'display' => 'standalone',
            'status_bar' => "black",
            'splash' => $fill['splash']
        ];

        foreach ($fill['icons'] as $size => $file) {
            $fileInfo = pathinfo($file['path']);
            $basicManifest['icons'][] = [
                'src' => $file['path'],
                'type' => 'image/' . $fileInfo['extension'],
                'sizes' => $size,
                'purpose' => $file['purpose']
            ];
        }

        if ($fill['shortcuts']) {
            foreach ($fill['shortcuts'] as $shortcut) {

                if (array_key_exists("icons", $shortcut)) {
                    $fileInfo = pathinfo($shortcut['icons']['src']);
                    $icon = [
                        'src' => $shortcut['icons']['src'],
                        'type' => 'image/' . $fileInfo['extension'],
                        'sizes' => $size,
                        'purpose' => $shortcut['icons']['purpose']
                    ];
                } else {
                    $icon = [];
                }

                $basicManifest['shortcuts'][] = [
                    'name' => trans($shortcut['name']),
                    'description' => trans($shortcut['description']),
                    'url' => $shortcut['url'],
                    'icons' => [
                        $icon
                    ]
                ];
            }
        }
        return $basicManifest;
    }
}
