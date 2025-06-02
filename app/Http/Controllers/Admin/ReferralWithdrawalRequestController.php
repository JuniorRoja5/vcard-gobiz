<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use App\Currency;
use Illuminate\Http\Request;
use App\ReferralWithdrawRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\ReferralWithdrawTransaction;
use Yajra\DataTables\Facades\DataTables;

class ReferralWithdrawalRequestController extends Controller
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

    //  Referral Withdrawal Request
    public function index(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable referral system
        if ($config[80]->config_value == '0') {
            return abort(404);
        }

        // Referral Withdrawal Requests
        $referral_withdraw_requests = ReferralWithdrawRequest::where('referral_withdraw_requests.status', 1)->join('users', 'users.user_id', '=', 'referral_withdraw_requests.user_id')
            ->orderBy('referral_withdraw_requests.id', 'desc')->get();

        // Currency symbol
        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        if ($request->ajax()) {
            return DataTables::of($referral_withdraw_requests)
                ->addIndexColumn()
                ->addColumn('created_at', function ($withdraw_request) {
                    return formatDateForUser($withdraw_request->created_at);
                })
                ->addColumn('user_id', function ($withdraw_request) {
                    return '<a href="' . route('admin.view.customer', $withdraw_request->user_id) . '" target="_blank"><span class="fw-bold">' . $withdraw_request->name . '</span></a>';
                })
                ->addColumn('amount', function ($withdraw_request) use ($symbol) {
                    return $symbol . $withdraw_request->amount;
                })
                ->addColumn('bank_details', function ($withdraw_request) {
                    return "<span class='fw-bold'>" . $withdraw_request->bank_details . "</span>";
                })
                ->addColumn('payment_status', function ($withdraw_request) {
                    if ($withdraw_request->payment_status == 0) {
                        return '<span class="badge bg-warning badge-outline text-white">' . __("Pending") . '</span>';
                    } else if ($withdraw_request->payment_status == 1) {
                        return '<span class="badge bg-success badge-outline text-white">' . __("Accepted") . '</span>';
                    } else if ($withdraw_request->payment_status == 2) {
                        return '<span class="badge bg-primary badge-outline text-white">' . __("Transferred") . '</span>';
                    } else {
                        return '<span class="badge bg-danger badge-outline text-white">' . __("Rejected") . '</span>';
                    }
                })
                ->addColumn('actions', function ($withdraw_request) {
                    $actions = [
                        0 => [
                            ['status' => 1, 'label' => __('Accept')],
                            ['status' => -1, 'label' => __('Reject')]
                        ],
                        1 => [
                            ['status' => 2, 'label' => __('Transfer')],
                            ['status' => -1, 'label' => __('Reject')]
                        ],
                        -1 => [
                            ['status' => 1, 'label' => __('Accept')]
                        ],
                        2 => [
                            ['status' => -1, 'label' => __('Reject')],
                            ['status' => 1, 'label' => __('Accept')]
                        ],
                    ];

                    $actionBtn = '';
                    foreach ($actions[$withdraw_request->payment_status] ?? [] as $action) {
                        $actionBtn .= '<a href="#" onclick="updateWithdrawalRequest(`' . $withdraw_request->referral_withdraw_request_id . '`, ' . $action['status'] . ')" class="dropdown-item">' . $action['label'] . '</a>';
                    }

                    return '
                        <a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                        <div class="dropdown-menu dropdown-menu-end" style="">
                            <div class="nav-item dropdown">
                                ' . $actionBtn . '
                            </div>
                        </div>';
                })
                ->rawColumns(['created_at', 'user_id', 'amount', 'bank_details', 'payment_status', 'actions'])
                ->make(true);
        }

        return view('admin.pages.referral-withdrawal-request.index', compact('settings', 'config'));
    }

    // Withdrawal request accepted / transfer / rejected
    public function updateWithdrawalRequestStatus(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable referral system
        if ($config[80]->config_value == '0') {
            return abort(404);
        }

        // Parameters
        $requestId = $request->query('requestId');
        $paymentStatus = $request->query('status');

        // Withdrawal Request
        $withdrawalRequest = ReferralWithdrawRequest::where('referral_withdraw_request_id', $requestId)->first();

        // Check withdrawal request is exist
        if ($withdrawalRequest == null) {
            return redirect()->route('admin.referral.withdrawal.request')->with('failed', __('Not Found!'));
        }

        // Check status
        if ($paymentStatus == 'accepted') {
            $paymentStatus = 1;
        } elseif ($paymentStatus == 'rejected') {
            $paymentStatus = -1;
        }

        // Update Payment Status
        $withdrawalRequest->payment_status = $paymentStatus;
        $withdrawalRequest->save();

        // Payment status is 2
        if ($paymentStatus == 2) {
            // Save transaction details
            $transaction = new ReferralWithdrawTransaction();
            $transaction->referral_withdraw_request_id = $withdrawalRequest->referral_withdraw_request_id;
            $transaction->transfer_id = $withdrawalRequest->transfer_id;
            $transaction->notes = $withdrawalRequest->notes;
            $transaction->payment_status = 2;
            $transaction->save();
        }

        return redirect()->route('admin.referral.withdrawal.request')->with('success', __('Updated!'));
    }

    // Withdrawal request transfer
    public function transfer(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable referral system
        if ($config[80]->config_value == '0') {
            return abort(404);
        }

        // Parameters
        $requestId = $request->request_id;
        $transactionId = $request->transfer_transaction_id;
        $notes = $request->transfer_notes;

        // Withdrawal Request
        $withdrawalRequest = ReferralWithdrawRequest::where('referral_withdraw_request_id', $requestId)->first();

        // Check withdrawal request is exist
        if ($withdrawalRequest == null) {
            return redirect()->route('admin.referral.withdrawal.request')->with('failed', __('Not Found!'));
        }

        // Update Payment Status
        $withdrawalRequest->payment_status = 2;
        $withdrawalRequest->notes = $notes;
        $withdrawalRequest->save();

        // Save transaction details
        $transaction = new ReferralWithdrawTransaction();
        $transaction->referral_withdraw_request_id = $withdrawalRequest->referral_withdraw_request_id;
        $transaction->transfer_id = $transactionId;
        $transaction->notes = $notes;
        $transaction->payment_status = 2;
        $transaction->save();

        return redirect()->route('admin.referral.withdrawal.request')->with('success', __('Transferred!'));
    }
}
