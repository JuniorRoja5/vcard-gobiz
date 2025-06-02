<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use App\Currency;
use App\NfcCardKey;
use App\NfcCardOrder;
use App\EmailTemplate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class NfcCardOrderController extends Controller
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
     * Show the orders.
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
            // Joins "nfc_card_orders", "nfc_card_order_transactions" and "nfc_card_designs" tables
            $orders = NfcCardOrder::join('nfc_card_order_transactions', 'nfc_card_orders.nfc_card_order_id', '=', 'nfc_card_order_transactions.nfc_card_order_id')
                ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
                ->select('nfc_card_order_transactions.*', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_orders.nfc_card_logo', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.order_status', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at')
                ->where('nfc_card_order_transactions.status', '!=', 2)
                ->orderBy('nfc_card_order_transactions.created_at', 'desc')
                ->get();

            return DataTables::of($orders)
                ->addIndexColumn()
                ->editColumn('created_at', function ($order) {
                    return formatDateForUser($order->created_at);
                })
                ->editColumn('nfc_card_order_id', function ($order) {
                    return '<a href="' . route('admin.order.show', $order->nfc_card_order_id) . '">' . $order->nfc_card_order_id . '</a>';
                })
                ->editColumn('nfc_card_logo', function ($order) {
                    if (!empty($order->nfc_card_logo)) {
                        return '<a href="' . asset($order->nfc_card_logo) . '" class="fw-bold" data-bs-toggle="tooltip" data-bs-placement="top" title="'. trans('Download NFC Card Logo') .'" download>
                                    <img src="' . asset($order->nfc_card_logo) . '" class="img-fluid rounded" width="50" height="50" alt="NFC Card Logo">
                                </a>';
                    } else {
                        return '-';
                    }
                })
                ->editColumn('nfc_card_name', function ($order) {
                    return '<a href="' . route('admin.edit.design', $order->nfc_card_id) . '">' . trans($order->nfc_card_name) . '</a>';
                })
                ->editColumn('nfc_card_price', function ($order) {
                    // Get config
                    $data = DB::table('config')->get();
                    $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
                    $symbol = $currencies[$data[1]->config_value] ?? '';

                    return '<span class="fw-bold">' . $symbol . $order->amount . '</span>';
                })
                ->editColumn('payment_status', function ($order) {
                    if ($order->payment_status == 'success') {
                        return '<span class="badge bg-success text-white">' . __('Paid') . '</span>';
                    } elseif ($order->payment_status == 'failed') {
                        return '<span class="badge bg-danger text-white">' . __('Failed') . '</span>';
                    } elseif ($order->payment_status == 'pending') {
                        return '<span class="badge bg-orange text-white">' . __('Pending') . '</span>';
                    } elseif ($order->payment_status == 'cancelled') {
                        return '<span class="badge bg-red text-white">' . __('Cancelled') . '</span>';
                    }
                })
                ->editColumn('delivery_status', function ($order) {
                    if ($order->order_status == 'pending') {
                        return '<span class="badge bg-warning text-white">' . __('Pending') . '</span>';
                    } elseif ($order->order_status == 'processing') {
                        return '<span class="badge bg-primary text-white">' . __('Processing') . '</span>';
                    } elseif ($order->order_status == 'out for delivery') {
                        return '<span class="badge bg-dark text-white">' . __('Out for delivery') . '</span>';
                    } elseif ($order->order_status == 'delivered') {
                        return '<span class="badge bg-success text-white">' . __('Delivered') . '</span>';
                    } elseif ($order->order_status == 'cancelled') {
                        return '<span class="badge bg-danger text-white">' . __('Cancelled') . '</span>';
                    } elseif ($order->order_status == 'hold') {
                        return '<span class="badge bg-warning text-white">' . __('Hold') . '</span>';
                    } elseif ($order->order_status == 'shipped') {
                        return '<span class="badge bg-success text-white">' . __('Shipped') . '</span>';
                    }
                })
                ->editColumn('action', function ($order) {
                    // View
                    $actionBtn = '<a href="' . route('admin.order.show', $order->nfc_card_order_id) . '" class="dropdown-item">' . __('View') . '</a>';

                    if($order->payment_status != 'pending' && $order->payment_status != 'failed') {
                        // Write in NFC Card
                        $actionBtn .= '<a href="' . route('admin.write.to.nfc.card', $order->nfc_card_order_id) . '" class="dropdown-item">' . __('Write in NFC Card') . '</a>';

                        // Greeting Letter
                        $actionBtn .= '<a href="' . route('admin.greeting.letter', $order->nfc_card_order_id) . '" class="dropdown-item">' . __('Greeting Letter') . '</a>';

                        // Update status
                        $actionBtn .= '<a href="' . route('admin.update.order', $order->nfc_card_order_id) . '" class="dropdown-item">' . __('Update Status') . '</a>';
                    }
                    return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <div class="nav-item dropdown">
                                    ' . $actionBtn . '
                                </div>
                            </div>';
                })
                ->rawColumns(['created_at', 'nfc_card_order_id', 'nfc_card_logo', 'nfc_card_id', 'nfc_card_name', 'nfc_card_price', 'payment_status', 'delivery_status', 'action'])
                ->make(true);
        }

        return view('admin.pages.nfc-card-order.index', compact('settings', 'config'));
    }

    /**
     * Show the order.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($orderId)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        // Get the order and transaction and user tables (joins)
        $order = NfcCardOrder::join('nfc_card_order_transactions', 'nfc_card_orders.nfc_card_order_id', '=', 'nfc_card_order_transactions.nfc_card_order_id')
            ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
            ->join('users', 'nfc_card_orders.user_id', '=', 'users.id')
            ->select('nfc_card_order_transactions.*', 'users.*', 'nfc_card_orders.order_details', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.delivery_address', 'nfc_card_orders.order_status', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at')
            ->where('nfc_card_order_transactions.status', '!=', 2)
            ->where('nfc_card_orders.nfc_card_order_id', $orderId)
            ->first();

        // Check if the order exists
        if (!$order) {
            return redirect()->route('admin.orders')->with('failed', __('Not Found!'));
        }

        return view('admin.pages.nfc-card-order.show', compact('order', 'settings', 'config', 'symbol'));
    }

    /**
     * Update the order status.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateOrder($orderId)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Check if the order exists
        $order = NfcCardOrder::where('nfc_card_order_id', $orderId)
            ->first();

        // Check if the order exists
        if (!$order) {
            return redirect()->route('admin.orders')->with('failed', __('Not Found!'));
        }

        return view('admin.pages.nfc-card-order.update-status', compact('order', 'settings', 'config'));
    }


    //* Write to NFC Card.
    public function writeToNfcCard(Request $request, $orderId)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Get the order details
        if($request->has('type') && $request->type == 'key') {
            $orderDetails = NfcCardOrder::join('nfc_card_keys', 'nfc_card_orders.order_details->unique_key', '=', 'nfc_card_keys.unqiue_key')
                ->whereJsonContains('nfc_card_orders.order_details->unique_key', $orderId)
                ->where('nfc_card_orders.order_status', '!=', 'pending')
                ->first();
        } else {
            $orderDetails = NfcCardOrder::where('nfc_card_order_id', $orderId)->where('order_status', '!=', 'pending')->first();
        }

        // NFC Card Logo
        $nfcCardLogo = $orderDetails->nfc_card_logo ?? '';

        // Check order details
        if(!$orderDetails) {
            return redirect()->back()->with('failed', trans('Payment not completed!'));
        }

        // Check unique key is exist in order details
        $orderDetails = json_decode($orderDetails->order_details, true);
        $uniqueKey = $orderDetails['unique_key'] ?? '';

        if (!$uniqueKey) {
            // Generate NFC card key
            $unqiueKey = Str::random(25);

            // Generate NFC card key
            $nfcCardKey = new NfcCardKey();
            $nfcCardKey->nfc_card_key_id = uniqid();
            $nfcCardKey->key_type = 'online';
            $nfcCardKey->unqiue_key = $unqiueKey;
            $nfcCardKey->save();

            $orderDetails['unique_key'] = $unqiueKey;
            $orderDetails = json_encode($orderDetails);

            NfcCardOrder::where('nfc_card_order_id', $orderId)->update([
                'order_details' => $orderDetails,
                'updated_at' => now(), 
            ]);
        }

        // Check if the order exists
        if (!$orderDetails) {
            return redirect()->route('admin.orders')->with('failed', __('Not Found!'));
        }

        return view('admin.pages.nfc-card-order.print', compact('orderDetails', 'nfcCardLogo', 'settings', 'config'));
    }


    /**
     * Update the order.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updatedOrder(Request $request)
    {
        // Queries
        $config = DB::table('config')->get();

        // Validation
        $validated = Validator::make($request->all(), [
            'status' => 'required'
        ]);

        // Check validation
        if ($validated->fails()) {
            return redirect()->route('admin.orders')->with('failed', $validated->errors()->first());
        }

        // Update order
        $order = NfcCardOrder::where('nfc_card_order_id', $request->order_id)->first();

        if ($order) {
            // Update order details
            $orderDetails = json_decode($order->order_details, true) ?? []; // Ensure it's an array
            $orderDetails['tracking_number'] = $request->tracking_number ?? '';
            $orderDetails['courier_partner'] = $request->courier_partner ?? '';
            $orderDetails['delivery_message'] = $request->delivery_message ?? '';

            $orderDetails = json_encode($orderDetails);

            // Update order details
            $order->order_details = $orderDetails;
            $order->order_status = $request->status;
            $order->updated_at = now();
            $order->save();

            // Get NFC card order details
            $nfcCardOrderDetails = NfcCardOrder::join('nfc_card_order_transactions', 'nfc_card_orders.nfc_card_order_id', '=', 'nfc_card_order_transactions.nfc_card_order_id')
                ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
                ->join('users', 'nfc_card_orders.user_id', '=', 'users.id')
                ->join('nfc_card_keys', 'nfc_card_orders.order_details->unique_key', '=', 'nfc_card_keys.unqiue_key')
                ->select('nfc_card_order_transactions.*', 'users.*', 'nfc_card_orders.order_details', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.delivery_address', 'nfc_card_orders.order_status', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at', 'nfc_card_order_transactions.invoice_details')
                ->where('nfc_card_orders.nfc_card_order_id', $request->order_id)
                ->first();

            // Email Message details
            $encode = json_decode($nfcCardOrderDetails['invoice_details'], true);
            $itemDetails = json_decode($nfcCardOrderDetails['order_details'], true);

            // Email Message details
            $MailTemplateDetails = EmailTemplate::where('email_template_id', '584922675210')->first();

            // Currency symbol
            $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
            $symbol = $currencies[$config[1]->config_value] ?? '';

            $details = [
                'emailSubject' => $MailTemplateDetails->email_template_subject,
                'emailContent' => $MailTemplateDetails->email_template_content,
                'orderid' => $nfcCardOrderDetails->nfc_card_order_id,
                'cardname' => $itemDetails['order_item'],
                'cardprice' => $itemDetails['price'],
                'paymentstatus' => $nfcCardOrderDetails->payment_status,
                'deliverystatus' => 'processing',
                'quantity' => 1,
                'trackingnumber' => $itemDetails['tracking_number'] ?? '-',
                'courierpartner' => $itemDetails['courier_partner'] ?? '-',
                'orderpageurl' => route('user.order.nfc.card.view', $nfcCardOrderDetails->nfc_card_order_id),
                'totalprice' => $symbol . $encode['invoice_amount'],
                'supportemail' => $encode['from_billing_email'],
                'supportphone' => $encode['from_billing_phone'],
            ];

            try {
                Mail::to($encode['to_billing_email'])->send(new \App\Mail\AppointmentMail($details)); 
            } catch (\Exception $e) {
                Log::info($e);
            }

            return redirect()->route('admin.update.order', $request->order_id)->with('success', trans('Updated!'));
        } else {
            return redirect()->route('admin.orders')->with('failed', trans('Not Found!'));
        }
    }
 
    // Greeting Letter
    public function greetingLetter(Request $request, $orderId)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Get the order details
        if($request->has('type') && $request->type == 'key') {
            $orderDetails = NfcCardOrder::join('nfc_card_order_transactions', 'nfc_card_orders.nfc_card_order_id', '=', 'nfc_card_order_transactions.nfc_card_order_id')
                    ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
                    ->join('users', 'nfc_card_orders.user_id', '=', 'users.id')
                    ->join('nfc_card_keys', 'nfc_card_orders.order_details->unique_key', '=', 'nfc_card_keys.unqiue_key')
                    ->select('nfc_card_order_transactions.*', 'users.*', 'nfc_card_orders.order_details', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.delivery_address', 'nfc_card_orders.order_status', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at', 'nfc_card_order_transactions.invoice_details')
                    ->whereJsonContains('nfc_card_orders.order_details->unique_key', $orderId)->where('nfc_card_orders.order_status', '!=', 'pending')
                    ->first();
        } else {
            $orderDetails = NfcCardOrder::join('nfc_card_order_transactions', 'nfc_card_orders.nfc_card_order_id', '=', 'nfc_card_order_transactions.nfc_card_order_id')
                    ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
                    ->join('users', 'nfc_card_orders.user_id', '=', 'users.id')
                    ->select('nfc_card_order_transactions.*', 'users.*', 'nfc_card_orders.order_details', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.delivery_address', 'nfc_card_orders.order_status', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at', 'nfc_card_order_transactions.invoice_details')
                    ->where('nfc_card_orders.nfc_card_order_id', $orderId)
                    ->first();
        }

        if(!$orderDetails) {
            return redirect()->route('admin.orders')->with('failed', trans('Not Found!'));
        }

        $template = "
        <div class='logo'>
            <img src=':logo' alt=':websitename'>
        </div>
        <h2 class='d-print-none'>Welcome to :websitename! 🎉</h2>
        <div class='content'>
            <h4>Dear <strong>:customername</strong>,</h4>
            <p>Your NFC Card is ready to help you share your contact details effortlessly. Activate your card and start networking with just a tap!</p>
            <h3>🔑 Your Activation Code</h3>
            <p class='activation-code'>:activationcode</p>
            <h3 class='mt-3'>📌 How to Activate Your NFC Card?</h3>
            <ol style='text-align: left;'>
                <li><strong>Log in</strong> to your account.</li>
                <li><strong>Go to</strong> the <strong>'Activate NFC Card'</strong> section.</li>
                <li><strong>Enter the activation code</strong> and submit.</li>
                <li>Your NFC card is now ready to use!</li>
            </ol>
            <div class='qr-code'>
                <h3>📲 Scan to Activate</h3>
                <canvas id='activateQrCode'></canvas>
            </div>
        </div>
        <div class='mt-3'>
            <span>Thank you for choosing <strong>:websitename</strong>!</span>
            <p>If you need any assistance, feel free to contact us at 
                <strong><a href='mailto::supportemail'>:supportemail</a></strong> 
                or call us at <strong>:supportphone</strong>.
            </p>
        </div>
        <div class='content'>
            <p>Best regards,</p>
            <h4>:websitename</h4>
        </div>
        ";

        return view('admin.pages.nfc-card-order.greeting-letter', compact('orderDetails', 'template', 'settings', 'config'));
    }

    // Update greeting letter
    public function updateGreetingLetter(Request $request, $orderId)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'greeting_letter' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.greeting.letter')->with('failed', $validator->errors()->first());
        }

        // Update the greeting letter
        DB::table('config')->where('config_key', 'nfc_greetings')->update([
            'config_value' => $request->greeting_letter,
        ]);

        // Page redirect
        return redirect()->route('admin.greeting.letter', $orderId)->with('success', trans('Updated!'));
    }
}
