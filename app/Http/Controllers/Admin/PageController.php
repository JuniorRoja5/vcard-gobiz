<?php

namespace App\Http\Controllers\Admin;

use App\Page;
use App\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
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

    //  Pages
    public function index(Request $request)
    {
        // Queries
        $settings = Setting::first();
        $config = DB::table('config')->get();

        // Static pages
        if ($request->ajax()) {
            $pages = DB::table('pages')
                ->whereIn('page_name', ['home', 'about', 'contact', 'faq', 'pricing', 'privacy', 'footer', 'refund', 'terms'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('page_name');

            return DataTables::of($pages)
                ->addIndexColumn()
                ->editColumn('page_name', function ($page) {
                    return match ($page->first()->page_name) {
                        'home' => __('Home'),
                        'about' => __('About Us'),
                        'contact' => __('Contact Us'),
                        'privacy' => __('Privacy Policy'),
                        'refund' => __('Refund Policy'),
                        'terms' => __('Terms & Conditions'),
                        default => trans(ucfirst($page->first()->page_name)),
                    };
                })
                ->editColumn('url', function ($page) {
                    $baseUrl = env('APP_URL');
                    return match ($page->first()->page_name) {
                        'home' => '<a href="' . $baseUrl . '" target="_blank">/</a>',
                        'about' => '<a href="' . $baseUrl . '/about-us" target="_blank">'. trans('/about-us') .'</a>',
                        'contact' => '<a href="' . $baseUrl . '/contact-us" target="_blank">'. trans('/contact-us') .'</a>',
                        'privacy' => '<a href="' . $baseUrl . '/privacy-policy" target="_blank">'. trans('/privacy-policy') .'</a>',
                        'refund' => '<a href="' . $baseUrl . '/refund-policy" target="_blank">'. trans('/refund-policy') .'</a>',
                        'terms' => '<a href="' . $baseUrl . '/terms-and-conditions" target="_blank">'. trans('/terms-and-conditions') .'</a>',
                        default => '<a href="' . $baseUrl . '/' . $page->first()->page_name . '" target="_blank">/' . trans($page->first()->page_name) . '</a>',
                    };
                })
                ->editColumn('status', function ($page) {
                    return $page->first()->status == 'active'
                        ? '<span class="badge bg-green text-white">' . __('Activated') . '</span>'
                        : '<span class="badge bg-red text-white">' . __('Deactivated') . '</span>';
                })
                ->addColumn('action', function ($page) {
                    $editUrl = route('admin.edit.page', $page->first()->page_name);
                    $actionBtn = '<a class="dropdown-item" href="' . $editUrl . '">' . __('Edit') . '</a>';

                    if ($page->first()->status == 'inactive') {
                        $actionBtn .= '<a class="dropdown-item" href="#" onclick="getDisablePage(\'' . $page->first()->page_name . '\', `activate`); return false;">' . __('Activate') . '</a>';
                    } else if (!in_array($page->first()->page_name, ['home', 'footer'])) {
                        $actionBtn .= '<a class="dropdown-item" href="#" onclick="getDisablePage(\'' . $page->first()->page_name . '\', `deactivate`); return false;">' . __('Deactivate') . '</a>';
                    }

                    return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <div class="nav-item dropdown">
                                    ' . $actionBtn . '
                                </div>
                            </div>';
                })
                ->rawColumns(['url', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pages.pages.index', compact('settings', 'config'));
    }

    public function customPagesIndex(Request $request)
    {
        // Queries
        $settings = Setting::first();
        $config = DB::table('config')->get();

        if ($request->ajax()) {
            $custom_pages = DB::table('pages')
                ->where('page_name', 'Custom Page')
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($custom_pages)
                ->addIndexColumn()
                ->editColumn('section_name', function ($page) {
                    return ucwords($page->section_name);
                })
                ->editColumn('url', function ($page) {
                    return '<a href="' . env('APP_URL') . '/p/' . $page->section_title . '" target="_blank">/' . $page->section_title . '</a>';
                })
                ->editColumn('status', function ($page) {
                    return $page->status == 'active'
                        ? '<span class="badge bg-green text-white">' . __('Activated') . '</span>'
                        : '<span class="badge bg-red text-white">' . __('Deactivated') . '</span>';
                })
                ->addColumn('action', function ($page) {
                    $editUrl = route('admin.edit.custom.page', $page->id);
                    $actionBtn = '<a class="dropdown-item" href="' . $editUrl . '">' . __('Edit') . '</a>';

                    if ($page->status == 'inactive') {
                        $actionBtn .= '<a class="dropdown-item" href="#" onclick="getPage(\'' . $page->id . '\', `activate`); return false;">' . __('Activate') . '</a>';
                    } else {
                        $actionBtn .= '<a class="dropdown-item" href="#" onclick="getPage(\'' . $page->id . '\', `deactivate`); return false;">' . __('Deactivate') . '</a>';
                    }

                    $actionBtn .= '<a class="dropdown-item" href="#" onclick="deletePage(\'' . $page->id . '\', `delete`); return false;">' . __('Delete') . '</a>';

                    return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <div class="nav-item dropdown">
                                    ' . $actionBtn . '
                                </div>
                            </div>';
                })
                ->rawColumns(['url', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pages.pages.index', compact('settings', 'config'));
    }

    // Add page
    public function addPage()
    {
        // Queries
        $config = DB::table('config')->get();
        $settings = Setting::first();

        // View
        return view('admin.pages.pages.add', compact('settings', 'config'));
    }

    // Save page
    public function savePage(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'page_name' => 'required',
            'slug' => 'required',
            'body' => 'required',
            'title' => 'required',
            'description' => 'required',
            'keywords' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Extract base64 images and replace with stored image URLs
        $updatedBody = preg_replace_callback(
            '/<img[^>]+src=["\']data:image\/([^;\']+);base64,([^"\']+)["\'][^>]*>/i',
            function ($matches) {
                $imageType = $matches[1]; // Get file extension (png, jpeg, etc.)
                $base64Data = base64_decode($matches[2]); // Decode base64 string

                if ($base64Data !== false) {
                    $imageName = Str::random(10) . '.' . $imageType;
                    $imagePath = 'uploads/pages/' . $imageName;

                    // Save image in storage/public/uploads/pages/
                    Storage::disk('public')->put($imagePath, $base64Data);

                    // Return new img tag with actual image URL
                    return '<img src="' . asset('storage/' . $imagePath) . '" />';
                }

                return $matches[0]; // If decoding fails, return original img tag
            },
            $request->body
        );

        // Save new page
        $page = new Page();
        $page->page_name = "Custom Page";
        $page->section_name = ucfirst($request->page_name);
        $page->section_title = $request->slug;
        $page->section_content = Purifier::clean($updatedBody); // Save cleaned HTML with stored image URLs
        $page->title = ucfirst($request->title);
        $page->description = ucfirst($request->description);
        $page->keywords = $request->keywords;
        $page->save();

        return redirect()->back()->with('success', trans('Created!'));
    }

    // Edit custom page
    public function editCustomPage($id)
    {
        // Get page details
        $page = DB::table('pages')->where('id', $id)->where('page_name', 'Custom Page')->first();

        if ($page) {
            // Queries
            $settings = Setting::first();
            $config = DB::table('config')->get();

            // View
            return view('admin.pages.pages.custom-edit', compact('page', 'settings', 'config'));
        } else {
            return redirect()->route('admin.pages')->with('failed', trans('Not Found!'));
        }
    }

    // Edit page
    public function editPage($id)
    {
        // Get page details
        $sections = DB::table('pages')->where('page_name', $id)->get();

        if (count($sections) > 0) {
            // Queries
            $settings = Setting::first();
            $config = DB::table('config')->get();

            // View
            return view('admin.pages.pages.edit', compact('sections', 'settings', 'config'));
        } else {
            return redirect()->route('admin.pages')->with('failed', trans('Not Found!'));
        }
    }

    // Update page
    public function updatePage(Request $request, $id)
    {
        // Update page
        $sections = DB::table('pages')->where('page_name', $id)->get();
        for ($i = 0; $i < count($sections); $i++) {
            $safe_section_content = $request->input('section' . $i);
            DB::table('pages')->where('page_name', $id)->where('id', $sections[$i]->id)->update(['section_content' => $safe_section_content]);
            DB::table('pages')->where('page_name', $id)->where('id', $sections[$i]->id)->update(['description' => $request->description, 'keywords' => $request->keywords]);
        }

        // SEO
        DB::table('pages')->where('page_name', $id)->update(['title' => $request->title]);
        DB::table('pages')->where('page_name', $id)->update(['keywords' => $request->keywords]);
        DB::table('pages')->where('page_name', $id)->update(['description' => $request->description]);

        // Page redirect
        return redirect()->route('admin.pages')->with('success', trans('Updated!'));
    }

    // Update custom page
    public function updateCustomPage(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'page_name' => 'required',
            'slug' => 'required',
            'body' => 'required',
            'title' => 'required',
            'description' => 'required',
            'keywords' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Extract and save base64 images from the body
        $bodyContent = $request->body;
        $updatedBody = preg_replace_callback(
            '/<img[^>]+src=["\']data:image\/([^;\']+);base64,([^"\']+)["\'][^>]*>/i',
            function ($matches) {
                $imageType = $matches[1]; // Get image extension
                $base64Data = base64_decode($matches[2]); // Decode the base64 string

                if ($base64Data !== false) {
                    $imageName = Str::random(10) . '.' . $imageType;
                    $imagePath = 'uploads/pages/' . $imageName;

                    // Store the image in the public folder
                    Storage::disk('public')->put($imagePath, $base64Data);

                    // Return updated img tag with new image URL
                    return '<img src="' . asset('storage/' . $imagePath) . '" />';
                }

                return $matches[0]; // Return original img tag if decoding fails
            },
            $bodyContent
        );

        // Clean HTML using Purifier before saving
        $cleanedBody = Purifier::clean($updatedBody);

        // Update page data in DB
        DB::table('pages')->where('id', $request->page_id)->update([
            'section_name' => $request->page_name,
            'section_title' => $request->slug,
            'section_content' => $cleanedBody,
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords
        ]);

        return redirect()->route('admin.pages')->with('success', trans('Updated!'));
    }

    // Status Page
    public function statusPage(Request $request)
    {
        // Get plan details
        $page_details = DB::table('pages')->where('id', $request->query('id'))->first();

        // Check status
        if ($page_details->status == 'inactive') {
            $status = 'active';
        } else {
            $status = 'inactive';
        }

        // Update status
        DB::table('pages')->where('id', $request->query('id'))->update(['status' => $status]);

        return redirect()->route('admin.pages')->with('success', trans('Updated!'));
    }

    // Disable Page
    public function disablePage(Request $request)
    {
        // Get plan details
        $page_details = DB::table('pages')->where('page_name', $request->query('id'))->first();

        // Check status
        if ($page_details->status == 'inactive') {
            $status = 'active';
        } else {
            $status = 'inactive';
        }

        // Update status
        DB::table('pages')->where('page_name', $request->query('id'))->update(['status' => $status]);

        return redirect()->route('admin.pages')->with('success', trans('Updated!'));
    }

    // Delete Page
    public function deletePage(Request $request)
    {
        // Update status
        DB::table('pages')->where('id', $request->query('id'))->delete();

        return redirect()->route('admin.pages')->with('success', trans('Deleted!'));
    }
}
