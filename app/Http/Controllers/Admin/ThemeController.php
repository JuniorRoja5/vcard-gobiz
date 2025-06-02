<?php

namespace App\Http\Controllers\Admin;

use App\Theme;
use App\Setting;
use App\BusinessCard;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ThemeController extends Controller
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

    // All Themes
    public function themes()
    {
        // Queries
        $themes = Theme::withCount('businessCards')
            ->orderByDesc('id')
            ->paginate(12);

        $settings = Setting::active()->first();

        return view('admin.pages.themes.index', compact('themes', 'settings'));
    }

    // Active Themes
    public function activeThemes()
    {
        // Queries
        $themes = Theme::where('status', 1)
            ->withCount('businessCards')
            ->orderByDesc('id')
            ->paginate(12);

        $settings = Setting::active()->first();

        return view('admin.pages.themes.active-themes', compact('themes', 'settings'));
    }

    // Disabled Themes
    public function disabledThemes()
    {
        // Queries
        $themes = Theme::where('status', 0)
            ->withCount('businessCards')
            ->orderByDesc('id')
            ->paginate(12);

        $settings = Setting::active()->first();

        return view('admin.pages.themes.disabled-themes', compact('themes', 'settings'));
    }

    // Edit theme
    public function editTheme(Request $request, $id)
    {
        // Queries
        $theme_details = Theme::where('theme_id', $id)->first();
        $settings = Setting::where('status', 1)->first();

        return view('admin.pages.themes.edit', compact('theme_details', 'settings'));
    }

    // Update theme
    public function updateTheme(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'theme_name' => 'required|min:3'
        ]);

        // Check theme thumbnail
        if (isset($request->theme_thumbnail)) {
            // Image validatation
            $validator = Validator::make($request->all(), [
                'theme_thumbnail' => 'required|mimes:jpeg,png,jpg,gif,svg|max:' . env("SIZE_LIMIT") . '',
            ]);

            if ($validator->fails()) {
                return back()->with('failed', $validator->messages()->all()[0])->withInput();
            }

            // get theme thumbnail
            $theme_thumbnail = $request->theme_thumbnail->getClientOriginalName();
            $UploadExtension = pathinfo($theme_thumbnail, PATHINFO_EXTENSION);

            // Upload image
            if ($UploadExtension == "jpeg" || $UploadExtension == "png" || $UploadExtension == "jpg" || $UploadExtension == "gif" || $UploadExtension == "svg") {
                // Upload image
                $fileName = time() . '.' . $request->theme_thumbnail->extension();

                $theme_thumbnail = 'img/vCards/' . $fileName;
                $request->theme_thumbnail->move(public_path('img/vCards'), $theme_thumbnail);

                // Update theme thumbnail
                Theme::where('theme_id', $request->theme_id)->update([
                    'theme_thumbnail' => $fileName,
                    'theme_name' => $request->theme_name
                ]);
            }

            return redirect()->route('admin.edit.theme', $request->theme_id)->with('success', trans('Updated!'));
        } else {
            // Update theme name
            Theme::where('theme_id', $request->theme_id)->update([
                'theme_name' => $request->theme_name
            ]);

            return redirect()->route('admin.edit.theme', $request->theme_id)->with('success', trans('Updated!'));
        }
    }

    // Update status
    public function updateThemeStatus(Request $request)
    {
        // Parameters
        if ($request->query('status') == 'enable') {
            $status = '1';
        } else {
            $status = '0';
        }

        Theme::where('theme_id', $request->query('id'))->update(['status' => $status]);

        return redirect()->back()->with('success', trans('Updated!'));
    }

    // Search theme
    public function searchTheme(Request $request)
    {
        // Parameters contains index or disabled-themes or active-themes
        if($request->query('view-page') && Str::contains($request->query('view-page'), ['disabled-themes', 'active-themes'])) {
            $page = $request->query('view-page');
        } else {
            $page = "index";
        }
        $search = $request->query('query');

        // Queries
        $settings = Setting::where('status', 1)->first();

        switch ($page) {
            case 'active-themes':
                // Queries
                $themes = Theme::where('theme_name', 'like', '%' . $search . '%')->where('status', '1')->paginate(12);

                for ($i = 0; $i < count($themes); $i++) {
                    $themes[$i]->business_cards_count = BusinessCard::where('theme_id', $themes[$i]->theme_id)->count();
                }
                break;

            case 'disabled-themes':
                // Queries
                $themes = Theme::where('theme_name', 'like', '%' . $search . '%')->where('status', '0')->paginate(12);

                for ($i = 0; $i < count($themes); $i++) {
                    $themes[$i]->business_cards_count = BusinessCard::where('theme_id', $themes[$i]->theme_id)->count();
                }
                break;

            default:
                // Queries
                $themes = Theme::where('theme_name', 'like', '%' . $search . '%')->paginate(12);

                for ($i = 0; $i < count($themes); $i++) {
                    $themes[$i]->business_cards_count = BusinessCard::where('theme_id', $themes[$i]->theme_id)->count();
                }
                break;
        }

        return view('admin.pages.themes.' . $page, compact('themes', 'settings'));
    }
}
