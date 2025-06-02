<?php

namespace App\Http\Controllers\Admin;

use App\Plan;
use App\User;
use App\Setting;
use App\Currency;
use App\Referral;
use Carbon\Carbon;
use App\Transaction;
use App\BusinessCard;
use App\AppliedCoupon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TransactionsController extends Controller
{
    // All online paid transactions
    public function onlinePaidTransactions(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::where('payment_gateway_name', '!=', 'Offline')->where('payment_status', 'SUCCESS')->orderBy('id', 'desc')->get();

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return formatDateForUser($row->created_at);
                })
                ->addColumn('gobiz_transaction_id', function ($row) {
                    return $row->gobiz_transaction_id;
                })
                ->addColumn('transaction_id', function ($row) {
                    return $row->transaction_id;
                })
                ->addColumn('user', function ($row) {
                    $user_details = User::where('id', $row->user_id)->first();
                    if ($user_details) {
                        return '<a href="' . route('admin.view.user', $user_details->user_id) . '">' . $user_details->name . '</a>';
                    } else {
                        return '<a href="#">' . __("Customer not available") . '</a>';
                    }
                })
                ->addColumn('payment_gateway_name', function ($row) {
                    return __($row->payment_gateway_name);
                })
                ->addColumn('transaction_amount', function ($row) {
                    $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                    $symbol = $currencies[$row->transaction_currency] ?? '';
                    return $symbol . formatCurrency($row->transaction_amount);
                })
                ->addColumn('payment_status', function ($row) {
                    $status = '';
                    if ($row->payment_status == 'SUCCESS') {
                        $status = '<span class="badge bg-green text-white">' . __('Paid') . '</span>';
                    } elseif ($row->payment_status == 'FAILED') {
                        $status = '<span class="badge bg-red text-white">' . __('Failed') . '</span>';
                    } elseif ($row->payment_status == 'PENDING') {
                        $status = '<span class="badge bg-orange text-white">' . __('Pending') . '</span>';
                    } elseif ($row->payment_status == 'CANCELLED') {
                        $status = '<span class="badge bg-red text-white">' . __('Cancelled') . '</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="dropdown">
                                    <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                    <div class="actions dropdown-menu dropdown-menu-end">';
                    if ($row->payment_status == "SUCCESS") {
                        $actions .= '<a class="dropdown-item" target="_blank" href="' . route('admin.view.invoice', ['id' => $row->gobiz_transaction_id]) . '">' . __('Invoice') . '</a>';
                    }
                    if ($row->payment_status != "SUCCESS") {
                        $actions .= '<a class="dropdown-item" href="' . route('admin.trans.status', ['id' => $row->gobiz_transaction_id, 'status' => 'PENDING']) . '">' . __('Pending') . '</a>';
                        $actions .= '<a class="dropdown-item" href="#" onclick="getTransaction(\'' . $row->gobiz_transaction_id . '\'); return false;">' . __('Success') . '</a>';
                        $actions .= '<a class="dropdown-item" href="' . route('admin.trans.status', ['id' => $row->gobiz_transaction_id, 'status' => 'FAILED']) . '">' . __('Failed') . '</a>';
                    }
                    $actions .= '</div></div>';
                    return $actions;
                })
                ->rawColumns(['user', 'payment_status', 'action'])
                ->make(true);
        }

        $settings = Setting::where('status', 1)->first();
        $currencies = Currency::get();

        return view('admin.pages.transactions.online-paid', compact('settings', 'currencies'));
    }

    // All online unpaid transactions
    public function onlineUnpaidTransactions(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::where('payment_gateway_name', '!=', 'Offline')->where('payment_status', '!=', 'SUCCESS')->orderBy('id', 'desc')->get();

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return formatDateForUser($row->created_at);
                })
                ->addColumn('gobiz_transaction_id', function ($row) {
                    return $row->gobiz_transaction_id;
                })
                ->addColumn('transaction_id', function ($row) {
                    return $row->transaction_id;
                })
                ->addColumn('user', function ($row) {
                    $user_details = User::where('id', $row->user_id)->first();
                    if ($user_details) {
                        return '<a href="' . route('admin.view.user', $user_details->user_id) . '">' . $user_details->name . '</a>';
                    } else {
                        return '<a href="#">' . __("Customer not available") . '</a>';
                    }
                })
                ->addColumn('payment_gateway_name', function ($row) {
                    return __($row->payment_gateway_name);
                })
                ->addColumn('transaction_amount', function ($row) {
                    $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                    $symbol = $currencies[$row->transaction_currency] ?? '';
                    return $symbol . formatCurrency($row->transaction_amount);
                })
                ->addColumn('payment_status', function ($row) {
                    $status = '';
                    if ($row->payment_status == 'SUCCESS') {
                        $status = '<span class="badge bg-green text-white">' . __('Paid') . '</span>';
                    } elseif ($row->payment_status == 'FAILED') {
                        $status = '<span class="badge bg-red text-white">' . __('Failed') . '</span>';
                    } elseif ($row->payment_status == 'PENDING') {
                        $status = '<span class="badge bg-orange text-white">' . __('Pending') . '</span>';
                    } elseif ($row->payment_status == 'CANCELLED') {
                        $status = '<span class="badge bg-red text-white">' . __('Cancelled') . '</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="dropdown">
                                    <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                    <div class="actions dropdown-menu dropdown-menu-end">';
                    if ($row->payment_status == "SUCCESS") {
                        $actions .= '<a class="dropdown-item" target="_blank" href="' . route('admin.view.invoice', ['id' => $row->gobiz_transaction_id]) . '">' . __('Invoice') . '</a>';
                    }
                    if ($row->payment_status != "SUCCESS") {
                        $actions .= '<a class="dropdown-item" href="' . route('admin.trans.status', ['id' => $row->gobiz_transaction_id, 'status' => 'PENDING']) . '">' . __('Pending') . '</a>';
                        $actions .= '<a class="dropdown-item" href="#" onclick="getTransaction(\'' . $row->gobiz_transaction_id . '\'); return false;">' . __('Success') . '</a>';
                        $actions .= '<a class="dropdown-item" href="' . route('admin.trans.status', ['id' => $row->gobiz_transaction_id, 'status' => 'FAILED']) . '">' . __('Failed') . '</a>';
                    }
                    $actions .= '</div></div>';
                    return $actions;
                })
                ->rawColumns(['user', 'payment_status', 'action'])
                ->make(true);
        }

        $settings = Setting::where('status', 1)->first();
        $currencies = Currency::get();

        return view('admin.pages.transactions.online-unpaid', compact('settings', 'currencies'));
    }

    // Update transaction status
    public function transactionStatus(Request $request, $id, $status)
    {
        // Transaction details
        $transaction_details = Transaction::where('gobiz_transaction_id', $id)->where('status', 1)->first();
        $user_details = User::find($transaction_details->user_id);

        // Success to failed or pending
        if ($transaction_details->payment_status == "SUCCESS" && $status != "SUCCESS") {
            // Update transaction status
            Transaction::where('gobiz_transaction_id', $id)->update([
                'invoice_prefix' => null,
                'invoice_number' => null,
                'payment_status' => $status,
            ]);
        }

        // If offline status is "SUCCESS"
        if ($status == "SUCCESS") {

            // Get config details
            $config = DB::table('config')->get();

            // Get plan validity
            $plan_data = Plan::where('plan_id', $transaction_details->plan_id)->first();
            $term_days = (int) $plan_data->validity;

            // Customer plan validity
            if ($user_details->plan_validity == "") {
                // Add days
                if ($term_days == "9999") {
                    $plan_validity = "2050-12-30 23:23:59";
                } else {
                    $plan_validity = Carbon::now();
                    $plan_validity->addDays($term_days);
                }

                // Invoice count generate
                $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                $invoice_number = $invoice_count + 1;

                // Update transaction status
                Transaction::where('gobiz_transaction_id', $id)->update([
                    'invoice_prefix' => $config[15]->config_value,
                    'invoice_number' => $invoice_number,
                    'payment_status' => 'SUCCESS',
                ]);

                // Update customer plan details
                if ($user_details) {
                    $user_details->plan_id              = $transaction_details->plan_id;
                    $user_details->term                 = $term_days;
                    $user_details->plan_validity        = $plan_validity;
                    $user_details->plan_activation_date = now();
                    $user_details->plan_details         = $plan_data;
    
                    $user_details->save();
                }

                if ($config[80]->config_value == '1') {
                    // Referral amount details
                    $referralCalculation = [];
                    $referralCalculation['referral_type'] = $config[81]->config_value;
                    $referralCalculation['referral_value'] = $config[82]->config_value;

                    // Check referral_type is percentage or amount
                    if ($config[81]->config_value == '0') {
                        // Plan amount
                        $base_amount = (float) $plan_data->plan_price;
                        
                        $referralCalculation['referral_amount'] = ($base_amount * $referralCalculation['referral_value']) / 100;
                    } else {
                        $referralCalculation['referral_amount'] = $referralCalculation['referral_value'];
                    }

                    // Update referral details
                    Referral::where('user_id', Auth::user()->user_id)->update([
                        'is_subscribed' => 1,
                        'referral_scheme' => json_encode($referralCalculation),
                    ]);
                }

                // Save applied coupon
                AppliedCoupon::where('transaction_id', $transaction_details->transaction_id)->update([
                    'status' => 1
                ]);

                // Generate invoice to customer
                $encode = json_decode($transaction_details['invoice_details'], true);
                $details = [
                    'from_billing_name' => $encode['from_billing_name'],
                    'from_billing_email' => $encode['from_billing_email'],
                    'from_billing_address' => $encode['from_billing_address'],
                    'from_billing_city' => $encode['from_billing_city'],
                    'from_billing_state' => $encode['from_billing_state'],
                    'from_billing_country' => $encode['from_billing_country'],
                    'from_billing_zipcode' => $encode['from_billing_zipcode'],
                    'gobiz_transaction_id' => $transaction_details->gobiz_transaction_id,
                    'to_billing_name' => $encode['to_billing_name'],
                    'invoice_currency' => $transaction_details->transaction_currency,
                    'subtotal' => $encode['subtotal'],
                    'tax_amount' => (float)($plan_data->plan_price) * (float)($config[25]->config_value) / 100,
                    'applied_coupon' => $encode['applied_coupon'],
                    'discounted_price' => $encode['discounted_price'],
                    'invoice_amount' => $encode['invoice_amount'],
                    'invoice_id' => $config[15]->config_value . $invoice_number,
                    'invoice_date' => $transaction_details->created_at,
                    'description' => $transaction_details->description,
                    'email_heading' => $config[27]->config_value,
                    'email_footer' => $config[28]->config_value,
                ];

                // Send email to customer
                try {
                    Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
                } catch (\Exception $e) {
                }

                // Return and alert
                return redirect()->back()->with('success', trans('Plan activation success!'));
            } else {
                $message = "";
                if ($user_details->plan_id == $transaction_details->plan_id) {


                    // Check if plan validity is expired or not.
                    $plan_validity = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $user_details->plan_validity);
                    $current_date = Carbon::now();
                    $remaining_days = $current_date->diffInDays($plan_validity, false);

                    if ($remaining_days > 0) {
                        // Add days
                        if ($term_days == "9999") {
                            $plan_validity = "2050-12-30 23:23:59";
                            $message = trans("Plan activated successfully!");
                        } else {
                            $plan_validity = Carbon::parse($user_details->plan_validity);
                            $plan_validity->addDays($term_days);
                            $message = trans("Plan activated successfully!");
                        }
                    } else {
                        // Add days
                        if ($term_days == "9999") {
                            $plan_validity = "2050-12-30 23:23:59";
                            $message = trans("Plan activated successfully!");
                        } else {
                            $plan_validity = Carbon::now();
                            $plan_validity->addDays($term_days);
                            $message = trans("Plan activated successfully!");
                        }
                    }
                } else {

                    // Making all cards inactive, For Plan change
                    BusinessCard::where('user_id', $user_details->user_id)->update([
                        'card_status' => 'inactive',
                    ]);

                    // Add days
                    if ($term_days == "9999") {
                        $plan_validity = "2050-12-30 23:23:59";
                        $message = trans("Plan activated successfully!");
                    } else {
                        $plan_validity = Carbon::now();
                        $plan_validity->addDays($term_days);
                        $message = trans("Plan activated successfully!");
                    }
                }

                $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                $invoice_number = $invoice_count + 1;

                Transaction::where('gobiz_transaction_id', $id)->update([
                    'invoice_prefix' => $config[15]->config_value,
                    'invoice_number' => $invoice_number,
                    'payment_status' => 'SUCCESS',
                ]);

                if ($user_details) {
                    $user_details->plan_id              = $transaction_details->plan_id;
                    $user_details->term                 = $term_days;
                    $user_details->plan_validity        = $plan_validity;
                    $user_details->plan_activation_date = now();
                    $user_details->plan_details         = $plan_data;
    
                    $user_details->save();
                }

                // Save applied coupon
                AppliedCoupon::where('transaction_id', $transaction_details->transaction_id)->update([
                    'status' => 1
                ]);

                $encode = json_decode($transaction_details['invoice_details'], true);
                $details = [
                    'from_billing_name' => $encode['from_billing_name'],
                    'from_billing_email' => $encode['from_billing_email'],
                    'from_billing_address' => $encode['from_billing_address'],
                    'from_billing_city' => $encode['from_billing_city'],
                    'from_billing_state' => $encode['from_billing_state'],
                    'from_billing_country' => $encode['from_billing_country'],
                    'from_billing_zipcode' => $encode['from_billing_zipcode'],
                    'gobiz_transaction_id' => $transaction_details->gobiz_transaction_id,
                    'to_billing_name' => $encode['to_billing_name'],
                    'invoice_currency' => $transaction_details->transaction_currency,
                    'subtotal' => $encode['subtotal'],
                    'tax_amount' => (float)($plan_data->plan_price) * (float)($config[25]->config_value) / 100,
                    'applied_coupon' => $encode['applied_coupon'],
                    'discounted_price' => $encode['discounted_price'],
                    'invoice_amount' => $encode['invoice_amount'],
                    'invoice_id' => $config[15]->config_value . $invoice_number,
                    'invoice_date' => $transaction_details->created_at,
                    'description' => $transaction_details->description,
                    'email_heading' => $config[27]->config_value,
                    'email_footer' => $config[28]->config_value, 
                ];

                try {
                    Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
                } catch (\Exception $e) {
                }

                return redirect()->back()->with('success', trans($message));
            }
        } else {
            Transaction::where('gobiz_transaction_id', $id)->update([
                'payment_status' => $status,
            ]);

            return redirect()->back()->with('success', trans('Updated!'));
        }
    }

    // View invoice
    public function viewInvoice($id)
    {
        // Queries
        $transaction = Transaction::where('gobiz_transaction_id', $id)->orWhere('transaction_id', $id)->where('payment_status', 'SUCCESS')->first();

        if ($transaction) {
            $settings = Setting::where('status', 1)->first();
            $config = DB::table('config')->get();
            $currencies = Currency::get();
            $transaction['billing_details'] = json_decode($transaction['invoice_details'], true);

            // View
            return view('admin.pages.transactions.view-invoice', compact('transaction', 'settings', 'config', 'currencies'));
        } else {
            return back()->with('failed', trans('Not Found!'));
        }
    }

    // All offline paid transactions
    public function offlinePaidTransactions(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::where('payment_gateway_name', 'Offline')->where('payment_status', 'SUCCESS')->orderBy('id', 'desc')->get();

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return formatDateForUser($row->created_at);
                })
                ->addColumn('gobiz_transaction_id', function ($row) {
                    return $row->gobiz_transaction_id;
                })
                ->addColumn('transaction_id', function ($row) {
                    return $row->transaction_id != null ? $row->transaction_id : '-';
                })
                ->addColumn('user', function ($row) {
                    $user_details = User::where('id', $row->user_id)->first();
                    if ($user_details) {
                        return '<a href="' . route('admin.view.user', $user_details->user_id) . '">' . $user_details->name . '</a>';
                    } else {
                        return '<a href="#">' . __("Customer not available") . '</a>';
                    }
                })
                ->addColumn('payment_gateway_name', function ($row) {
                    return __($row->payment_gateway_name);
                })
                ->addColumn('transaction_amount', function ($row) {
                    $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                    $symbol = $currencies[$row->transaction_currency] ?? '';
                    return $symbol . formatCurrency($row->transaction_amount);
                })
                ->addColumn('payment_status', function ($row) {
                    $status = '';
                    if ($row->payment_status == 'SUCCESS') {
                        $status = '<span class="badge bg-green text-white">' . __('Paid') . '</span>';
                    } elseif ($row->payment_status == 'FAILED') {
                        $status = '<span class="badge bg-red text-white">' . __('Failed') . '</span>';
                    } elseif ($row->payment_status == 'PENDING') {
                        $status = '<span class="badge bg-orange text-white">' . __('Pending') . '</span>';
                    } elseif ($row->payment_status == 'CANCELLED') {
                        $status = '<span class="badge bg-red text-white">' . __('Cancelled') . '</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="dropdown">
                                    <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                    <div class="actions dropdown-menu dropdown-menu-end">';
                    if ($row->invoice_number > 0) {
                        $actions .= '<a class="dropdown-item" target="_blank" href="' . route('admin.view.invoice', ['id' => $row->gobiz_transaction_id]) . '">' . __('Invoice') . '</a>';
                    }
                    if ($row->payment_status != "SUCCESS") {
                        $actions .= '<a class="dropdown-item" href="#" onclick="getOfflineTransaction(\'' . $row->gobiz_transaction_id . '\'); return false;">' . __('Success') . '</a>';
                        $actions .= '<a class="dropdown-item" href="' . route('admin.offline.trans.status', ['id' => $row->gobiz_transaction_id, 'status' => 'PENDING']) . '">' . __('Pending') . '</a>';
                        $actions .= '<a class="dropdown-item" href="' . route('admin.offline.trans.status', ['id' => $row->gobiz_transaction_id, 'status' => 'FAILED']) . '">' . __('Failed') . '</a>';
                    }
                    $actions .= '</div></div>';
                    return $actions;
                })
                ->rawColumns(['user', 'payment_status', 'action'])
                ->make(true);
        }

        $settings = Setting::where('status', 1)->first();
        $currencies = Currency::get();

        return view('admin.pages.transactions.offline-paid', compact('settings', 'currencies'));
    }

    // All offline unpaid transactions
    public function offlineUnpaidTransactions(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::where('payment_gateway_name', 'Offline')->where('payment_status', '!=', 'SUCCESS')->orderBy('id', 'desc')->get();

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return formatDateForUser($row->created_at);
                })
                ->addColumn('gobiz_transaction_id', function ($row) {
                    return $row->gobiz_transaction_id;
                })
                ->addColumn('transaction_id', function ($row) {
                    return $row->transaction_id != null ? $row->transaction_id : '-';
                })
                ->addColumn('user', function ($row) {
                    $user_details = User::where('id', $row->user_id)->first();
                    if ($user_details) {
                        return '<a href="' . route('admin.view.user', $user_details->user_id) . '">' . $user_details->name . '</a>';
                    } else {
                        return '<a href="#">' . __("Customer not available") . '</a>';
                    }
                })
                ->addColumn('payment_gateway_name', function ($row) {
                    return __($row->payment_gateway_name);
                })
                ->addColumn('transaction_amount', function ($row) {
                    $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                    $symbol = $currencies[$row->transaction_currency] ?? '';
                    return $symbol . formatCurrency($row->transaction_amount);
                })
                ->addColumn('payment_status', function ($row) {
                    $status = '';
                    if ($row->payment_status == 'SUCCESS') {
                        $status = '<span class="badge bg-green text-white">' . __('Paid') . '</span>';
                    } elseif ($row->payment_status == 'FAILED') {
                        $status = '<span class="badge bg-red text-white">' . __('Failed') . '</span>';
                    } elseif ($row->payment_status == 'PENDING') {
                        $status = '<span class="badge bg-orange text-white">' . __('Pending') . '</span>';
                    } elseif ($row->payment_status == 'CANCELLED') {
                        $status = '<span class="badge bg-red text-white">' . __('Cancelled') . '</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="dropdown">
                                    <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                    <div class="actions dropdown-menu dropdown-menu-end">';
                    if ($row->invoice_number > 0) {
                        $actions .= '<a class="dropdown-item" target="_blank" href="' . route('admin.view.invoice', ['id' => $row->gobiz_transaction_id]) . '">' . __('Invoice') . '</a>';
                    }
                    if ($row->payment_status != "SUCCESS") {
                        $actions .= '<a class="dropdown-item" href="#" onclick="getOfflineTransaction(\'' . $row->gobiz_transaction_id . '\'); return false;">' . __('Success') . '</a>';
                        $actions .= '<a class="dropdown-item" href="' . route('admin.offline.trans.status', ['id' => $row->gobiz_transaction_id, 'status' => 'PENDING']) . '">' . __('Pending') . '</a>';
                        $actions .= '<a class="dropdown-item" href="' . route('admin.offline.trans.status', ['id' => $row->gobiz_transaction_id, 'status' => 'FAILED']) . '">' . __('Failed') . '</a>';
                    }
                    $actions .= '</div></div>';
                    return $actions;
                })
                ->rawColumns(['user', 'payment_status', 'action'])
                ->make(true);
        }

        $settings = Setting::where('status', 1)->first();
        $currencies = Currency::get();

        return view('admin.pages.transactions.offline-unpaid', compact('settings', 'currencies'));
    }

    // Offline
    public function offlineTransactionStatus(Request $request, $id, $status)
    {
        // Transaction details
        $transaction_details = Transaction::where('gobiz_transaction_id', $id)->where('status', 1)->first();
        $user_details = User::find($transaction_details->user_id);

        // Success to failed or pending
        if ($transaction_details->payment_status == "SUCCESS" && $status != "SUCCESS") {
            // Update transaction status
            Transaction::where('gobiz_transaction_id', $id)->update([
                'invoice_prefix' => null,
                'invoice_number' => null,
                'payment_status' => $status,
            ]);
        }

        // If offline status is "SUCCESS"
        if ($status == "SUCCESS") {

            // Get config details
            $config = DB::table('config')->get();

            // Transaction details
            $transaction_details = Transaction::where('gobiz_transaction_id', $id)->where('status', 1)->first();
            $user_details = User::find($transaction_details->user_id);

            // Get plan validity
            $plan_data = Plan::where('plan_id', $transaction_details->plan_id)->first();
            $term_days = (int) $plan_data->validity;

            // Customer plan validity
            if ($user_details->plan_validity == "") {
                // Add days
                if ($term_days == "9999") {
                    $plan_validity = "2050-12-30 23:23:59";
                } else {
                    $plan_validity = Carbon::now();
                    $plan_validity->addDays($term_days);
                }

                // Invoice count generate
                $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                $invoice_number = $invoice_count + 1;

                // Update transaction status
                Transaction::where('gobiz_transaction_id', $id)->update([
                    'invoice_prefix' => $config[15]->config_value,
                    'invoice_number' => $invoice_number,
                    'payment_status' => 'SUCCESS',
                ]);

                // Update customer plan details
                if ($user_details) {
                    $user_details->plan_id              = $transaction_details->plan_id;
                    $user_details->term                 = $term_days;
                    $user_details->plan_validity        = $plan_validity;
                    $user_details->plan_activation_date = now();
                    $user_details->plan_details         = $plan_data;
    
                    $user_details->save();
                }

                if ($config[80]->config_value == '1') {
                    // Referral amount details
                    $referralCalculation = [];
                    $referralCalculation['referral_type'] = $config[81]->config_value;
                    $referralCalculation['referral_value'] = $config[82]->config_value;

                    // Check referral_type is percentage or amount
                    if ($config[81]->config_value == '0') {
                        // Plan amount
                        $base_amount = (float) $plan_data->plan_price;
                        
                        $referralCalculation['referral_amount'] = ($base_amount * $referralCalculation['referral_value']) / 100;
                    } else {
                        $referralCalculation['referral_amount'] = $referralCalculation['referral_value'];
                    }

                    // Update referral details
                    Referral::where('user_id', Auth::user()->user_id)->update([
                        'is_subscribed' => 1,
                        'referral_scheme' => json_encode($referralCalculation),
                    ]);
                }

                // Save applied coupon
                AppliedCoupon::where('transaction_id', $transaction_details->transaction_id)->update([
                    'status' => 1
                ]);

                // Generate invoice to customer
                $encode = json_decode($transaction_details['invoice_details'], true);
                $details = [
                    'from_billing_name' => $encode['from_billing_name'],
                    'from_billing_email' => $encode['from_billing_email'],
                    'from_billing_address' => $encode['from_billing_address'],
                    'from_billing_city' => $encode['from_billing_city'],
                    'from_billing_state' => $encode['from_billing_state'],
                    'from_billing_country' => $encode['from_billing_country'],
                    'from_billing_zipcode' => $encode['from_billing_zipcode'],
                    'gobiz_transaction_id' => $transaction_details->gobiz_transaction_id,
                    'to_billing_name' => $encode['to_billing_name'],
                    'invoice_currency' => $transaction_details->transaction_currency,
                    'subtotal' => $encode['subtotal'],
                    'tax_amount' => (float)($plan_data->plan_price) * (float)($config[25]->config_value) / 100,
                    'applied_coupon' => $encode['applied_coupon'],
                    'discounted_price' => $encode['discounted_price'],
                    'invoice_amount' => $encode['invoice_amount'],
                    'invoice_id' => $config[15]->config_value . $invoice_number,
                    'invoice_date' => $transaction_details->created_at,
                    'description' => $transaction_details->description,
                    'email_heading' => $config[27]->config_value,
                    'email_footer' => $config[28]->config_value,
                ];

                // Send email to customer
                try {
                    Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
                } catch (\Exception $e) {
                }

                // Return and alert
                return redirect()->back()->with('success', trans('Plan activation success!'));
            } else {
                $message = "";
                if ($user_details->plan_id == $transaction_details->plan_id) {


                    // Check if plan validity is expired or not.
                    $plan_validity = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $user_details->plan_validity);
                    $current_date = Carbon::now();
                    $remaining_days = $current_date->diffInDays($plan_validity, false);

                    if ($remaining_days > 0) {
                        // Add days
                        if ($term_days == "9999") {
                            $plan_validity = "2050-12-30 23:23:59";
                            $message = trans("Plan activated successfully!");
                        } else {
                            $plan_validity = Carbon::parse($user_details->plan_validity);
                            $plan_validity->addDays($term_days);
                            $message = trans("Plan activated successfully!");
                        }
                    } else {
                        // Add days
                        if ($term_days == "9999") {
                            $plan_validity = "2050-12-30 23:23:59";
                            $message = trans("Plan activated successfully!");
                        } else {
                            $plan_validity = Carbon::now();
                            $plan_validity->addDays($term_days);
                            $message = trans("Plan activated successfully!");
                        }
                    }
                } else {

                    // Making all cards inactive, For Plan change
                    BusinessCard::where('user_id', $user_details->user_id)->update([
                        'card_status' => 'inactive',
                    ]);

                    // Add days
                    if ($term_days == "9999") {
                        $plan_validity = "2050-12-30 23:23:59";
                        $message = trans("Plan activated successfully!");
                    } else {
                        $plan_validity = Carbon::now();
                        $plan_validity->addDays($term_days);
                        $message = trans("Plan activated successfully!");
                    }
                }

                $invoice_count = Transaction::where("invoice_prefix", $config[15]->config_value)->count();
                $invoice_number = $invoice_count + 1;

                Transaction::where('gobiz_transaction_id', $id)->update([
                    'invoice_prefix' => $config[15]->config_value,
                    'invoice_number' => $invoice_number,
                    'payment_status' => 'SUCCESS',
                ]);

                if ($user_details) {
                    $user_details->plan_id              = $transaction_details->plan_id;
                    $user_details->term                 = $term_days;
                    $user_details->plan_validity        = $plan_validity;
                    $user_details->plan_activation_date = now();
                    $user_details->plan_details         = $plan_data;
    
                    $user_details->save();
                }

                // Save applied coupon
                AppliedCoupon::where('transaction_id', $transaction_details->transaction_id)->update([
                    'status' => 1
                ]);

                $encode = json_decode($transaction_details['invoice_details'], true);
                $details = [
                    'from_billing_name' => $encode['from_billing_name'],
                    'from_billing_email' => $encode['from_billing_email'],
                    'from_billing_address' => $encode['from_billing_address'],
                    'from_billing_city' => $encode['from_billing_city'],
                    'from_billing_state' => $encode['from_billing_state'],
                    'from_billing_country' => $encode['from_billing_country'],
                    'from_billing_zipcode' => $encode['from_billing_zipcode'],
                    'gobiz_transaction_id' => $transaction_details->gobiz_transaction_id,
                    'to_billing_name' => $encode['to_billing_name'],
                    'invoice_currency' => $transaction_details->transaction_currency,
                    'subtotal' => $encode['subtotal'],
                    'tax_amount' => (float)($plan_data->plan_price) * (float)($config[25]->config_value) / 100,
                    'applied_coupon' => $encode['applied_coupon'],
                    'discounted_price' => $encode['discounted_price'],
                    'invoice_amount' => $encode['invoice_amount'],
                    'invoice_id' => $config[15]->config_value . $invoice_number,
                    'invoice_date' => $transaction_details->created_at,
                    'description' => $transaction_details->description,
                    'email_heading' => $config[27]->config_value,
                    'email_footer' => $config[28]->config_value,
                ];

                try {
                    Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
                } catch (\Exception $e) {
                }

                return redirect()->back()->with('success', trans($message));
            }
        } else {
            Transaction::where('gobiz_transaction_id', $id)->update([
                'payment_status' => $status,
            ]);

            return redirect()->back()->with('success', trans('Updated!'));
        }
    }
}
