<?php

namespace App\Http\Controllers\User;

use App\Setting;
use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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

    // All categories
    public function categories(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::where('user_id', Auth::user()->user_id)->get();

            return DataTables::of($categories)
                ->addIndexColumn()
                ->editColumn('created_at', function ($category) {
                    return formatDateForUser($category->created_at);
                })
                ->editColumn('category_name', function ($category) {
                    return '<strong>' . $category->category_name . '</strong>';
                })
                ->editColumn('status', function ($category) {
                    return $category->status == 0
                        ? '<span class="badge bg-red text-white text-white">' . __('Disabled') . '</span>'
                        : '<span class="badge bg-green text-white text-white">' . __('Enabled') . '</span>';
                })
                ->addColumn('action', function ($category) {
                    $actionBtn = '<a class="dropdown-item" href="' . route('user.edit.category', $category->category_id) . '"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-pencil"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>' . __('Edit') . '</a>';
                    $actionBtn .= $category->status == 1
                        ? '<a class="dropdown-item" onclick="updateStatus(`' . $category->category_id . '`, `disable`); return false;"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-server-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12h-6a3 3 0 0 1 -3 -3v-2c0 -1.083 .574 -2.033 1.435 -2.56m3.565 -.44h10a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-2" /><path d="M16 12h2a3 3 0 0 1 3 3v2m-1.448 2.568a2.986 2.986 0 0 1 -1.552 .432h-12a3 3 0 0 1 -3 -3v-2a3 3 0 0 1 3 -3h6" /><path d="M7 8v.01" /><path d="M7 16v.01" /><path d="M3 3l18 18" /></svg>' . __('Disable') . '</a>'
                        : '<a class="dropdown-item" onclick="updateStatus(`' . $category->category_id . '`, `enable`); return false;"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-server"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M3 12m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M7 8l0 .01" /><path d="M7 16l0 .01" /></svg>' . __('Enable') . '</a>';
                    $actionBtn .= '<a class="dropdown-item text-danger" onclick="deleteCategory(`' . $category->category_id . '`, `deleted`); return false;"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>' . __('Delete') . '</a>';

                    return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#"
                                data-toggle="dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <div class="nav-item dropdown">
                                    ' . $actionBtn . '
                                </div>
                            </div>';
                })
                ->rawColumns(['category_name', 'status', 'action'])
                ->make(true);
        }

        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        return view('user.pages.categories.index', compact('settings', 'config'));
    }

    // Create category
    public function createCategory()
    {
        // Queries
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        // Get plan details
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);

        // Queries
        $categories = Category::where('user_id', Auth::user()->user_id)->count();

        // Chech vcard creation limit
        if ($categories <= $plan_details->no_of_categories) {
            return view('user.pages.categories.create', compact('settings', 'config'));
        } else {
            return redirect()->route('user.categories')->with('failed', trans('You have reached the plan limit!'));
        }
    }

    // Save category
    public function saveCategory(Request $request)
    {
        // Get plan details
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);

        // Queries
        $categories = Category::where('user_id', Auth::user()->user_id)->count();

        // Check categories limit
        if ($categories < $plan_details->no_of_categories) {

            // Validity
            $validator = Validator::make($request->all(), [
                'thumbnail' => 'required|mimes:jpeg,png,jpg|max:' . env("SIZE_LIMIT") . '',
                'category_name' => 'required',
            ]);

            // Validate alert
            if ($validator->fails()) {
                return back()->with('failed', $validator->messages()->all()[0])->withInput();
            }

            // get thumbnail image
            $thumbnail = $request->thumbnail->getClientOriginalName();
            $UploadThumbnail = pathinfo($thumbnail, PATHINFO_FILENAME);
            $UploadExtension = pathinfo($thumbnail, PATHINFO_EXTENSION);

            // Upload image
            if ($UploadExtension == "jpeg" || $UploadExtension == "png" || $UploadExtension == "jpg" || $UploadExtension == "gif" || $UploadExtension == "svg") {
                // Upload image
                $thumbnail = 'images/categories/' . 'IMG-' . uniqid() . '-' . time() . '.' . $request->thumbnail->extension();
                $request->thumbnail->move(public_path('images/categories'), $thumbnail);
            }

            // Save
            $category = new Category;
            $category->user_id = Auth::user()->user_id;
            $category->category_id = uniqid();
            $category->thumbnail = $thumbnail;
            $category->category_name = ucfirst($request->category_name);
            $category->save();

            return redirect()->route('user.create.category')->with('success', trans('New Category Created!'));
        } else {
            return redirect()->route('user.categories')->with('failed', trans('You have reached the plan limit!'));
        }
    }

    // Edit category
    public function editCategory(Request $request, $id)
    {
        // Parameters
        $category_id = $request->id;

        // Queries
        $category_details = Category::where('category_id', $category_id)->first();
        $settings = Setting::where('status', 1)->first();

        if ($category_details == null) {
            return redirect()->route('user.categories')->with('failed', trans('Category not found!'));
        } else {
            return view('user.pages.categories.edit', compact('category_details', 'settings'));
        }
    }

    // Update category
    public function updateCategory(Request $request)
    {
        // Validity
        if (!isset($request->thumbnail)) {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
                'category_name' => 'required',
            ]);

            // Validate alert
            if ($validator->fails()) {
                return back()->with('failed', $validator->messages()->all()[0])->withInput();
            }

            // Update query
            Category::where('category_id', $request->category_id)->update([
                'category_name' => ucfirst($request->category_name), 'updated_at' => now(),
            ]);

            return redirect()->route('user.edit.category', $request->category_id)->with('success', trans('Category details updated!'));
        } else {
            // Validity
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
                'thumbnail' => 'required|mimes:jpeg,png,jpg|max:' . env("SIZE_LIMIT") . '',
            ]);

            // Validate alert
            if ($validator->fails()) {
                return back()->with('failed', $validator->messages()->all()[0])->withInput();
            }

            // get thumbnail image
            $thumbnail = $request->thumbnail->getClientOriginalName();
            $UploadThumbnail = pathinfo($thumbnail, PATHINFO_FILENAME);
            $UploadExtension = pathinfo($thumbnail, PATHINFO_EXTENSION);

            // Upload image
            if ($UploadExtension == "jpeg" || $UploadExtension == "png" || $UploadExtension == "jpg" || $UploadExtension == "gif" || $UploadExtension == "svg") {
                // Upload image
                $thumbnail = 'images/categories/' . 'IMG-' . uniqid() . '-' . time() . '.' . $request->thumbnail->extension();
                $request->thumbnail->move(public_path('images/categories'), $thumbnail);
            }

            // Update query
            Category::where('category_id', $request->category_id)->update([
                'thumbnail' => $thumbnail, 'category_name' => ucfirst($request->category_name), 'updated_at' => now(),
            ]);

            return redirect()->route('user.edit.category', $request->category_id)->with('success', trans('Category details updated!'));
        }
    }

    // Status category
    public function statusCategory(Request $request)
    {
        // Queries
        $category_details = Category::where('category_id', $request->query('id'))->first();

        // Get status
        if ($category_details->status == 0) {
            $status = 1;
        } else {
            $status = 0;
        }

        // Update query
        Category::where('category_id', $request->query('id'))->update(['status' => $status]);

        return redirect()->route('user.categories')->with('success', trans('Updated!'));
    }

    // Delete category
    public function deleteCategory(Request $request)
    {
        // Delete
        Category::where('category_id', $request->query('id'))->delete();

        return redirect()->route('user.categories')->with('success', trans('Removed!'));
    }
}
