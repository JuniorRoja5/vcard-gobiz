<?php

namespace App\Http\Controllers\User;

use App\Plan;
use App\User;
use App\Setting;
use App\Currency;
use App\Referral;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\ReferralWithdrawRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

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

    // Index
    public function index(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable referral system
        if ($config[80]->config_value == '0') {
            return abort(404);
        }

        // Currency symbol
        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        if ($active_plan != null) {

            // Get referral details
            $customerDetails = User::where('user_id', Auth::user()->user_id)->first();
            $referralUrl = url('register/?ref=' . Str::upper($customerDetails->user_id));

            // Minimum Withdrawal Amount
            $minWithdrawalAmount = $config[83]->config_value;

            // Referral Amount
            $earnings = Referral::where('is_registered', 1)->where('is_subscribed', 1)->where('referred_user_id', Auth::user()->user_id)->get();

            // Default Earning
            $overAllEarning = 0;
            $currentBalance = 0;
            foreach ($earnings as $earning) {
                $overAllEarning += (float) json_decode($earning->referral_scheme)->referral_amount;
            }

            // Withdrawals
            $withdrawals = ReferralWithdrawRequest::where('payment_status', 2)->where('user_id', Auth::user()->user_id)->sum('amount');

            $currentBalance = $overAllEarning - $withdrawals;

            if ($request->ajax()) {
                // Joins "users", "referral_system" tables
                $referrals = Referral::where('referrals.status', 1)
                    ->leftJoin('users as referrer', 'referrer.user_id', '=', 'referrals.user_id')
                    ->leftJoin('users as referred', 'referred.user_id', '=', 'referrals.referred_user_id')
                    ->select('referrals.*', 'referrer.name as referrer_name', 'referred.name as referred_name', 'referrer.user_id as referrer_user_id', 'referred.user_id as referred_user_id')
                    ->where('referrals.referred_user_id', Auth::user()->user_id)
                    ->orderBy('referrals.created_at', 'desc')
                    ->get();

                return DataTables::of($referrals)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($referral) {
                        return formatDateForUser($referral->created_at);
                    })
                    ->addColumn('user_id', function ($referral) {
                        if ($referral->referrer_user_id == null) {
                            return '<span class="fw-bold">' . __("Customer not available") . '</span>';
                        } else {
                            return '<span class="fw-bold">' . $referral->referrer_name . '</span>';
                        }
                    })
                    ->editColumn('is_registered', function ($referral) {
                        if ($referral->is_registered == 1) {
                            return '<span class="badge bg-success text-white">' . __('Yes') . '</span>';
                        } else {
                            return '<span class="badge bg-danger text-white">' . __('No') . '</span>';
                        }
                    })
                    ->editColumn('is_subscribed', function ($referral) {
                        if ($referral->is_subscribed == 1) {
                            return '<span class="badge bg-success text-white">' . __('Yes') . '</span>';
                        } else {
                            return '<span class="badge bg-danger text-white">' . __('No') . '</span>';
                        }
                    })
                    ->editColumn('referral_amount', function ($referral) {
                        // Get config
                        $data = DB::table('config')->get();
                        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                        $symbol = $currencies[$data[1]->config_value] ?? '';

                        if ($referral->is_registered == 1 && $referral->is_subscribed == 1) {
                            // Referral amount details
                            $jsonDecode = json_decode($referral->referral_scheme);
                            return "<span class='fw-bold'>" . $symbol . '' . $jsonDecode->referral_amount . '</span>';
                        } else {
                            return '<span class="badge bg-danger badge-outline text-white">' . __("Plan not subscribed") . '</span>';
                        }
                    })
                    ->editColumn('status', function ($referral) {
                        if($referral->is_registered == 1 && $referral->is_subscribed == 0) {
                            return '<span class="badge bg-primary text-white">' . __('Waiting for subscription') . '</span>';
                        } elseif ($referral->is_registered == 1 && $referral->is_subscribed == 1) {
                            return '<span class="badge bg-success text-white">' . __('Completed') . '</span>';
                        } else {
                            return '<span class="badge bg-primary text-white">' . __('Waiting for registration') . '</span>';
                        }
                    })
                    ->rawColumns(['created_at', 'user_id', 'referred_user_id', 'is_registered', 'is_subscribed', 'referral_amount', 'status'])
                    ->make(true);
            }

            return view('user.pages.referral.index', compact('referralUrl', 'minWithdrawalAmount', 'overAllEarning', 'currentBalance', 'settings', 'config', 'symbol'));
        } else {
            return redirect()->route('user.plans');
        }
    }

    // Withdrawal request
    public function withdrawalRequest(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable referral system
        if ($config[80]->config_value == '0') {
            return abort(404);
        }

        // Currency symbol
        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $customerDetails = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($customerDetails->plan_details);

        // Bank details for withdrawal request
        $bankDetails = $customerDetails->bank_details;

        if ($active_plan != null) {

            if ($request->ajax()) {
                // Joins "referral_withdraw_requests" and "users" tables
                $withdrawalRequests = ReferralWithdrawRequest::leftJoin('users', 'referral_withdraw_requests.user_id', '=', 'users.user_id')->leftJoin('referral_withdraw_transactions', 'referral_withdraw_requests.referral_withdraw_request_id', '=', 'referral_withdraw_transactions.referral_withdraw_request_id')
                    ->select('referral_withdraw_requests.*', 'users.*', 'referral_withdraw_transactions.transfer_id', 'referral_withdraw_transactions.notes')
                    ->where('referral_withdraw_requests.user_id', Auth::user()->user_id)
                    ->orderBy('referral_withdraw_requests.id', 'desc')
                    ->get();

                // dd($withdrawalRequests);

                return DataTables::of($withdrawalRequests)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($withdrawalRequest) {
                        return formatDateForUser($withdrawalRequest->created_at);
                    })
                    ->editColumn('request_id', function ($withdrawalRequest) {
                        return '<span class="fw-bold">' . $withdrawalRequest->referral_withdraw_request_id . '</span>';
                    })
                    ->editColumn('transfer_id', function ($withdrawalRequest) {
                        if ($withdrawalRequest->transfer_id != null) {
                            return '<span class="fw-bold">' . $withdrawalRequest->transfer_id . '</span>';
                        } else {
                            return '<span class="fw-bold">-</span>';
                        }
                    })
                    ->editColumn('amount', function ($withdrawalRequest) {
                        // Get config
                        $data = DB::table('config')->get();
                        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                        $symbol = $currencies[$data[1]->config_value] ?? '';

                        return '<span class="fw-bold">' . $symbol . $withdrawalRequest->amount . '</span>';
                    })
                    ->editColumn('bank_details', function ($withdrawalRequest) {
                        // Transfer to
                        $bankDetails = $withdrawalRequest->bank_details;

                        return '<span class="fw-bold">' . $bankDetails ?? '-' . '</span>';
                    })
                    ->editColumn('notes', function ($withdrawalRequest) {
                        if ($withdrawalRequest->notes != null) {
                            return '<span class="fw-bold">' . $withdrawalRequest->notes . '</span>';
                        } else {
                            return '<span class="fw-bold">-</span>';
                        }
                    })
                    ->editColumn('payment_status', function ($withdrawalRequest) {
                        if ($withdrawalRequest->payment_status == 0) {
                            return '<span class="badge bg-warning text-white">' . __('Waiting for Approval') . '</span>';
                        } elseif ($withdrawalRequest->payment_status == 1) {
                            return '<span class="badge bg-primary text-white">' . __('Processed') . '</span>';
                        } elseif ($withdrawalRequest->payment_status == 2) {
                            return '<span class="badge bg-success text-white">' . __('Transferred') . '</span>';
                        } else {
                            return '<span class="badge bg-danger text-white">' . __('Withdrawal Rejected') . '</span>';
                        }
                    })
                    ->rawColumns(['created_at', 'request_id', 'transfer_id', 'amount', 'bank_details', 'notes', 'payment_status'])
                    ->make(true);
            }

            return view('user.pages.referral.withdrawal-request', compact('bankDetails', 'settings', 'config'));
        } else {
            return redirect()->route('user.plans');
        }
    }

    // Update Bank Details
    public function updateBankDetails(Request $request)
    {
        // Get Bank Details
        $bankDetails = $request->bank_details;

        $user = Auth::user();

        // Update Bank Details
        $user->bank_details = $bankDetails;
        $user->save();

        return redirect()->route('user.referrals.withdrawal.request')->with('success', __('Bank Details Updated Successfully'));
    }

    // New Withdrawal Request
    public function newWithdrawalRequest()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable referral system
        if ($config[80]->config_value == '0') {
            return abort(404);
        }

        // Minimum Withdrawal Amount
        $minWithdrawalAmount = $config[83]->config_value;

        // Referral Amount
        $earnings = Referral::where('is_registered', 1)->where('is_subscribed', 1)->where('referred_user_id', Auth::user()->user_id)->get();

        // Default Earning
        $overAllEarning = 0;
        $currentBalance = 0;
        foreach ($earnings as $earning) {
            $overAllEarning += (float) json_decode($earning->referral_scheme)->referral_amount;
        }

        // Withdrawals
        $withdrawals = ReferralWithdrawRequest::where('payment_status', 2)->where('user_id', Auth::user()->user_id)->sum('amount');

        $currentBalance = $overAllEarning - $withdrawals;

        // Check minimum balance
        if ($currentBalance < $minWithdrawalAmount) {
            return redirect()->route('user.referrals.withdrawal.request')->with('failed', __('Your current balance is less than the minimum withdrawal amount. Please increase your balance to proceed.'));
        }

        // Bank Details
        $customerDetails = User::where('user_id', Auth::user()->user_id)->first();
        $bankDetails = $customerDetails->bank_details;

        return view('user.pages.referral.new-request', [
            'bankDetails' => $bankDetails,
            'minWithdrawalAmount' => $minWithdrawalAmount,
            'currentBalance' => $currentBalance,
            'settings' => $settings,
            'config' => $config,
        ]);
    }

    // Save Withdrawal Request
    public function saveWithdrawalRequest(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'bank_details' => 'required',
            'amount' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.referrals.withdrawal.request')->with('failed', $validator->errors());
        }

        // Request ID
        $requestId = "REQ" . preg_replace('/\D/', '', Str::uuid());

        // Save Withdrawal Request
        $withdrawalRequest = new ReferralWithdrawRequest();
        $withdrawalRequest->referral_withdraw_request_id = $requestId;
        $withdrawalRequest->user_id = Auth::user()->user_id;
        $withdrawalRequest->amount = $request->amount;
        $withdrawalRequest->bank_details = json_encode($request->bank_details);
        $withdrawalRequest->notes = $request->notes ?? '-';
        $withdrawalRequest->payment_status = 0;
        $withdrawalRequest->save();

        // Update Bank Details
        $user = Auth::user();
        $user->bank_details = $request->bank_details;
        $user->save();

        return redirect()->route('user.referrals.withdrawal.request')->with('success', __('Withdrawal Requested Successfully'));
    }
}
