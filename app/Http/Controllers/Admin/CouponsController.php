<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Coupon;
use App\Setting;
use App\Currency;
use App\Transaction;
use App\AppliedCoupon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\NfcCardOrderTransaction;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Application;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CouponsController extends Controller
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

    // Get all coupons
    public function indexCoupons(Request $request)
    {
        // Queries
        $coupons = Coupon::where('status', '!=', 2)->orderBy('id', 'desc')->get();
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        if ($request->ajax()) {
            return DataTables::of($coupons)
                ->addIndexColumn()
                ->addColumn('used_for', function ($coupon) {
                    return '<span class="badge bg-green text-white text-white">' . __($coupon->used_for == 'plan' ? 'Plan' : 'NFC Card') . '</span>';
                })
                ->addColumn('coupon_code', function ($coupon) {
                    return '<span class="fw-bold text-uppercase">' . $coupon->coupon_code . '</span>';
                })
                ->addColumn('coupon_amount', function ($coupon) {
                    // Get config
                    $data = DB::table('config')->get();
                    $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                    $symbol = $currencies[$data[1]->config_value] ?? '';

                    if ($coupon->coupon_type == 'fixed') {
                        return $symbol . formatCurrency($coupon->coupon_amount);
                    } else {
                        return $coupon->coupon_amount . '%';
                    }
                })
                ->addColumn('validity', function ($coupon) {
                    return '<span class="fw-bold">' . date('Y-m-d', strtotime($coupon->coupon_expired_on)) . '</span>';
                })
                // Number of users who have used the coupon
                ->addColumn('user', function ($coupon) {
                    // Count the number of users who have used the coupon (user_id wise users -> id)
                    $count = AppliedCoupon::whereIn('coupon_id', [$coupon->coupon_code, $coupon->coupon_id])->distinct('user_id')->count('user_id');
                    return '<span class="fw-bold">' . __(':count User:plural (Used)', ['count' => $count, 'plural' => $count > 1 ? 's' : '']) . '</span>';
                })
                // Number of times used
                ->addColumn('used', function ($coupon) {
                    // Count the number of times used
                    $count = AppliedCoupon::whereIn('coupon_id', [$coupon->coupon_code, $coupon->coupon_id])->count();
                    return '<span class="fw-bold">' . __(':count Time:plural (Used)', ['count' => $count, 'plural' => $count > 1 ? 's' : '']) . '</span>';
                })
                ->addColumn('status', function ($coupon) {
                    if ($coupon->status == 0) {
                        return '<span class="badge bg-red text-white text-white">' . __('Disabled') . '</span>';
                    } else {
                        return '<span class="badge bg-green text-white text-white">' . __('Active') . '</span>';
                    }
                })
                ->addColumn('action', function ($coupon) {
                    $actions = '<span class="dropdown">
                                    <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                    <div class="actions dropdown-menu dropdown-menu-end" style="">
                                        <a class="dropdown-item" href="' . route('admin.edit.coupon', $coupon->coupon_id) . '">' . __('Edit') . '</a>';
                    $actions .= '<a class="dropdown-item" href="' . route('admin.statistics.coupon', Str::lower($coupon->coupon_code)) . '">' . __('Statistics') . '</a>';
                    if ($coupon->status == 0) {
                        $actions .= '<a class="dropdown-item" href="#" onclick="getCoupon(`' . $coupon->coupon_id . '`); return false;">' . __('Activate') . '</a>';
                    } else {
                        $actions .= '<a class="dropdown-item" href="#" onclick="getCoupon(`' . $coupon->coupon_id . '`); return false;">' . __('Deactivate') . '</a>';
                    }
                    $actions .= '<a class="dropdown-item" href="#" onclick="deleteCoupon(`' . $coupon->coupon_id . '`); return false;">' . __('Delete') . '</a>';
                    $actions .= '</div>
                                </span>';
                    return $actions;
                })
                ->rawColumns(['used_for', 'coupon_code', 'coupon_amount', 'validity', 'user', 'used', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pages.coupons.index', compact('coupons', 'settings', 'config'));
    }

    // Create a new coupon
    public function createCoupon(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.coupons.create', compact('settings', 'config'));
    }

    // Save a new coupon
    public function storeCoupon(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'used_for' => 'required',
            'code' => 'required',
            'type' => 'required',
            'discount' => 'required',
            'validity' => 'required',
            'user_limit' => 'required',
            'total_limit' => 'required',
        ]);

        // Validate message
        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Coupon code already exists
        if (Coupon::where('coupon_code', $request->code)->where('status', '!=', 2)->first()) {
            return redirect()->route('admin.coupons')->with('failed', trans('Already exists!'));
        }

        // Save
        $coupon = new Coupon;
        $coupon->coupon_id = uniqid();
        $coupon->used_for = $request->used_for;
        $coupon->coupon_code = Str::upper($request->code);
        $coupon->coupon_desc = $request->description;
        $coupon->coupon_type = $request->type;
        $coupon->coupon_amount = $request->discount;
        $coupon->coupon_expired_on = $request->validity . " 23:59:59";
        $coupon->coupon_user_usage_limit = $request->user_limit;
        $coupon->coupon_total_usage_limit = $request->total_limit;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('success', trans('Created!'));
    }

    // Statistics of coupon
    public function statisticsCoupon(Request $request, $id)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Get config
        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        // Get coupon details
        $couponDetails = Coupon::where('coupon_code', $id)->first();

        // Check $couponDetails is not null
        if (!$couponDetails) {
            return redirect()->route('admin.coupons')->with('failed', trans('Not Found!'));
        }

        // Check "used_for" for plan or nfc card
        if ($couponDetails->used_for == 'plan') {
            // Get applied coupon and coupon details (joins)
            $couponUsage = AppliedCoupon::where('applied_coupons.coupon_id', $couponDetails->coupon_id)
                ->join('coupons', 'coupons.coupon_id', '=', 'applied_coupons.coupon_id')
                ->select('applied_coupons.*', 'coupons.*') // Select all columns
                ->get();

            for ($i = 0; $i < count($couponUsage); $i++) {
                // Transactions
                $couponUsage[$i]->transactions = Transaction::whereJsonContains('invoice_details->applied_coupon', $couponDetails->coupon_code)->get();
                $couponUsage[$i]->user = User::where('id', $couponUsage[$i]->user_id)->first();
            }
        } else {
            // Get applied coupon and coupon details (joins)
            $couponUsage = AppliedCoupon::where('applied_coupons.coupon_id', $couponDetails->coupon_code)
                ->join('coupons', 'coupons.coupon_code', '=', 'applied_coupons.coupon_id')
                ->select('applied_coupons.*', 'coupons.*') // Select all columns
                ->get();
                
            for ($i = 0; $i < count($couponUsage); $i++) {
                // Transactions
                $couponUsage[$i]->transactions = NfcCardOrderTransaction::whereJsonContains('invoice_details->applied_coupon', $couponDetails->coupon_code)->get();
                $couponUsage[$i]->user = User::where('id', $couponUsage[$i]->user_id)->first();
            }
        }

        // Return the view with the necessary data
        return view('admin.pages.coupons.statistics', compact('couponUsage', 'symbol', 'settings', 'config'));
    }

    // Edit a coupon
    public function editCoupon(Request $request, $id)
    {
        // First we need to find the coupon
        $couponDetails = Coupon::where('coupon_id', $id)->first();

        // Check coupon exists
        if ($couponDetails == null) {
            return redirect()->route('admin.coupons')->with('failed', trans('Not Found!'));
        }

        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.coupons.edit', compact('couponDetails', 'config', 'settings'));
    }

    // Update a coupon
    public function updateCoupon(Request $request, $id)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'used_for' => 'required',
            'code' => 'required',
            'type' => 'required',
            'discount' => 'required',
            'validity' => 'required',
            'user_limit' => 'required',
            'total_limit' => 'required',
        ]);

        // Validate message
        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Check if coupon code already exists (excluding the current coupon)
        $couponDetails = Coupon::where('coupon_code', $request->code)
            ->where('status', '!=', 2)
            ->where('coupon_id', '!=', $id)
            ->first();

        if ($couponDetails) {
            return redirect()->route('admin.edit.coupon', $id)->with('failed', trans('Already exists!'));
        }

        // Update coupon
        $coupon = Coupon::where('coupon_id', $id)->first();
        $coupon->used_for = $request->used_for;
        $coupon->coupon_code = Str::upper($request->code);
        $coupon->coupon_desc = $request->description;
        $coupon->coupon_type = $request->type;
        $coupon->coupon_amount = $request->discount;
        $coupon->coupon_expired_on = $request->validity . " 23:59:59";
        $coupon->coupon_user_usage_limit = $request->user_limit;
        $coupon->coupon_total_usage_limit = $request->total_limit;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('success', trans('Updated!'));
    }

    // Update coupon status
    public function updateCouponStatus(Request $request)
    {
        // Update
        $coupon = Coupon::where('coupon_id', $request->query('id'))->first();

        // Check status
        if ($coupon->status == 1) {
            $coupon->status = 0;
        } else {
            $coupon->status = 1;
        }
        $coupon->save();

        return redirect()->route('admin.coupons')->with('success', trans('Updated!'));
    }

    // Delete coupon
    public function deleteCoupon(Request $request)
    {
        // Update
        $coupon = Coupon::where('coupon_id', $request->query('id'))->first();
        $coupon->status = 2;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('success', trans('Deleted!'));
    }
}
