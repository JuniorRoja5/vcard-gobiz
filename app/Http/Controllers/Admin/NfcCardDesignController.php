<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use App\Currency;
use App\NfcCardDesign;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class NfcCardDesignController extends Controller
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
     * Show the designs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }
        
        // Currency symbol
        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        if ($request->ajax()) {
            $designs = NfcCardDesign::where('status', '!=', 2)->get();

            return DataTables::of($designs)
                ->addIndexColumn()
                ->editColumn('image', function ($design) {
                    return '<a data-fslightbox="gallery" href="' . asset($design->nfc_card_front_image) . '">
                                <span style="background-image: url(' . asset($design->nfc_card_front_image) . ')" class="avatar avatar-lg" alt="' . $design->nfc_card_name . '"></span>
                            </a>
                            <a data-fslightbox="gallery" href="' . asset($design->nfc_card_back_image) . '">
                                <span style="background-image: url(' . asset($design->nfc_card_back_image) . ')" class="avatar avatar-lg d-none" alt="' . $design->nfc_card_name . '"></span>
                            </a>';
                })
                ->editColumn('name', function ($design) {
                    return '<a href="' . route('admin.edit.design', $design->nfc_card_id) . '" class="text-capitalize">' . trans($design->nfc_card_name) . '</a>';
                })
                ->editColumn('description', function ($design) {
                    return '<span>' . trans(nl2br(e(wordwrap($design->nfc_card_description, 85, "\n"))))
                    . '</span>';
                })
                ->editColumn('price', function ($design) use ($symbol) {
                    return $symbol . $design->nfc_card_price;
                })
                ->editColumn('available_stocks', function ($design) {
                    // Stock available below 10
                    if ($design->available_stocks < 10) {
                        return '<span class="badge bg-red text-white">' . $design->available_stocks . '</span>';
                    } else {
                        return '<span class="badge bg-green text-white">' . $design->available_stocks . '</span>';
                    }
                })
                ->editColumn('status', function ($design) {
                    return $design->status == 0
                        ? '<span class="badge bg-red text-white text-white">' . __('Deactivated') . '</span>'
                        : '<span class="badge bg-green text-white text-white">' . __('Activated') . '</span>';
                })
                ->editColumn('action', function ($design) {
                    // Edit
                    $actionBtn = '<a href="' . route('admin.edit.design', $design->nfc_card_id) . '" class="dropdown-item">' . __('Edit') . '</a>';

                    // Add stock
                    $actionBtn .= '<a href="' . route('admin.edit.design', $design->nfc_card_id) . '" class="dropdown-item">' . __('Add Stock') . '</a>';

                    // Check status
                    if($design->status == 0){
                        $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $design->nfc_card_id . '\', `activate`); return false;" class="dropdown-item">' . __('Activate') . '</a>';
                    } else {
                        $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $design->nfc_card_id . '\', `deactivate`); return false;" class="dropdown-item">' . __('Deactivate') . '</a>';
                    }

                    // Delete
                    $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $design->nfc_card_id . '\', `delete`); return false;" class="dropdown-item">' . __('Delete') . '</a>';

                    return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <div class="nav-item dropdown">
                                    ' . $actionBtn . '
                                </div>
                            </div>';
                })
                ->rawColumns(['image', 'name', 'description', 'price', 'available_stocks', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pages.nfc-card-design.index', compact('settings', 'config'));
    }

    /**
     * Create the design.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }
        
        return view('admin.pages.nfc-card-design.create', compact('settings', 'config'));
    }

    /**
     * Save the design.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'nfc_card_name' => 'required|string|max:255',
            'nfc_card_description' => 'string',
            'nfc_card_front_image' => 'required|mimes:jpeg,png,jpg|max:' . env("SIZE_LIMIT") . '',
            'nfc_card_back_image' => 'required|mimes:jpeg,png,jpg|max:' . env("SIZE_LIMIT") . '',
            'nfc_card_price' => 'required|numeric',
            'nfc_card_available_stocks' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Save image (front)
        $frontImage = $request->file('nfc_card_front_image');
        $frontImageName = Str::random(20) . '.' . $frontImage->extension();
        $frontImage->move(storage_path('app/public/nfc-card-designs'), $frontImageName);

        // Save image (back)
        $backImage = $request->file('nfc_card_back_image');
        $backImageName = Str::random(20) . '.' . $backImage->extension();
        $backImage->move(storage_path('app/public/nfc-card-designs'), $backImageName);


        // Unique validation
        $nfcCardId = uniqid();

        NfcCardDesign::create([
            'nfc_card_id' => $nfcCardId,
            'nfc_card_name' => ucfirst($request->nfc_card_name),
            'nfc_card_description' => ucfirst($request->nfc_card_description),
            'nfc_card_front_image' => "storage/nfc-card-designs/" . $frontImageName,
            'nfc_card_back_image' => "storage/nfc-card-designs/" . $backImageName,
            'nfc_card_price' => $request->nfc_card_price,
            'available_stocks' => $request->nfc_card_available_stocks,
        ]);

        return redirect()->route('admin.create.design')->with('success', trans('Created!'));
    }

    /**
     * Edit the design.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Request $request, $cardId)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Get the design
        $design = NfcCardDesign::where('nfc_card_id', $cardId)->first();
         
        return view('admin.pages.nfc-card-design.edit', compact('design', 'settings', 'config'));
    }

    /**
     * Update the design.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'nfc_card_name' => 'required|string|max:255',
            'nfc_card_description' => 'string',
            'nfc_card_price' => 'required|numeric',
            'nfc_card_available_stocks' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Update
        $design = NfcCardDesign::where('nfc_card_id', $request->nfc_card_id);

        $design->update([
            'nfc_card_name' => $request->nfc_card_name,
            'nfc_card_description' => $request->nfc_card_description,
            'nfc_card_price' => $request->nfc_card_price,
            'available_stocks' => $request->nfc_card_available_stocks,
            'updated_at' => now(),
        ]);

        // Check "nfc_card_front_image"
        if($request->hasFile('nfc_card_front_image')) {
            $validator = Validator::make($request->all(), [
                'nfc_card_front_image' => 'required|image|mimes:jpeg,png,jpg|max:' . env("SIZE_LIMIT") . '',
            ]);

            if ($validator->fails()) {
                return back()->with('failed', $validator->messages()->all()[0])->withInput();
            }

            // Save image (front)
            $frontImage = $request->file('nfc_card_front_image');
            $frontImageName = Str::random(20) . '.' . $frontImage->extension();
            $frontImage->move(storage_path('app/public/nfc-card-designs'), $frontImageName);

            // Update image (front)
            $design->update([
                'nfc_card_front_image' => "storage/nfc-card-designs/" . $frontImageName,
                'updated_at' => now(),
            ]);
        }

        // Check "nfc_card_back_image"
        if($request->hasFile('nfc_card_back_image')) {
            $validator = Validator::make($request->all(), [
                'nfc_card_back_image' => 'required|mimes:jpg,jpeg,png|max:' . env("SIZE_LIMIT") . ''
            ]);

            if ($validator->fails()) {
                return back()->with('failed', $validator->messages()->all()[0])->withInput();
            }

            // Save image (back)
            $backImage = $request->file('nfc_card_back_image');
            $backImageName = Str::random(20) . '.' . $backImage->extension();
            $backImage->move(storage_path('app/public/nfc-card-designs'), $backImageName);

            $design->update([
                'nfc_card_back_image' => "storage/nfc-card-designs/" . $backImageName,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.edit.design', $request->nfc_card_id)->with('success', trans('Updated!'));
    }

    // Update status
    public function action(Request $request)
    {
        // Get data from request
        $nfcCardId = $request->query('id');
        $nfcCardStatus = $request->query('status');

        // Check status
        switch ($nfcCardStatus) {
            case 'activate':
                $status = 1;
                break;

            case 'deactivate':
                $status = 0;
                break;

            case 'delete':
                $status = 2;
                break;

            default:
                $status = 1;
                break;
        }

        // Update status
        NfcCardDesign::where('nfc_card_id', $nfcCardId)->update(['status' => $status]);

        return redirect()->route('admin.designs')->with('success', trans('Updated!'));
    }
}
