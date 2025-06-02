<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use App\Currency;
use App\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ReferralController extends Controller
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

    public function index(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable referral system
        if ($config[80]->config_value == '0') {
            return abort(404);
        }

        // Referrals
        $referrals = Referral::where('referrals.status', 1)
            ->leftJoin('users as referrer', 'referrer.user_id', '=', 'referrals.user_id')
            ->leftJoin('users as referred', 'referred.user_id', '=', 'referrals.referred_user_id')
            ->select('referrals.*', 'referrer.name as referrer_name', 'referred.name as referred_name', 'referrer.user_id as referrer_user_id', 'referred.user_id as referred_user_id')
            ->orderBy('referrals.created_at', 'desc')
            ->get();

        // Currency symbol
        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        if ($request->ajax()) {
            return DataTables::of($referrals)
                ->addIndexColumn()
                ->addColumn('created_at', function ($referral) {
                    return formatDateForUser($referral->created_at);
                })
                ->addColumn('user_id', function ($referral) {
                    if ($referral->referrer_user_id == null) {
                        return '<span class="fw-bold">' . __("Customer not available") . '</span>';
                    } else {
                        $viewUrl = route('admin.view.customer', $referral->referrer_user_id);
                        return '<a href="' . $viewUrl . '" target="_blank">' . trans($referral->referrer_name) . '</a>';
                    }
                })
                ->addColumn('referred_user_id', function ($referral) {
                    if ($referral->referred_user_id == null) {
                        return '<span class="fw-bold">' . __("Customer not available") . '</span>';
                    } else {
                        $viewUrl = route('admin.view.customer', $referral->referred_user_id);
                        return '<a href="' . $viewUrl . '" target="_blank">' . trans($referral->referred_name) . '</a>';
                    }
                })
                ->addColumn('is_registered', function ($referral) {
                    if ($referral->is_registered == 1) {
                        return '<span class="badge bg-success badge-outline text-white">' . __("Yes") . '</span>';
                    } else {
                        return '<span class="badge bg-danger badge-outline text-white">' . __("No") . '</span>';
                    }
                })
                ->addColumn('is_subscribed', function ($referral) {
                    if ($referral->is_subscribed == 1) {
                        return '<span class="badge bg-success badge-outline text-white">' . __("Yes") . '</span>';
                    } else {
                        return '<span class="badge bg-danger badge-outline text-white">' . __("No") . '</span>';
                    }
                })
                ->addColumn('referral_scheme', function ($referral) use ($symbol) {
                    if ($referral->is_registered == 1 && $referral->is_subscribed == 1) {
                        // Referral amount details
                        $jsonDecode = json_decode($referral->referral_scheme);
                        return "<span class='fw-bold'>" . $symbol . '' . $jsonDecode->referral_amount . '</span>';
                    } else {
                        return '<span class="badge bg-danger badge-outline text-white">' . __("Plan not subscribed") . '</span>';
                    }
                })
                ->rawColumns(['created_at', 'user_id', 'referred_user_id', 'is_registered', 'is_subscribed', 'referral_scheme'])
                ->make(true);
        }

        return view('admin.pages.referrals.index', compact('settings', 'config'));
    }
}
