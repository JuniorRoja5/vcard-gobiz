<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Setting;
use App\Currency;
use App\Transaction;
use App\BusinessCard;
use App\NfcCardDesign;
use Illuminate\Support\Carbon;
use App\Classes\AvailableVersion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
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
    public function index()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();
        $currency = Currency::where('iso_code', $config['1']->config_value)->first();
        $thisMonthIncome = Transaction::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('payment_status', 'Success')->sum('transaction_amount');
        $today_income = Transaction::where('payment_status', 'Success')->whereDate('created_at', Carbon::today())->sum('transaction_amount');
        $overall_users = User::where('role_id', 2)->where('status', 1)->count();
        $today_users = User::where('role_id', 2)->where('status', 1)->whereDate('created_at', Carbon::today())->count();

        // Current year
        $year = date('Y');

        // Fetch monthly income
        $monthWisecome = Transaction::where('payment_status', 'Success')
        ->whereYear('created_at', $year)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->selectRaw('MONTH(created_at) as month, SUM(transaction_amount) as total_income')
        ->pluck('total_income', 'month')
        ->toArray();

        // Fetch monthly users
        $monthWiseUsers = User::where('role_id', 2)
        ->whereYear('created_at', $year)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_users')
        ->pluck('total_users', 'month')
        ->toArray();

        // Ensure all months (1-12) are present and fill missing months with 0
        $monthIncome = [];
        $monthUsers = [];

        for ($i = 1; $i <= 12; $i++) {
        $monthIncome[$i] = $monthWisecome[$i] ?? 0;
        $monthUsers[$i] = $monthWiseUsers[$i] ?? 0;
        }

        // Convert arrays to comma-separated strings
        $monthIncome = implode(',', $monthIncome);
        $monthUsers = implode(',', $monthUsers);

        // Overview chart
        $year = date('Y');

        // Earnings query
        $monthWiseWarnings = Transaction::where('payment_status', 'Success')
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->selectRaw('MONTH(created_at) as month, SUM(transaction_amount) as total_earnings')
            ->pluck('total_earnings', 'month')
            ->toArray();

        // Ensure all months (1-12) are present, set missing months to 0
        $earnings = [];
        for ($i = 1; $i <= 12; $i++) {
            $earnings[$i] = $monthWiseWarnings[$i] ?? 0;
        }

        // VCards counts queries
        $monthWiseVcards = BusinessCard::where('card_type', 'vcard')
            ->where('card_status', '!=', 'deleted')
            ->where('status', 1)
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_vcards')
            ->pluck('total_vcards', 'month')
            ->toArray();

        // Ensure all months (1-12) are present and fill missing months with 0
        $vcards = [];

        for ($i = 1; $i <= 12; $i++) {
            $vcards[$i] = $monthWiseVcards[$i] ?? 0;
        }

        // Store counts queries
        $monthWiseStores = BusinessCard::where('card_type', 'store')
            ->where('card_status', '!=', 'deleted')
            ->where('status', 1)
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_stores')
            ->pluck('total_stores', 'month')
            ->toArray();

        // Ensure all months (1-12) are present and fill missing months with 0
        $stores = [];

        for ($i = 1; $i <= 12; $i++) {
            $stores[$i] = $monthWiseStores[$i] ?? 0;
        }

        // Pad missing months with 0s
        $earnings = implode(',', array_pad($earnings, 12, 0));
        $vcards = implode(',', array_pad($vcards, 12, 0));
        $stores = implode(',', array_pad($stores, 12, 0));

        // Total vCards and stores count
        $cardCounts = BusinessCard::where('card_status', '!=', 'deleted')
            ->where('status', 1)
            ->selectRaw('card_type, COUNT(*) as count')
            ->groupBy('card_type')
            ->pluck('count', 'card_type')
            ->toArray();

        $totalvCards = $cardCounts['vcard'] ?? 0;
        $totalStores = $cardCounts['store'] ?? 0;

        // Total Earnings
        $totalEarnings = Transaction::where('payment_status', 'Success')->sum('transaction_amount');

        // Current week sales
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $currentWeekSales = Transaction::where('payment_status', 'Success')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DAYOFWEEK(created_at) - 1 as day, SUM(transaction_amount) as daily_sales')
            ->groupBy(DB::raw('DAYOFWEEK(created_at) - 1')) // Group by the adjusted day of the week (0 = Sunday, 6 = Saturday)
            ->pluck('daily_sales', 'day')
            ->toArray();

        // Pad missing days with 0s (in case no sales on a specific day)
        $currentWeekSales = array_pad($currentWeekSales, 7, 0);

        // Default message
        $available = new AvailableVersion;
        $resp_data = $available->availableVersion();
 
        // Check support expiry
        $supportStatusMessage = "";

        if ($resp_data) {
            if ($resp_data['status'] == true && $resp_data['update'] == true) {
                // Store success message in session
                session()->flash('message', trans('<a href="' . route("admin.check") . '" class="text-white">A new version is available! <span style="position: absolute; right: 7.5vh;">' . trans("Install") . '</span></a>'));
            }


            // Check support expiry
            if (isset($resp_data['support_remaining_days']) && $resp_data['support_remaining_days'] <= 0) {
                session()->flash('support_status_message', trans('<a href="https://store.nativecode.in" target="_blank" class="text-white">Your support plan has ended! <span style="position: absolute; right: 7.5vh;">' . trans("Renew") . '</span></a>'));
            }
        }

        // NFC card designs available stocks is below 10 is true
        $nfcCardDesigns = NfcCardDesign::where('available_stocks', '<', 10)->get();

        // Check if there are results
        if ($nfcCardDesigns->isNotEmpty()) {
            session()->flash('stock_message', trans('<a href="' . route("admin.designs") . '" class="text-white">Some NFC card designs have stock below 10! <span class="text-white d-inline-block ms-lg-2">' . trans("Manage") . '</span></a>'));
        }        

        // View
        return view('admin.dashboard', compact('thisMonthIncome', 'today_income', 'overall_users', 'today_users', 'currency', 'settings', 'monthIncome', 'monthUsers', 'earnings', 'vcards', 'stores', 'totalEarnings', 'totalvCards', 'totalStores', 'currentWeekSales', 'supportStatusMessage'));
    }
}
