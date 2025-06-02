<?php

namespace App\Http\Controllers\User;

use App\Plan;
use App\User;
use App\Setting;
use App\Currency;
use App\NfcCardKey;
use App\BusinessCard;
use App\NfcCardOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ManageNfcCardController extends Controller
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

    // Manage NFC Cards
    public function index(Request $request)
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

        if ($active_plan != null) {
            // Check "nfc_card" is available in the plan
            if ($active_plan->nfc_card == 1) {
                if ($request->ajax()) {
                    // Get nfc card key generations
                    $nfcCardKeys = NfcCardOrder::join('nfc_card_keys', 'nfc_card_orders.order_details->unique_key', '=', 'nfc_card_keys.unqiue_key')
                        ->join('nfc_card_designs', 'nfc_card_orders.nfc_card_id', '=', 'nfc_card_designs.nfc_card_id')
                        ->select('nfc_card_keys.*', 'nfc_card_orders.nfc_card_id', 'nfc_card_orders.order_details', 'nfc_card_orders.nfc_card_order_id', 'nfc_card_orders.created_at', 'nfc_card_designs.nfc_card_name')
                        ->where('nfc_card_orders.user_id', Auth::user()->id) 
                        ->orderBy('nfc_card_orders.created_at', 'desc')
                        ->get();

                    return DataTables::of($nfcCardKeys)
                        ->addIndexColumn()
                        ->editColumn('created_at', function ($nfcCardKey) {
                            return formatDateForUser($nfcCardKey->created_at);
                        })
                        ->editColumn('nfc_card_name', function ($nfcCardKey) {
                            return '<a href="' . route('user.order.nfc.card.view', $nfcCardKey->nfc_card_order_id) . '" class="fw-bold">' . trans($nfcCardKey->nfc_card_name) . '</a>';
                        })
                        ->editColumn('card_id', function ($nfcCardKey) {
                            if ($nfcCardKey->card_id) {
                                // Get card id wise business card
                                $businessCard = BusinessCard::where('card_id', $nfcCardKey->card_id)->first();

                                return '<a href="' . url($businessCard->card_url) . '" target="_blank">' . $businessCard->title . '</a>';
                            } else {
                                return '<span class="fw-bold">' . __('-') . '</span>';
                            }
                        })
                        ->editColumn('link_status', function ($nfcCardKey) {
                            return $nfcCardKey->link_status == 'unlinked'
                                ? '<span class="badge bg-red text-white">' . __('Unlinked') . '</span>'
                                : '<span class="badge bg-green text-white">' . __('Linked') . '</span>';
                        })
                        ->editColumn('action', function ($nfcCardKey) {
                            // Link / Unlink
                            if ($nfcCardKey->link_status == 'linked') {
                                $actionBtn = '<a href="" onclick="updateStatus(\'' . $nfcCardKey->nfc_card_key_id . '\', \'unlink\'); return false;" class="dropdown-item"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-link-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l3 -3m2 -2l1 -1" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M3 3l18 18" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>' . __('Unlink from NFC Card') . '</a>';
                            } else {
                                $actionBtn = '<a href="' . route('user.link.nfc.card', $nfcCardKey->nfc_card_key_id) . '" class="dropdown-item"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>' . __('Link to NFC Card') . '</a>';
                            }

                            return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                                        data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                        <div class="nav-item dropdown">
                                            ' . $actionBtn . '
                                        </div>
                                    </div>';
                        })
                        ->rawColumns(['created_at', 'nfc_card_name', 'card_id', 'link_status', 'action'])
                        ->make(true);
                }
            } else {
                return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
            }

            return view('user.pages.manage-nfc-cards.index', compact('settings', 'config'));
        } else {
            return redirect()->route('user.plans');
        }
    }

    // Link NFC Card
    public function linkNfcCard(Request $request, $id)
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

        // Check "nfc_card" is available in the plan
        if ($active_plan->nfc_card == 1) {

            // Get the key generation
            $nfcCardKey = NfcCardKey::where('nfc_card_key_id', $id)->first();
            // All Acyive business cards
            $businessCards = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_status', 'activated')->get();

            // Check if the key generation exists
            if (!$nfcCardKey) {
                return redirect()->route('user.manage.nfc.cards')->with('failed', __('NFC Card not found!'));
            }

            return view('user.pages.manage-nfc-cards.link', compact('settings', 'config', 'nfcCardKey', 'businessCards'));
        } else {
            return redirect()->route('user.plans')->with('failed', __('This feature is not available on your current plan. Upgrade to a plan that includes this feature.'));
        }
    }

    public function updateCardLink(Request $request)
    {
        // Get data from request
        $nfcCardKeyId = $request->key_id;
        $cardId = $request->card_id;

        // Check if the key generation exists
        $nfcCardKey = NfcCardKey::where('nfc_card_key_id', $nfcCardKeyId)->first();

        // Check if the key generation exists
        if (!$nfcCardKey) {
            return redirect()->route('user.manage.nfc.cards')->with('failed', __('NFC Card not found!'));
        }

        // Update status
        NfcCardKey::where('nfc_card_key_id', $nfcCardKeyId)->update(['card_id' => $cardId, 'link_status' => 'linked', 'status' => 1, 'updated_at' => now()]);

        return redirect()->route('user.manage.nfc.cards')->with('success', __('Vcard Linked Successfully'));
    }

    // Actions
    public function action(Request $request)
    {
        // Get data from request
        $nfcCardKeyId = $request->query('id');
        $nfcCardKeyStatus = $request->query('status');

        // Check status
        switch ($nfcCardKeyStatus) {
            case 'link':
                $linkStatus = 'linked';
                $status = 1;
                break;

            case 'delete':
                $linkStatus = 'unlinked';
                $status = 1;

                // Update status
                NfcCardKey::where('nfc_card_key_id', $nfcCardKeyId)->update(['card_id' => null, 'link_status' => $linkStatus, 'status' => $status, 'updated_at' => now()]);
                break;

            default:
                $linkStatus = 'unlinked';
                $status = 1;

                // Update status
                NfcCardKey::where('nfc_card_key_id', $nfcCardKeyId)->update(['card_id' => null, 'link_status' => $linkStatus, 'status' => $status, 'updated_at' => now()]);
                break;
        }

        return redirect()->route('user.manage.nfc.cards')->with('success', __('Vcard Unlinked Successfully'));
    }
}
