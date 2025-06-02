<?php

namespace App\Http\Controllers\User;

use App\BookedAppointment;
use App\User;
use App\Setting;
use App\Visitor;
use Carbon\Carbon;
use App\BusinessCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
    public function index(Request $request)
    {
        // Check
        if (Auth::user()->status == 1) {
            // Queries
            $plan = User::where('user_id', Auth::user()->user_id)->first();
            $active_plan = json_decode($plan->plan_details);
            $settings = Setting::where('status', 1)->first();
            $business_card = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'vcard')->where('card_status', '!=', 'deleted')->count();
            $storesCount = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'store')->where('card_status', '!=', 'deleted')->count();
            $remaining_days = 0;

            // Check active plan in user
            if ($active_plan != null) {

                // Check active plan
                if (isset($active_plan)) {
                    // Add more days in validity
                    $plan_validity = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', Auth::user()->plan_validity);
                    // Set the time to 23:59:59
                    $plan_validity->setTime(23, 59, 59);
                    if ($plan_validity->diffInDays() < 10) {
                        $plan_validity->addDays(1);
                    }
                    $current_date = Carbon::now();
                    $remaining_days = $current_date->diffInDays($plan_validity, false);
                }

                // Convert to integer
                $remaining_days = (int)$remaining_days;

                // Fetch counts for each month in a single query
                $monthCards = BusinessCard::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                    ->where('user_id', Auth::user()->user_id)
                    ->where('card_status', '!=', 'deleted')
                    ->groupBy(DB::raw('MONTH(created_at)'))
                    ->orderBy(DB::raw('MONTH(created_at)'))
                    ->pluck('count', 'month')
                    ->toArray();

                // Ensure there are 12 months in the result, filling in missing months with 0
                $monthCards = array_replace(array_fill(1, 12, 0), $monthCards);

                // Convert to comma-separated string
                $monthCards = implode(',', $monthCards);

                // Fetch vCards and store counts for each month in a single query
                $monthOverview = BusinessCard::selectRaw('MONTH(created_at) as month, card_type, COUNT(*) as count')
                    ->where('user_id', Auth::user()->user_id)
                    ->where('card_status', '!=', 'deleted')
                    ->where('status', 1)
                    ->groupBy(DB::raw('MONTH(created_at)'), 'card_type')
                    ->orderBy(DB::raw('MONTH(created_at)'))
                    ->get();

                // Prepare the result in the desired format
                $vcards = array_fill(1, 12, 0); // Default values for each month
                $stores = array_fill(1, 12, 0); // Default values for each month

                // Loop through the results and populate the vcards and stores arrays
                foreach ($monthOverview as $data) {
                    if ($data->card_type == 'vcard') {
                        $vcards[$data->month] = $data->count;
                    } elseif ($data->card_type == 'store') {
                        $stores[$data->month] = $data->count;
                    }
                }

                // Now $vcards and $stores contain the counts for each month
                $vcards = implode(',', $vcards);
                $stores = implode(',', $stores);

                // vCard and store counts
                $cards = BusinessCard::where('user_id', Auth::user()->user_id)->where('status', 1)->where('card_status', '!=', 'deleted')->get();

                $totalvCards = 0;
                $totalStores = 0;
                $cardId = [];
                for ($i = 0; $i < count($cards); $i++) {
                    if ($cards[$i]->card_type == 'vcard') {
                        $totalvCards += 1;
                    } else {
                        $totalStores += 1;
                    }
                    $cardId[$i] = $cards[$i]->card_url;
                }

                // Fetch the top 5 platforms in a single query, ordered by the total count
                $platforms = Visitor::select('visitors.platform', DB::raw('count(*) as total'))
                    ->whereIn('card_id', $cardId)
                    ->where('visitors.status', 1)
                    ->groupBy('visitors.platform')
                    ->orderByDesc(DB::raw('count(*)')) // Sort by count in descending order
                    ->limit(5) // Limit to top 5
                    ->get();

                // Prepare the result in the desired format
                $highestPlatforms = [
                    'platform' => $platforms->pluck('platform')->toArray(),
                    'count' => $platforms->pluck('total')->toArray(),
                ];

                // If no platforms are found, provide default values
                if ($platforms->isEmpty()) {
                    $highestPlatforms['platform'][] = '';
                    $highestPlatforms['count'][] = 100;
                }

                // Fetch the top 5 devices in a single query, ordered by the total count
                $devices = Visitor::select('visitors.device', DB::raw('count(*) as total'))
                    ->whereIn('card_id', $cardId)
                    ->where('visitors.status', 1)
                    ->groupBy('visitors.device')
                    ->orderByDesc(DB::raw('count(*)')) // Sort by count in descending order
                    ->limit(5) // Limit to top 5
                    ->get();

                // Prepare the result in the desired format
                $highestDevices = [
                    'device' => $devices->pluck('device')->toArray(),
                    'count' => $devices->pluck('total')->toArray(),
                ];

                // If no devices are found, provide default values
                if ($devices->isEmpty()) {
                    $highestDevices['device'][] = '';
                    $highestDevices['count'][] = 100;
                }

                // Fetch the top 20 vCards & Stores
                $cards = Visitor::select('visitors.card_id', DB::raw('count(*) as total'))
                    ->whereIn('card_id', $cardId)
                    ->where('visitors.status', '1')
                    ->groupBy('visitors.card_id')
                    ->orderByDesc(DB::raw('count(*)'))
                    ->take(20) // Limit to top 20 results
                    ->get();

                // Prepare the result
                $highestCards = $cards->map(function ($card) {
                    return [
                        'card' => $card->card_id,
                        'count' => $card->total,
                    ];
                })->toArray();

                // Get the start of the current week (Monday) and the end (Sunday)
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = Carbon::now()->endOfWeek();

                // Fetch vCard and store visitors for the entire week in a single query
                $currentWeekVisitors = Visitor::whereIn('visitors.card_id', $cardId)
                    ->where('visitors.status', "1")
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->whereIn('visitors.type', ['vcard', 'store'])
                    ->selectRaw('DAYOFWEEK(created_at) as day_of_week, visitors.type, count(*) as total')
                    ->groupBy(DB::raw('DAYOFWEEK(created_at)'), 'visitors.type')
                    ->get();

                // Initialize arrays to hold the results for vcard and store
                $weekData = [
                    'vcard' => array_fill(0, 7, 0), // Initialize with 7 days, all set to 0
                    'store' => array_fill(0, 7, 0), // Initialize with 7 days, all set to 0
                ];

                // Process the query results and fill the weekData arrays
                foreach ($currentWeekVisitors as $visitor) {
                    // Get the correct index for the day (1 = Sunday, 2 = Monday, ..., 7 = Saturday)
                    $dayIndex = $visitor->day_of_week - 1;
                    if ($visitor->type == 'vcard') {
                        $weekData['vcard'][$dayIndex] = $visitor->total;
                    } else {
                        $weekData['store'][$dayIndex] = $visitor->total;
                    }
                }

                return view('user.dashboard', compact('settings', 'active_plan', 'remaining_days', 'business_card', 'storesCount', 'monthCards', 'vcards', 'stores', 'totalvCards', 'totalStores', 'highestPlatforms', 'highestCards', 'weekData', 'highestDevices'));
            } else {
                return redirect()->route('user.plans');
            }
        } else {
            Session::flush();

            // Assume $errorMessage holds your error message
            $errorMessage = trans("User not found!");

            // Flash the error message to the session
            Session::flash('error', trans($errorMessage));

            return redirect()->back();
        }
    }

    // Fetch Appointments
    public function fetchAppointments(Request $request)
    {
        // Get user's business cards in "business_cards" table
        $cards = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_status', '!=', 'deleted')->get();
        $cardIds = [];

        // Get card ids
        foreach ($cards as $card) {
            $cardIds[] = $card->card_id;
        }

        // Get appointments and business details
        $appointments = BookedAppointment::whereIn('booked_appointments.card_id', $cardIds)->join('business_cards', 'business_cards.card_id', '=', 'booked_appointments.card_id')
            ->select('booked_appointments.booked_appointment_id', 'booked_appointments.card_id', 'booked_appointments.name', 'booked_appointments.phone', 'booked_appointments.notes', 'booked_appointments.booking_date as start', 'booked_appointments.booking_time as end',  'booked_appointments.booking_date',  'booked_appointments.booking_time', 'booked_appointments.booking_status', 'booked_appointments.created_at', 'business_cards.title')
            ->get();

        // Generate google calendar URL for the appointment for each appointment
        foreach ($appointments as $appointment) {
            // Check if the appointment is pending or not
            if ($appointment->booking_status == '0') {
                $appointment->new_booking_status = __('pending');
            } elseif ($appointment->booking_status == '1') {
                $appointment->new_booking_status = __('confirmed');
            } elseif ($appointment->booking_status == '2') {
                $appointment->new_booking_status = __('completed');
            } elseif ($appointment->booking_status == '-1') {
                $appointment->new_booking_status = __('canceled');
            }


            // Prepare details for the email and Google Calendar
            $appointmentDate = $appointment->booking_date; // e.g., '2024-10-18'
            $appointmentTime = $appointment->booking_time; // e.g., '14:00 - 15:00'

            $appointmentTimeJson = explode(' - ', $appointmentTime);

            if (count($appointmentTimeJson) < 2) {
                $startTime = $appointmentTimeJson[0];
                $endTime = $appointmentTimeJson[0];
            } else {
                $startTime = $appointmentTimeJson[0];
                $endTime = $appointmentTimeJson[1];
            }

            // Combine date and time for start and end in ISO 8601 format
            $startDateTime = Carbon::parse("{$appointmentDate} {$startTime}")->format('Ymd\THis');
            $endDateTime = Carbon::parse("{$appointmentDate} {$endTime}")->format('Ymd\THis');


            // Generate Google Calendar URL for the appointment
            $googleCalendarUrl = "https://calendar.google.com/calendar/r/eventedit?text=" . urlencode($appointment->name) . "&dates={$startDateTime}/{$endDateTime}&details=Your+appointment+has+been+{$appointment->new_booking_status}";

            // Set the URL in the appointment object
            $appointment->google_calendar_url = $googleCalendarUrl;
        }

        return response()->json($appointments);
    }
}
