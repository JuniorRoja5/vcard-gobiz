<?php

namespace App\Http\Controllers\User;

use App\Plan;
use App\User;
use App\Setting;
use App\Currency;
use App\NfcCardOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ManageOrderNfcCardController extends Controller
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

    // Manage NFC Card orders
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

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        if ($active_plan != null) {
            // Check "nfc_card" is available in the plan
            if ($active_plan->nfc_card == 1) {
                if ($request->ajax()) {
                    // Joins "nfc_card_orders", "nfc_card_order_transactions" and "nfc_card_designs" tables
                    $orders = NfcCardOrder::join('nfc_card_order_transactions', 'nfc_card_orders.nfc_card_order_id', '=', 'nfc_card_order_transactions.nfc_card_order_id')
                        ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
                        ->select('nfc_card_order_transactions.*', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_orders.nfc_card_logo', 'nfc_card_orders.order_details', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.order_status', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at')
                        ->where('nfc_card_orders.user_id', Auth::user()->id)
                        ->orderBy('nfc_card_order_transactions.created_at', 'desc')
                        ->get();

                    return DataTables::of($orders)
                        ->addIndexColumn()
                        ->editColumn('created_at', function ($order) {
                            return formatDateForUser($order->created_at);
                        })
                        ->editColumn('nfc_card_order_id', function ($order) {
                            return '<a href="' . route('user.order.nfc.card.view', $order->nfc_card_order_id) . '">' . $order->nfc_card_order_id . '</a>';
                        })
                        ->editColumn('order_details', function ($order) {
                            $orderDetails = json_decode($order->order_details);

                            if ($orderDetails && isset($orderDetails->unique_key) && !empty($orderDetails->unique_key)) {
                                return '<a href="' . route('user.activate.nfc.card') . '?id=' . $orderDetails->unique_key . '" class="fw-bold">' . $orderDetails->unique_key . '</a>';
                            } else {
                                return '-';
                            }
                        })
                        ->editColumn('nfc_card_logo', function ($order) {
                            if (!empty($order->nfc_card_logo)) {
                                $output = '<a href="' . asset($order->nfc_card_logo) . '" class="fw-bold" data-bs-toggle="tooltip" data-bs-placement="top" title="' . trans('Download') . '" download>
                                            <img src="' . asset($order->nfc_card_logo) . '" class="img-fluid rounded" width="50" height="50" alt="NFC Card Attachment">
                                        </a>';
                            
                                if ($order->order_status == 'processing' || $order->order_status == 'hold') {
                                    $output .= '<br><a href="' . route('user.upload.nfc.card.logo', $order->nfc_card_order_id) . '" class="fw-bold btn small-btn text-success bg-white">' . __('Re-upload') . '</a>';
                                }
                            
                                return $output;
                            } elseif ($order->order_status == 'printing process begun' || $order->order_status == 'processing' || $order->order_status == 'hold') {
                                return '<a href="' . route('user.upload.nfc.card.logo', $order->nfc_card_order_id) . '" class="fw-bold btn small-btn text-success bg-white">' . __('Upload') . '</a>';
                            }
                            
                            return '-';                                                    
                        })
                        ->editColumn('nfc_card_name', function ($order) {
                            return '<span class="fw-bold">' . $order->nfc_card_name . '</span>';
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
                            } elseif ($order->order_status == 'printing process begun') {
                                return '<span class="badge bg-dark text-white">' . __('Printing Process Begun') . '</span>';
                            }
                        })
                        ->editColumn('action', function ($order) {
                            // View
                            $actionBtn = '<a href="' . route('user.order.nfc.card.view', $order->nfc_card_order_id) . '" class="dropdown-item"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>' . __('View') . '</a>';
                            return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                                        data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                        <div class="nav-item dropdown">
                                            ' . $actionBtn . '
                                        </div>
                                    </div>';
                        })
                        ->rawColumns(['created_at', 'nfc_card_order_id', 'order_details', 'nfc_card_logo', 'nfc_card_id', 'nfc_card_name', 'nfc_card_price', 'payment_status', 'delivery_status', 'action'])
                        ->make(true);
                }
            } else {
                return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
            }

            return view('user.pages.manage-orders.index', compact('settings', 'config'));
        } else {
            return redirect()->route('user.plans');
        }
    }

    // View order details
    public function viewOrder($orderId)
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

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        // Get the order and transaction and user tables (joins)
        $order = NfcCardOrder::join('nfc_card_order_transactions', 'nfc_card_orders.nfc_card_order_id', '=', 'nfc_card_order_transactions.nfc_card_order_id')
            ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
            ->join('users', 'nfc_card_orders.user_id', '=', 'users.id')
            ->select('nfc_card_order_transactions.*', 'users.*', 'nfc_card_orders.order_details', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.delivery_address', 'nfc_card_orders.order_status', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at')
            ->where('nfc_card_order_transactions.status', '!=', 2)
            ->where('nfc_card_orders.nfc_card_order_id', $orderId)->where('nfc_card_orders.user_id', Auth::user()->id)
            ->first();

        // Check if the order exists
        if (!$order) {
            return redirect()->route('user.manage.nfc.orders')->with('failed', __('Order not found!'));
        }

        // Check "nfc_card" is available in the plan
        if ($active_plan->nfc_card == 1) {
            return view('user.pages.manage-orders.view', compact('order', 'settings', 'config', 'symbol'));
        } else {
            return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
        }
    }

    // Upload NFC card logo
    public function uploadNfcCardLogo($nfcId)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        // Check enable nfc card order system
        if ($config[76]->config_value == '0') {
            return abort(404);
        }

        // Check if the user has a plan
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        // Get the order and transaction and user tables (joins)
        $order = NfcCardOrder::join('nfc_card_order_transactions', 'nfc_card_orders.nfc_card_order_id', '=', 'nfc_card_order_transactions.nfc_card_order_id')
            ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
            ->join('users', 'nfc_card_orders.user_id', '=', 'users.id')
            ->select('nfc_card_order_transactions.*', 'users.*', 'nfc_card_orders.order_details', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.nfc_card_id', 'nfc_card_designs.nfc_card_name', 'nfc_card_designs.nfc_card_price', 'nfc_card_orders.delivery_address', 'nfc_card_orders.order_status', 'nfc_card_orders.status', 'nfc_card_orders.created_at', 'nfc_card_orders.updated_at')
            ->where('nfc_card_order_transactions.status', '!=', 2)
            ->where('nfc_card_orders.nfc_card_order_id', $nfcId)->where('nfc_card_orders.user_id', Auth::user()->id)
            ->first();

        // Check if the order exists
        if (!$order) {
            return redirect()->route('user.manage.nfc.orders')->with('failed', __('Order not found!'));
        }

        // Check if the order status is "processing" or "hold"
        if ($order->order_status != 'processing' && $order->order_status != 'hold') {
            return redirect()->route('user.manage.nfc.orders')->with('failed', __('Unable to re-upload NFC card attachment. Order status is not "Processing" or "Holding".'));
        }

        // Check "nfc_card" is available in the plan
        if ($active_plan->nfc_card == 1) {
            return view('user.pages.manage-orders.upload-nfc-card-logo', compact('order', 'settings', 'config'));
        } else {
            return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
        }
    }

    // Update NFC card logo
    public function updateNfcCardLogo(Request $request, $nfcId)
    {
        // Check if the order exists
        $order = NfcCardOrder::where('nfc_card_order_id', $nfcId)->first();

        // Check if the order exists
        if (!$order) {
            return redirect()->route('user.manage.nfc.orders')->with('failed', __('Order not found!'));
        }

        // Check if the order status is "processing" or "hold"
        if ($order->order_status != 'processing' && $order->order_status != 'hold') {
            return redirect()->route('user.manage.nfc.orders')->with('failed', __('Unable to re-upload NFC card attachment. Order status is not "Processing" or "Holding".'));
        }

        // Upload nfc card logo
        $nfcCardLogo = $request->file('nfc_card_logo');
        $nfcCardLogo->move(public_path('uploads/nfc-cards'), $nfcCardLogo->getClientOriginalName());
        // Get uploaded image url
        $nfcCardLogoUrl = '/uploads/nfc-cards/' . $nfcCardLogo->getClientOriginalName();

        // Update logo in database
        NfcCardOrder::where('nfc_card_order_id', $nfcId)->update([
            'nfc_card_logo' => $nfcCardLogoUrl
        ]);

        return redirect()->route('user.manage.nfc.orders')->with('success', __('NFC Card Logo updated successfully!'));
    }
}
