<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Spatie\Sitemap\Sitemap;
use Illuminate\Http\Request;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SitemapController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    // Generating a sitemap
    public function index()
    {
        // Queries
        $settings = Setting::active()->first();
        $config   = DB::table('config')->get();

        return view('admin.pages.sitemap.index', compact('settings', 'config'));
    }

    // Generate sitemap
    public function generate(Request $request)
    {
        $categories = $request->categories ?? null;

        // Create a new Sitemap instance
        $sitemap = Sitemap::create();

        // Generate sitemap for website pages
        if (in_array('pages', $categories) || in_array('all', $categories)) {
            $pages = DB::table('pages')->select('page_name')->groupBy('page_name')->get();
            foreach ($pages as $page) {
                $sitemap->add(Url::create(url($page->page_name == "home" ? "/" : $page->page_name)));
            }
        }

        // Generate sitemap for blogs
        if (in_array('blog', $categories) || in_array('all', $categories)) {
            $sitemap->add(Url::create(url('/blogs')));
            $blogs = DB::table('blogs')->get();
            foreach ($blogs as $blog) {
                $sitemap->add(Url::create(url('blog/' . $blog->slug)));
            }
        }

        // Generate sitemap for vCards
        if (in_array('vcards', $categories) || in_array('all', $categories)) {
            $vcards = DB::table('business_cards')->where('card_type', 'vcard')->get();
            foreach ($vcards as $vcard) {
                $sitemap->add(Url::create(url('/' . $vcard->card_url)));
                if ($vcard->custom_domain) {
                    $sitemap->add(Url::create('https://' . $vcard->custom_domain));
                }
            }
        }

        // Generate sitemap for stores
        if (in_array('store', $categories) || in_array('all', $categories)) {
            $stores = DB::table('business_cards')->where('card_type', 'store')->get();
            foreach ($stores as $store) {
                $sitemap->add(Url::create(url('/' . $store->card_url)));
                if ($store->custom_domain) {
                    $sitemap->add(Url::create('https://' . $store->custom_domain));
                }
            }
        }

        // Generate sitemap for web tools
        if (in_array('webtools', $categories) || in_array('all', $categories)) {
            $webtools = [
                '/html-beautifier',
                '/html-minifier',
                '/css-beautifier',
                '/css-minifier',
                '/js-beautifier',
                '/js-minifier',
                '/random-password-generator',
                '/bcrypt-password-generator',
                '/md5-password-generator',
                '/random-word-generator',
                '/text-counter',
                '/lorem-generator',
                '/emojies',
                '/dns-lookup',
                '/ip-lookup',
                '/whois-lookup'
            ];
            foreach ($webtools as $tool) {
                $sitemap->add(Url::create(url($tool)));
            }
        }

        // Save sitemap to public directory
        $sitemap->writeToFile(public_path('sitemap.xml'));

        return redirect()->route('admin.sitemap')->with('success', trans('Generated!'));
    }
}
