<?php
namespace App\Http\Controllers\User;

use App\BusinessCard;
use App\BusinessField;
use App\BusinessHour;
use App\Category;
use App\ContactForm;
use App\Gallery;
use App\Http\Controllers\Controller;
use App\Payment;
use App\Plan;
use App\Service;
use App\Setting;
use App\StoreProduct;
use App\Theme;
use App\User;
use App\VcardProduct;
use App\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CardController extends Controller
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

    // All user cards
    public function index(Request $request)
    {
        // Queries
        $config      = DB::table('config')->get();
        $active_plan = Plan::where('plan_id', Auth::user()->plan_id)->first();
        $plan        = User::where('user_id', Auth::user()->user_id)->first();
        $active_plan = json_decode($plan->plan_details);

        if ($active_plan != null) {
            if ($request->ajax()) {
                $businessCards = DB::table('business_cards')
                    ->join('users', 'business_cards.user_id', '=', 'users.user_id')
                    ->select('users.user_id', 'users.plan_validity', 'business_cards.*')
                    ->where('business_cards.user_id', Auth::user()->user_id)
                    ->where('business_cards.card_type', 'vcard')
                    ->where('business_cards.status', 1)
                    ->where('business_cards.card_status', '!=', 'deleted')
                    ->orderBy('business_cards.id', 'desc')
                    ->get();

                return DataTables::of($businessCards)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($card) {
                        return '<div><p>' . formatDateForUser($card->created_at) . '</p></div>';
                    })
                    ->editColumn('type', function ($card) {
                        if ($card->type === 'business') {
                            return '<span class="badge bg-primary text-white text-uppercase">' . trans($card->type) . '</span>';
                        } elseif ($card->type === 'personal') {
                            return '<span class="badge bg-info text-white text-uppercase">' . trans($card->type) . '</span>';
                        }
                    })
                    ->editColumn('title', function ($card) {
                        return '<div class="d-flex py-1 align-items-center">
                                    <div class="flex-fill">
                                        <div class=""><a href="' . route('user.edit.card', $card->card_id) . '" class="text-reset fw-bold">' . $card->title . '</a></div>
                                        <div class="text-secondary">' . $card->sub_title . '</div>
                                    </div>
                                </div>';
                    })
                    ->editColumn('views', function ($card) {
                        $views = Visitor::where('card_id', $card->card_url)->count();
                        return '<span class="">' . $views . '</span>';
                    })
                    ->editColumn('card_status', function ($card) {
                        return $card->card_status == 'inactive'
                        ? '<span class="badge bg-red text-white text-white">' . __('Disabled') . '</span>'
                        : '<span class="badge bg-green text-white text-white">' . __('Enabled') . '</span>';
                    })
                    ->editColumn('text_card_status', function ($card) {
                        return $card->card_status;
                    })
                    ->addColumn('action', function ($card) use ($config, $active_plan) {
                        $actionBtn = '';
                        // Preview
                        $actionBtn .= '<a class="dropdown-item" href="' . route('user.view.preview', $card->card_id) . '" target="_blank"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>' . __('Preview') . '</a>';

                        // Live
                        if ($card->custom_domain == null) {
                            if ($config[46]->config_value == '1') {
                                $actionBtn .= '<a class="dropdown-item" href="' . route('subdomain.profile', $card->card_url) . '" target="_blank"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-world-www"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.5 7a9 9 0 0 0 -7.5 -4a8.991 8.991 0 0 0 -7.484 4" /><path d="M11.5 3a16.989 16.989 0 0 0 -1.826 4" /><path d="M12.5 3a16.989 16.989 0 0 1 1.828 4" /><path d="M19.5 17a9 9 0 0 1 -7.5 4a8.991 8.991 0 0 1 -7.484 -4" /><path d="M11.5 21a16.989 16.989 0 0 1 -1.826 -4" /><path d="M12.5 21a16.989 16.989 0 0 0 1.828 -4" /><path d="M2 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M17 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M9.5 10l1 4l1.5 -4l1.5 4l1 -4" /></svg>' . __('Live') . '</a>';
                            } else {
                                $actionBtn .= '<a class="dropdown-item" href="' . route('profile', $card->card_url) . '" target="_blank"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-world-www"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.5 7a9 9 0 0 0 -7.5 -4a8.991 8.991 0 0 0 -7.484 4" /><path d="M11.5 3a16.989 16.989 0 0 0 -1.826 4" /><path d="M12.5 3a16.989 16.989 0 0 1 1.828 4" /><path d="M19.5 17a9 9 0 0 1 -7.5 4a8.991 8.991 0 0 1 -7.484 -4" /><path d="M11.5 21a16.989 16.989 0 0 1 -1.826 -4" /><path d="M12.5 21a16.989 16.989 0 0 0 1.828 -4" /><path d="M2 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M17 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M9.5 10l1 4l1.5 -4l1.5 4l1 -4" /></svg>' . __('Live') . '</a>';
                            }
                        } else {
                            $actionBtn .= '<a class="dropdown-item" href="https://www.' . $card->custom_domain . '" target="_blank"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-world-www"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.5 7a9 9 0 0 0 -7.5 -4a8.991 8.991 0 0 0 -7.484 4" /><path d="M11.5 3a16.989 16.989 0 0 0 -1.826 4" /><path d="M12.5 3a16.989 16.989 0 0 1 1.828 4" /><path d="M19.5 17a9 9 0 0 1 -7.5 4a8.991 8.991 0 0 1 -7.484 -4" /><path d="M11.5 21a16.989 16.989 0 0 1 -1.826 -4" /><path d="M12.5 21a16.989 16.989 0 0 0 1.828 -4" /><path d="M2 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M17 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M9.5 10l1 4l1.5 -4l1.5 4l1 -4" /></svg>' . __('Live') . '</a>';
                        }

                        // Connect with custom domain
                        $actionBtn .= '<a class="dropdown-item" href="' . route('user.connect.domain', $card->card_id) . '"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5" /><path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5" /></svg>' . __('Connect') . '</a>';

                        // QR
                        if ($card->custom_domain == null) {
                            if ($config[46]->config_value == '1') {
                                $actionBtn .= '<a class="dropdown-item open-qr" onclick="updateQr(`' . route('subdomain.profile', $card->card_url) . '`)"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-qrcode"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M7 17l0 .01" /><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M7 7l0 .01" /><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M17 7l0 .01" /><path d="M14 14l3 0" /><path d="M20 14l0 .01" /><path d="M14 14l0 3" /><path d="M14 20l3 0" /><path d="M17 17l3 0" /><path d="M20 17l0 3" /></svg>' . __('QR Code') . '</a>';
                            } else {
                                $actionBtn .= '<a class="dropdown-item open-qr" onclick="updateQr(`' . route('profile', $card->card_url) . '`)"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-qrcode"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M7 17l0 .01" /><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M7 7l0 .01" /><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M17 7l0 .01" /><path d="M14 14l3 0" /><path d="M20 14l0 .01" /><path d="M14 14l0 3" /><path d="M14 20l3 0" /><path d="M17 17l3 0" /><path d="M20 17l0 3" /></svg>' . __('QR Code') . '</a>';
                            }
                        } else {
                            $actionBtn .= '<a class="dropdown-item open-qr" onclick="updateQr(`https://www.' . $card->custom_domain . '`)"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-qrcode"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M7 17l0 .01" /><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M7 7l0 .01" /><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M17 7l0 .01" /><path d="M14 14l3 0" /><path d="M20 14l0 .01" /><path d="M14 14l0 3" /><path d="M14 20l3 0" /><path d="M17 17l3 0" /><path d="M20 17l0 3" /></svg>' . __('QR Code') . '</a>';
                        }

                        // Analytics
                        $actionBtn .= '<a class="dropdown-item" href="' . route('user.visitors', $card->card_url) . '"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-chart-bar"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 20h14" /></svg>' . __('Visitors') . '</a>';

                        // Newsletter
                        $actionBtn .= '<a class="dropdown-item" href="' . route('user.newsletter', $card->card_id) . '"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-mail-opened"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 9l9 6l9 -6l-9 -6l-9 6" /><path d="M21 9v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10" /><path d="M3 19l6 -6" /><path d="M15 13l6 6" /></svg>' . __('Newsletters') . '</a>';

                        // Enquiries
                        $actionBtn .= '<a class="dropdown-item" href="' . route('user.enquiries', $card->card_id) . '"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-mail"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>' . __('Enquiries') . '</a>';

                        // Appointments
                        if ($active_plan->appointment == 1) {
                            $actionBtn .= '<a class="dropdown-item" href="' . route('user.appointments', $card->card_id) . '"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-calendar-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h10" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M18 16.5v1.5l.5 .5" /></svg>' . __('Appointments') . '</a>';
                        }

                        // Edit
                        $actionBtn .= '<a class="dropdown-item border-top" href="' . route('user.edit.card', $card->card_id) . '"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-pencil"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>' . __('Edit') . '</a>';

                        // Duplicate
                        $actionBtn .= '<a class="dropdown-item" onclick="duplicateCard(`' . $card->card_id . '`, `vcard`); return false;"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-copy-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path stroke="none" d="M0 0h24v24H0z" /><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2 2 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /><path d="M11 14h6" /><path d="M14 11v6" /></svg>' . __('Duplicate') . '</a>';

                        // Disable / Enable card
                        $actionBtn .= $card->card_status == 'activated'
                        ? '<a class="open-model dropdown-item" data-id="' . $card->card_id . '" href="#openModel"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-server-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12h-6a3 3 0 0 1 -3 -3v-2c0 -1.083 .574 -2.033 1.435 -2.56m3.565 -.44h10a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-2" /><path d="M16 12h2a3 3 0 0 1 3 3v2m-1.448 2.568a2.986 2.986 0 0 1 -1.552 .432h-12a3 3 0 0 1 -3 -3v-2a3 3 0 0 1 3 -3h6" /><path d="M7 8v.01" /><path d="M7 16v.01" /><path d="M3 3l18 18" /></svg>' . __('Disable') . '</a>'
                        : '<a class="open-model dropdown-item" data-id="' . $card->card_id . '" href="#openModel"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-server"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M3 12m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M7 8l0 .01" /><path d="M7 16l0 .01" /></svg>' . __('Enable') . '</a>';

                        // Delete
                        $actionBtn .= '<a class="dropdown-item text-danger" onclick="deleteCard(`' . $card->card_id . '`, `delete`); return false;"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-1 icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>' . __('Delete') . '</a>';

                        return '
                            <a class="btn act-btn dropdown-toggle actions-buttons-column" href="#"
                                data-toggle="dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <div class="nav-item dropdown">
                                    ' . $actionBtn . '
                                </div>
                            </div>';
                    })
                    ->rawColumns(['created_at', 'type', 'title', 'views', 'card_status', 'action'])
                    ->make(true);
            }

            $config   = DB::table('config')->get(); 
            $settings = Setting::where('status', 1)->first();

            return view('user.pages.cards.cards', compact('settings', 'config'));
        } else {
            return redirect()->route('user.plans');
        }
    }

    // Choose a card type
    public function chooseCardType()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();

        $cards = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'vcard')->where('card_status', 'activated')->count();

        // Active plan details in user
        $plan         = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);

        // Check validity
        $validity = User::where('user_id', Auth::user()->user_id)->whereDate('plan_validity', '>=', Carbon::now())->count();

        // Check unlimited cards
        if ($plan_details->no_of_vcards == 999) {
            $no_cards = 999999;
        } else {
            $no_cards = $plan_details->no_of_vcards;
        }

        // Check vcard creation limit
        if ($validity == 1) {
            if ($cards < $no_cards) {
                return view('user.pages.cards.choose-a-card', compact('settings', 'plan_details'));
            } else {
                return redirect()->route('user.cards')->with('failed', trans('The maximum limit has been exceeded. Please upgrade your plan.'));
            }
        } else {
            // Redirect
            return redirect()->route('user.cards')->with('failed', trans('Your plan is over. Choose your plan renewal or new package and use it.'));
        }
    }

    // Skip business hours
    public function skipAndSave()
    {
        // Redirect
        return redirect()->route('user.cards')->with('success', trans('Your virtual business card is updated!'));
    }

    // Card Status Page
    public function cardStatus(Request $request, $id)
    {
        // Queries
        $businessCard = BusinessCard::where('card_id', $id)->first();

        // Check business card
        if ($businessCard == null) {
            return redirect()->route('user.cards')->with('failed', trans('Card not found!'));
        } else {

            // Queries
            $business_card = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_id', $id)->first();

            // Check business card
            if ($business_card == null) {
                return redirect()->route('user.cards')->with('failed', trans('Card not found!'));
            } else {
                // Check active cards
                if ($business_card->card_status == 'inactive') {

                    // Queries
                    $plan        = User::where('user_id', Auth::user()->user_id)->first();
                    $active_plan = json_decode($plan->plan_details);

                    // vCard
                    if ($business_card->card_type == "vcard") {
                        // vCard
                        $no_of_services       = Service::where('card_id', $id)->count();
                        $no_of_vcard_products = VcardProduct::where('card_id', $id)->count();
                        $no_of_links          = BusinessField::where('card_id', $id)->count();
                        $no_of_payments       = Payment::where('card_id', $id)->count();
                        $no_of_galleries      = Gallery::where('card_id', $id)->count();
                        $business_hours       = BusinessHour::where('card_id', $id)->count();
                        $contact_form         = ContactForm::where('card_id', $id)->count();

                        // Check vcard / store limitation
                        if ($no_of_services <= $active_plan->no_of_services && $no_of_vcard_products <= $active_plan->no_of_vcard_products && $no_of_galleries <= $active_plan->no_of_galleries && $no_of_links <= $active_plan->no_of_links && $no_of_payments <= $active_plan->no_of_payments) {

                            // Queries (vCards)
                            $cards = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'vcard')->where('card_status', 'activated')->count();

                            // Get plan details in user
                            $plan         = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
                            $plan_details = json_decode($plan->plan_details);

                            // Number of vcards limitation
                            if ($cards < $plan_details->no_of_vcards) {

                                // Update (vCard)
                                BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'vcard')->where('card_id', $id)->update([
                                    'card_status' => 'activated',
                                ]);

                                return redirect()->route('user.cards')->with('success', trans('Your vcard activated.'));
                            } else {

                                return redirect()->route('user.cards')->with('failed', trans('The maximum limit has been exceeded. Please upgrade your plan.'));
                            }
                        } else {
                            // Queries
                            $cards = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'vcard')->where('card_status', 'activated')->count();

                            // Get plan details in user
                            $plan         = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
                            $plan_details = json_decode($plan->plan_details);

                            // Number of vcards limitation
                            if ($cards < $plan_details->no_of_vcards) {
                                return redirect()->route('user.edit.card', $id)->with('failed', trans('You have downgraded your plan. Please re-configure this vcard as per your current plan features.'));
                            } else {
                                return redirect()->route('user.cards')->with('failed', trans('The maximum limit has been exceeded. Please upgrade your plan.'));
                            }
                        }
                    }

                    // Store
                    if ($business_card->card_type == "store") {
                        // Store
                        $no_of_categories     = Category::where('user_id', auth::user()->user_id)->count();
                        $no_of_store_products = StoreProduct::where('card_id', $id)->count();

                        // Check vcard / store limitation
                        if ($no_of_categories <= $active_plan->no_of_categories && $no_of_store_products <= $active_plan->no_of_store_products) {

                            // Queries (Stores)
                            $stores = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'store')->where('card_status', 'activated')->count();

                            // Get plan details in user
                            $plan         = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
                            $plan_details = json_decode($plan->plan_details);

                            // Number of stores limitation
                            if ($stores < $plan_details->no_of_stores) {

                                // Update (Stores)
                                BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'store')->where('card_id', $id)->update([
                                    'card_status' => 'activated',
                                ]);

                                return redirect()->route('user.stores')->with('success', trans('Your store link was activated.'));
                            } else {
                                return redirect()->route('user.stores')->with('failed', trans('The maximum limit has been exceeded. Please upgrade your plan.'));
                            }
                        } else {

                            // Queries (Stores)
                            $stores = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'store')->where('card_status', 'activated')->count();

                            // Get plan details in user
                            $plan         = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
                            $plan_details = json_decode($plan->plan_details);

                            // Number of stores limitation
                            if ($stores < $plan_details->no_of_stores) {
                                return redirect()->route('user.edit.store', $id)->with('failed', trans('You have downgraded your plan. Please re-configure this vcard as per your current plan features.'));
                            } else {
                                return redirect()->route('user.stores')->with('failed', trans('The maximum limit has been exceeded. Please upgrade your plan.'));
                            }
                        }
                    }
                } else {
                    // Update
                    BusinessCard::where('user_id', Auth::user()->user_id)->where('card_id', $id)->update([
                        'card_status' => 'inactive',
                    ]);

                    return redirect()->back()->with('success', trans('Deactivated'));
                }
            }
        }
    }

    // Delete card
    public function deleteCard(Request $request)
    {
        // Delete
        BusinessCard::where('user_id', Auth::user()->user_id)->where('card_id', $request->query('id'))->update([
            'card_status' => 'deleted',
        ]);

        return redirect()->route('user.cards')->with('success', trans('Deleted!'));
    }

    // Search by theme
    public function searchTheme(Request $request)
    {
        $query = $request->get('query');
        $type  = $request->get('type');

        $cards = Theme::where('theme_name', 'LIKE', '%' . $query . '%')->where('theme_description', $type)->where('status', 1)->orderBy('id', 'desc')->get();

        return response()->json($cards);
    }
}
