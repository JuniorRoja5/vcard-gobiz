<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CronJobController extends Controller
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

    // Cron Jobs
    public function index()
    {
        // Queries
        $settings = Setting::first();
        $config = DB::table('config')->get();

        // Separate dates in array
        $config[60]->config_value = str_replace('[', '', $config[60]->config_value);
        $config[60]->config_value = str_replace(']', '', $config[60]->config_value);

        return view('admin.pages.cron-jobs.index', compact('settings', 'config'));
    }

    // Update cron jobs
    public function update(Request $request)
    {
        // Validate form
        $validator = Validator::make($request->all(), [
            'dates_in_array' => 'required',
        ]);

        // Check validation
        if ($validator->fails()) {
            return redirect()->route('admin.cron.jobs')->with('failed', trans('Please fill all the fields.'));
        }

        // dates_in_array in array (Like [10, 5, 3, 1])
        $dates_in_array = explode(',', $request->dates_in_array);
        $dates_in_array = array_map('intval', $dates_in_array);
        $dates_in_array = array_unique($dates_in_array);

        // Check $dates_in_array is min -30 to max 366
        foreach ($dates_in_array as $date) {
            if ($date < -30 || $date > 366) {
                return redirect()->route('admin.cron.jobs')->with('failed', trans('Please enter a valid number of dates.'));
            }
        }

        // Update config
        DB::table('config')->where('config_key', 'cronjob_dates_in_array')->update([
            'config_value' => $dates_in_array,
        ]);

        // Success message
        return redirect()->route('admin.cron.jobs')->with('success', trans('Updated!'));
    }

    // Set cronjob time
    public function setCronjobTime(Request $request)
    {
        // Validate form
        $validator = Validator::make(request()->all(), [
            'cron_hour' => 'required|integer|between:0,23',
        ]);

        // Check validation
        if ($validator->fails()) {
            return redirect()->route('admin.cron.jobs')->with('failed', trans('Please fill all the fields.'));
        }

        // Update config
        DB::table('config')->where('config_key', 'cron_hour')->update([
            'config_value' => $request->cron_hour,
        ]);

        // Success message
        return redirect()->route('admin.cron.jobs')->with('success', trans('Updated!'));
    }

    // Test reminder
    public function testReminder()
    {
        // Reminder intervals (fetch from settings or define statically)
        $reminderDays = DB::table('config')->get()[60]->config_value;

        // Convert to array
        $reminderDays = explode(',', $reminderDays);
        $reminderDays = array_map('intval', $reminderDays);

        $currentDate = Carbon::now();

        foreach ($reminderDays as $daysBefore) {
            // Calculate target expiry range
            $expiryDate = $currentDate->copy()->addDays($daysBefore);

            // Get users whose plans expire on the target date
            $users = DB::table('users')
                ->where('status', 1)
                ->whereDate('plan_validity', $expiryDate)
                ->get();

            if ($users->isEmpty()) {
                // $this->info("No users found for reminders {$daysBefore} days before expiry.");
                continue;
            }

            foreach ($users as $user) {
                $details = [
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ];

                // Send email
                try {
                    // Check $daysBefore is below 0
                    if ($daysBefore <= 0) {
                        Mail::to(Auth::user()->email)->send(new \App\Mail\ExpiredPlanMail($details));
                        // $this->info("Reminder email sent to {$user->email} ({$daysBefore} days before expiry)");
                    } else {
                        Mail::to(Auth::user()->email)->send(new \App\Mail\ExpiryPlanMail($details));
                        // $this->info("Reminder email sent to {$user->email} ({$daysBefore} days before expiry)");
                    }
                } catch (\Exception $e) {
                    // $this->error("Failed to send email to {$user->email}: {$e->getMessage()}");
                }
            }
        }

        return redirect()->back()->with('success', trans('Reminder emails have been sent successfully.'));
    }
}
