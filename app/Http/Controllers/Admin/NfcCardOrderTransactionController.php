<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use App\Currency;
use App\NfcCardKey;
use App\NfcCardOrder;
use App\AppliedCoupon;
use App\NfcCardDesign;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\NfcCardOrderTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class NfcCardOrderTransactionController extends Controller
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
     * Show the transactions.
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

        // Queries
        if ($request->ajax()) {
            // Joins "nfc_card_orders", "nfc_card_order_transactions" and "nfc_card_designs" tables
            $transactions = NfcCardOrderTransaction::join('nfc_card_orders', 'nfc_card_order_transactions.nfc_card_order_id', '=', 'nfc_card_orders.nfc_card_order_id')->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
                ->join('users', 'nfc_card_orders.user_id', '=', 'users.id')
                ->select('nfc_card_order_transactions.*', 'users.*', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at')
                ->where('nfc_card_order_transactions.status', '!=', 2)
                ->orderBy('nfc_card_order_transactions.created_at', 'desc')
                ->get();

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->editColumn('created_at', function ($transaction) {
                    return formatDateForUser($transaction->created_at);
                })
                ->editColumn('nfc_card_order_transaction_id', function ($transaction) {
                    if ($transaction->payment_status == 'success') {
                        return '<a class="fw-bold" href="' . route('admin.transaction.show', $transaction->nfc_card_order_transaction_id) . '" target="_blank">' . $transaction->nfc_card_order_transaction_id . '</a>';
                    } else {
                        return '<span class="fw-bold">' . $transaction->nfc_card_order_transaction_id . '</span>';
                    }
                })
                ->editColumn('payment_transaction_id', function ($transaction) {
                    if ($transaction->payment_status == 'success') {
                        return '<a class="fw-bold" href="' . route('admin.transaction.show', $transaction->payment_transaction_id) . '" target="_blank">' . $transaction->payment_transaction_id . '</a>';
                    } else {
                        return '<span class="fw-bold">' . $transaction->payment_transaction_id . '</span>';
                    }
                })
                ->editColumn('nfc_card_order_id', function ($transaction) {
                    return '<a class="fw-bold" href="' . route('admin.order.show', $transaction->nfc_card_order_id) . '" target="_blank">' . $transaction->nfc_card_order_id . '</a>';
                })
                ->editColumn('user_id', function ($transaction) {
                    return '<a class="fw-bold" href="' . route('admin.view.customer', $transaction->user_id) . '" target="_blank">' . trans($transaction->name) . '</a>';
                })
                ->editColumn('payment_method', function ($transaction) {
                    return '<span class="fw-bold">' . trans($transaction->payment_method) . '</span>';
                })
                ->editColumn('amount', function ($transaction) {
                    // Get config
                    $data = DB::table('config')->get();
                    $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                    $symbol = $currencies[$data[1]->config_value] ?? '';

                    return '<span class="fw-bold">' . $symbol . $transaction->amount . '</span>';
                })
                ->editColumn('payment_status', function ($transaction) {
                    if ($transaction->payment_status == 'pending') {
                        return '<span class="badge bg-warning text-white">' . __('Pending') . '</span>';
                    } elseif ($transaction->payment_status == 'success') {
                        return '<span class="badge bg-success text-white">' . __('Success') . '</span>';
                    } else {
                        return '<span class="badge bg-danger text-white">' . __('Failed') . '</span>';
                    }
                })
                ->editColumn('action', function ($transaction) {

                    $actionBtn = '';

                    // Show
                    if ($transaction->payment_status == 'success') {
                        $actionBtn .= '<a href="' . route('admin.transaction.show', $transaction->nfc_card_order_transaction_id) . '" target="_blank" class="dropdown-item">' . __('Invoice') . '</a>';
                    }

                    // Update payment status
                    if ($transaction->payment_status == 'pending') {
                        $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $transaction->nfc_card_order_transaction_id . '\', `success`); return false;" class="dropdown-item">' . __('Complete payment') . '</a>';
                        $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $transaction->nfc_card_order_transaction_id . '\', `failed`); return false;" class="dropdown-item">' . __('Failed payment') . '</a>';
                    } elseif ($transaction->payment_status == 'failed') {
                        $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $transaction->nfc_card_order_transaction_id . '\', `pending`); return false;" class="dropdown-item">' . __('Pending payment') . '</a>';
                        $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $transaction->nfc_card_order_transaction_id . '\', `success`); return false;" class="dropdown-item">' . __('Complete payment') . '</a>';
                    } else {
                        $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $transaction->nfc_card_order_transaction_id . '\', `pending`); return false;" class="dropdown-item">' . __('Pending payment') . '</a>';
                        $actionBtn .= '<a href="#" onclick="updateStatus(\'' . $transaction->nfc_card_order_transaction_id . '\', `failed`); return false;" class="dropdown-item">' . __('Failed payment') . '</a>';
                    }

                    return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <div class="nav-item dropdown">
                                    ' . $actionBtn . '
                                </div>
                            </div>';
                })
                ->rawColumns(['created_at', 'nfc_card_order_transaction_id', 'payment_transaction_id', 'nfc_card_order_id', 'user_id', 'payment_method', 'amount', 'payment_status', 'action'])
                ->make(true);
        }

        return view('admin.pages.nfc-card-order-transactions.index', compact('settings', 'config'));
    }

    /**
     * Show the transaction.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($transaction)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();
        $currencies = Currency::get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Joins "nfc_card_orders", "nfc_card_order_transactions" and "nfc_card_designs" tables
        $transaction_details = NfcCardOrderTransaction::join('nfc_card_orders', 'nfc_card_order_transactions.nfc_card_order_id', '=', 'nfc_card_orders.nfc_card_order_id')
            ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
            ->join('users', 'nfc_card_orders.user_id', '=', 'users.id')
            ->select('nfc_card_order_transactions.*', 'users.*', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at')
            ->where('nfc_card_order_transactions.nfc_card_order_transaction_id', $transaction)->orWhere('nfc_card_order_transactions.payment_transaction_id', $transaction)
            ->first();

        // Check if the transaction exists
        if (!$transaction_details) {
            return redirect()->route('admin.transactions')->with('failed', trans('Not Found!'));
        }

        return view('admin.pages.nfc-card-order-transactions.show', compact('transaction_details', 'currencies', 'settings', 'config'));
    }

    /**
     * Update the transaction.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request, NfcCardOrderTransaction $transaction)
    {
        // Validation
        $request->validate([
            'status' => 'required|in:pending,completed,failed',
            'transaction_id' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric',
        ]);

        // Update transaction
        $transaction->update([
            'status' => $request->status,
            'transaction_id' => $request->transaction_id,
            'amount' => $request->amount,
        ]);

        return redirect()->route('admin.pages.transactions')->with('success', trans('Updated!'));
    }

    /**
     * Action the transaction.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function action(Request $request)
    {
        // Parameters
        $transaction = $request->query('id');
        $status = $request->query('status');

        // Queries
        $config = DB::table('config')->get();

        // Transaction details
        $transaction_details = NfcCardOrderTransaction::where('nfc_card_order_transaction_id', $transaction)->orWhere('payment_transaction_id', $transaction)->first();

        // Check if the transaction exists
        if (!$transaction_details) {
            return redirect()->route('admin.transactions')->with('failed', trans('Not Found!'));
        }

        // Get order details
        $order_details = NfcCardOrder::where('nfc_card_order_id', $transaction_details->nfc_card_order_id)->first();

        switch ($status) {
            case 'success':
                $invoice_count = NfcCardOrderTransaction::where("invoice_prefix", $config[15]->config_value)->count();
                $invoice_number = $invoice_count + 1;

                // Update nfc card order transaction details
                if ($transaction_details) {
                    $transaction_details->invoice_prefix = $config[15]->config_value;
                    $transaction_details->invoice_number = $invoice_number;
                    $transaction_details->payment_status = 'success';
                    $transaction_details->save();
                }

                // Update nfc card order details
                NfcCardOrder::where('nfc_card_order_id', $transaction_details->nfc_card_order_id)->update([
                    'order_status' => 'processing',
                ]);

                // Save applied coupon
                AppliedCoupon::where('transaction_id', $transaction_details->payment_transaction_id)->update([
                    'status' => 1
                ]);

                // Get NFC card order details
                $nfcCardOrderDetails = NfcCardOrder::where('nfc_card_order_id', $transaction_details->nfc_card_order_id)->first();

                // Reduce NFC card stock
                if ($order_details->order_status != 'processing') {
                    $nfcCardDesign = NfcCardDesign::where('nfc_card_id', $nfcCardOrderDetails->nfc_card_id)->first();
                    $nfcCardDesign->available_stocks = (int) $nfcCardDesign->available_stocks - 1;
                    $nfcCardDesign->save();
                }

                // Generate NFC card key
                $unqiueKey = Str::random(25);

                // Generate NFC card key
                $nfcCardKey = new NfcCardKey();
                $nfcCardKey->nfc_card_key_id = uniqid();
                $nfcCardKey->key_type = 'online';
                $nfcCardKey->unqiue_key = $unqiueKey;
                $nfcCardKey->save();

                // Update nfc card order details
                $orderDetails = json_decode($nfcCardOrderDetails->order_details, true) ?? []; // Ensure it's an array
                $orderDetails['unique_key'] = $unqiueKey;

                $orderDetails = json_encode($orderDetails);

                NfcCardOrder::where('nfc_card_order_id', $transaction_details->nfc_card_order_id)->update([
                    'order_details' => $orderDetails,
                    'updated_at' => now(),
                ]);

                // Email Message details
                $encode = json_decode($transaction_details['invoice_details'], true);
                $itemDetails = json_decode($nfcCardOrderDetails['order_details'], true);

                $details = [
                    'from_billing_name' => $encode['from_billing_name'],
                    'from_billing_email' => $encode['from_billing_email'],
                    'from_billing_address' => $encode['from_billing_address'],
                    'from_billing_city' => $encode['from_billing_city'],
                    'from_billing_state' => $encode['from_billing_state'],
                    'from_billing_country' => $encode['from_billing_country'],
                    'from_billing_zipcode' => $encode['from_billing_zipcode'],
                    'gobiz_transaction_id' => $transaction_details->payment_transaction_id,
                    'to_billing_name' => $encode['to_billing_name'],
                    'invoice_currency' => $transaction_details->currency,
                    'subtotal' => $encode['subtotal'],
                    'tax_amount' => (float)($itemDetails['price']) * (float)($config[25]->config_value) / 100,
                    'applied_coupon' => $encode['applied_coupon'],
                    'discounted_price' => $encode['discounted_price'],
                    'invoice_amount' => $encode['invoice_amount'],
                    'invoice_id' => $config[15]->config_value . $invoice_number,
                    'invoice_date' => $transaction_details->created_at,
                    'description' => $itemDetails['order_item'],
                    'email_heading' => $config[27]->config_value,
                    'email_footer' => $config[28]->config_value,
                ];

                try {
                    Mail::to($encode['to_billing_email'])->send(new \App\Mail\SendEmailInvoice($details));
                } catch (\Exception $e) {
                }

                break;
            case 'failed':
                // Update transaction
                $transaction_details->update([
                    'payment_status' => 'failed',
                ]);

                // Update order status
                $order_details->update([
                    'order_status' => 'cancelled',
                ]);   
                
                // Check order_status is "processing"
                if ($order_details->order_status != 'processing') {
                    // Reduce NFC card stock
                    $nfcCardDesign = NfcCardDesign::where('nfc_card_id', $order_details->nfc_card_id)->first();
                    $nfcCardDesign->available_stocks = (int) $nfcCardDesign->available_stocks + 1;
                    $nfcCardDesign->save();
                }

                break;
            default:

                // Update transaction
                $transaction_details->update([
                    'payment_status' => 'pending',
                ]);

                // Update order status
                $order_details->update([
                    'order_status' => 'pending',
                ]);

                // Check order_status is "processing"
                if ($order_details->order_status != 'processing') {
                    // Reduce NFC card stock
                    $nfcCardDesign = NfcCardDesign::where('nfc_card_id', $order_details->nfc_card_id)->first();
                    $nfcCardDesign->available_stocks = (int) $nfcCardDesign->available_stocks + 1;
                    $nfcCardDesign->save();
                }

                break;
        }

        return redirect()->route('admin.transactions')->with('success', trans('Updated!'));
    }
}
