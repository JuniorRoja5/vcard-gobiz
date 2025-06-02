<?php
namespace App\Console\Commands;

use App\EmailTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class PlanExpiryCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expiry:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Reminder intervals (fetch from settings or define statically)
        $reminderDays = DB::table('config')->get()[60]->config_value;

        // Convert to array
        $reminderDays = explode(',', $reminderDays);
        $reminderDays = array_map('intval', $reminderDays);

        $currentDate = Carbon::now();

        // Get appointment pending email template content
        $expiredEmailTemplateDetails = EmailTemplate::where('email_template_id', '584922675206')->first();
        $expiryEmailTemplateDetails  = EmailTemplate::where('email_template_id', '584922675207')->first();

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
                $expiredPlanDetails = [
                    'status'          => "",
                    'emailSubject'    => $expiredEmailTemplateDetails->email_template_subject,
                    'emailContent'    => $expiredEmailTemplateDetails->email_template_content,
                    'registeredName'  => $user->name,
                    'registeredEmail' => $user->email,
                    'expiryDate'      => Carbon::parse($user->plan_validity)->format('Y-m-d'),
                    'planCode'        => json_decode($user->plan_details)->plan_id,
                    'planName'        => json_decode($user->plan_details)->plan_name,
                    'planPrice'       => json_decode($user->plan_details)->plan_price,
                ];

                $expiryPlanDetails = [
                    'status'          => "",
                    'emailSubject'    => $expiryEmailTemplateDetails->email_template_subject,
                    'emailContent'    => $expiryEmailTemplateDetails->email_template_content,
                    'registeredName'  => $user->name,
                    'registeredEmail' => $user->email,
                    'expiryDate'      => Carbon::parse($user->plan_validity)->format('Y-m-d'),
                    'planCode'        => json_decode($user->plan_details)->plan_id,
                    'planName'        => json_decode($user->plan_details)->plan_name,
                    'planPrice'       => json_decode($user->plan_details)->plan_price,
                ];

                // Send email
                try {
                    // Check $daysBefore is below 0
                    if ($daysBefore <= 0) {
                        Mail::to($user->email)->send(new \App\Mail\AppointmentMail($expiredPlanDetails));
                        // $this->info("Reminder email sent to {$user->email} ({$daysBefore} days before expiry)");

                        // Send Whatsapp notification twilio
                        if (DB::table('twilio_whatsapp_notification_settings')->exists() && File::exists(base_path('plugins/TwilioWhatsappNotification/plugin.json'))) {
                            if ($user->billing_phone == null || $user->billing_phone == '') {
                                continue;
                            }
                            $notification_template = DB::table('twilio_whatsapp_notification_templates')->where('template_name', 'User Plan Expired Notification')->first();

                            // check is enabled
                            if ($notification_template->is_enabled == 1) {
                                $twilio_notification_details = DB::table('twilio_whatsapp_notification_settings')->first();
                                $phone_code                  = DB::table('countries')->where('country_name', $user->billing_country)->first()->phone_code;
                                $app_name                    = config('app.name');

                                // Template
                                $sid   = $twilio_notification_details->account_sid;
                                $token = $twilio_notification_details->auth_token;

                                $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

                                $data = [
                                    'From'             => 'whatsapp:+' . $twilio_notification_details->from_number,
                                    'To'               => 'whatsapp:+' . $phone_code . $user->billing_phone,
                                    'ContentSid'       => $notification_template->template_sid,
                                    'ContentVariables' => json_encode(["app_name" => $app_name, "name" => $user->name, "email" => $user->email, "plan_name" => $expiredPlanDetails['planName'], "expiry_date" => $expiredPlanDetails['expiryDate']]),
                                ];

                                Http::withBasicAuth($sid, $token)
                                    ->asForm()
                                    ->post($url, $data);
                            }
                        }

                        // Send Whatsapp notification msg91
                        if (DB::table('msg91_whatsapp_notification_settings')->exists() && File::exists(base_path('plugins/MSG91WhatsappNotification/plugin.json'))) {
                            if ($user->billing_phone == null || $user->billing_phone == '') {
                                continue;
                            }
                            $notification_template = DB::table('msg91_whatsapp_notification_templates')->where('template_name', 'User Plan Expired Notification')->first();

                            // check is enabled
                            if ($notification_template->is_enabled == 1) {
                                // Template
                                $msg91_notification_details = DB::table('msg91_whatsapp_notification_settings')->first();
                                $phone_code                 = DB::table('countries')->where('country_name', $user->billing_country)->first()->phone_code;
                                $app_name                   = config('app.name');

                                $auth_key  = $msg91_notification_details->auth_key;
                                $sender_id = $msg91_notification_details->sender_id;

                                $url = "https://control.msg91.com/api/v5/whatsapp/";

                                $variables = [
                                    "app_name"    => $app_name,
                                    "name"        => $user->name,
                                    "email"       => $user->email,
                                    "plan_name"   => $expiredPlanDetails['planName'],
                                    "expiry_date" => $expiredPlanDetails['expiryDate'],
                                ];

                                $payload = [
                                    "template_id" => $notification_template->template_id,
                                    "sender"      => $sender_id,
                                    "short_url"   => "0",
                                    "mobiles"     => $phone_code . $user->billing_phone,
                                    "vars"        => $variables,
                                ];

                                Http::withHeaders([
                                    'authkey'      => $auth_key,
                                    'Content-Type' => 'application/json',
                                ])->post($url, $payload);
                            }
                        }

                        // Send Sms notification twilio
                        if (DB::table('twilio_sms_notification_settings')->exists() && File::exists(base_path('plugins/TwilioSmsNotification/plugin.json'))) {
                            if ($user->billing_phone == null || $user->billing_phone == '') {
                                continue;
                            }
                            $notification_template = DB::table('twilio_sms_notification_templates')->where('template_name', 'User Plan Expired Notification')->first();

                            // check is enabled
                            if ($notification_template->is_enabled == 1) {
                                $twilio_notification_details = DB::table('twilio_sms_notification_settings')->first();
                                $phone_code                  = DB::table('countries')->where('country_name', $user->billing_country)->first()->phone_code;
                                $app_name                    = config('app.name');

                                // Template
                                $sid   = $twilio_notification_details->account_sid;
                                $token = $twilio_notification_details->auth_token;

                                $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

                                $data = [
                                    'From'             => '+' . $twilio_notification_details->from_number,
                                    'To'               => '+' . $phone_code . $user->billing_phone,
                                    'ContentSid'       => $notification_template->template_sid,
                                    'ContentVariables' => json_encode(["app_name" => $app_name, "name" => $user->name, "email" => $user->email, "plan_name" => $expiredPlanDetails['planName'], "expiry_date" => $expiredPlanDetails['expiryDate']]),
                                ];

                                Http::withBasicAuth($sid, $token)
                                    ->asForm()
                                    ->post($url, $data);
                            }
                        }

                        // Send Sms notification msg91
                        if (DB::table('msg91_sms_notification_settings')->exists() && File::exists(base_path('plugins/MSG91SmsNotification/plugin.json'))) {
                            if ($user->billing_phone == null || $user->billing_phone == '') {
                                continue;
                            }
                            $notification_template = DB::table('msg91_sms_notification_templates')->where('template_name', 'User Plan Expired Notification')->first();

                            // check is enabled
                            if ($notification_template->is_enabled == 1) {
                                // Template
                                $msg91_notification_details = DB::table('msg91_sms_notification_settings')->first();
                                $phone_code                 = DB::table('countries')->where('country_name', $user->billing_country)->first()->phone_code;
                                $app_name                   = config('app.name');

                                $auth_key  = $msg91_notification_details->auth_key;
                                $sender_id = $msg91_notification_details->sender_id;

                                $url = "https://control.msg91.com/api/v5/flow/";

                                $variables = [
                                    "app_name"    => $app_name,
                                    "name"        => $user->name,
                                    "email"       => $user->email,
                                    "plan_name"   => $expiredPlanDetails['planName'],
                                    "expiry_date" => $expiredPlanDetails['expiryDate'],
                                ];

                                $payload = [
                                    "flow_id" => $notification_template->template_id,
                                    "sender"  => $sender_id,
                                    "mobiles" => $phone_code . $user->billing_phone,
                                    "vars"    => $variables,
                                ];

                                Http::withHeaders([
                                    'authkey'      => $auth_key,
                                    'Content-Type' => 'application/json',
                                ])->post($url, $payload);
                            }
                        }

                    } else {
                        Mail::to($user->email)->send(new \App\Mail\AppointmentMail($expiryPlanDetails));
                        // $this->info("Reminder email sent to {$user->email} ({$daysBefore} days before expiry)");

                        // Send Whatsapp notification twilio
                        if (DB::table('twilio_whatsapp_notification_settings')->exists() && File::exists(base_path('plugins/TwilioWhatsappNotification/plugin.json'))) {
                            if ($user->billing_phone == null || $user->billing_phone == '') {
                                continue;
                            }
                            $notification_template = DB::table('twilio_whatsapp_notification_templates')->where('template_name', 'User Plan Expiry Remainder')->first();

                            // check is enabled
                            if ($notification_template->is_enabled == 1) {
                                $twilio_notification_details = DB::table('twilio_whatsapp_notification_settings')->first();
                                $phone_code                  = DB::table('countries')->where('country_name', $user->billing_country)->first()->phone_code;
                                $app_name                    = config('app.name');

                                // Template

                                $sid   = $twilio_notification_details->account_sid;
                                $token = $twilio_notification_details->auth_token;

                                $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

                                $data = [
                                    'From'             => 'whatsapp:+' . $twilio_notification_details->from_number,
                                    'To'               => 'whatsapp:+' . $phone_code . $user->billing_phone,
                                    'ContentSid'       => $notification_template->template_sid,
                                    'ContentVariables' => json_encode(["app_name" => $app_name, "name" => $user->name, "email" => $user->email, "plan_name" => $expiryPlanDetails['planName'], "expiry_date" => $expiryPlanDetails['expiryDate']]),
                                ];

                                Http::withBasicAuth($sid, $token)
                                    ->asForm()
                                    ->post($url, $data);
                            }
                        }

                        // Send Whatsapp notification msg91
                        if (DB::table('msg91_whatsapp_notification_settings')->exists() && File::exists(base_path('plugins/MSG91WhatsappNotification/plugin.json'))) {
                            if ($user->billing_phone == null || $user->billing_phone == '') {
                                continue;
                            }
                            $notification_template = DB::table('msg91_whatsapp_notification_templates')->where('template_name', 'User Plan Expiry Remainder')->first();

                            // check is enabled
                            if ($notification_template->is_enabled == 1) {
                                // Template
                                $msg91_notification_details = DB::table('msg91_whatsapp_notification_settings')->first();
                                $phone_code                 = DB::table('countries')->where('country_name', $user->billing_country)->first()->phone_code;
                                $app_name                   = config('app.name');

                                $auth_key  = $msg91_notification_details->auth_key;
                                $sender_id = $msg91_notification_details->sender_id;

                                $url = "https://control.msg91.com/api/v5/whatsapp/";

                                $variables = [
                                    "app_name"    => $app_name,
                                    "name"        => $user->name,
                                    "email"       => $user->email,
                                    "plan_name"   => $expiryPlanDetails['planName'],
                                    "expiry_date" => $expiryPlanDetails['expiryDate'],
                                ];

                                $payload = [
                                    "template_id" => $notification_template->template_id,
                                    "sender"      => $sender_id,
                                    "short_url"   => "0",
                                    "mobiles"     => $phone_code . $user->billing_phone,
                                    "vars"        => $variables,
                                ];

                                Http::withHeaders([
                                    'authkey'      => $auth_key,
                                    'Content-Type' => 'application/json',
                                ])->post($url, $payload);
                            }
                        }

                        // Send Sms notification twilio
                        if (DB::table('twilio_sms_notification_settings')->exists() && File::exists(base_path('plugins/TwilioSmsNotification/plugin.json'))) {
                            if ($user->billing_phone == null || $user->billing_phone == '') {
                                continue;
                            }
                            $notification_template = DB::table('twilio_sms_notification_templates')->where('template_name', 'User Plan Expiry Remainder')->first();

                            // check is enabled
                            if ($notification_template->is_enabled == 1) {
                                $twilio_notification_details = DB::table('twilio_sms_notification_settings')->first();
                                $phone_code                  = DB::table('countries')->where('country_name', $user->billing_country)->first()->phone_code;
                                $app_name                    = config('app.name');

                                // Template

                                $sid   = $twilio_notification_details->account_sid;
                                $token = $twilio_notification_details->auth_token;

                                $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

                                $data = [
                                    'From'             => '+' . $twilio_notification_details->from_number,
                                    'To'               => '+' . $phone_code . $user->billing_phone,
                                    'ContentSid'       => $notification_template->template_sid,
                                    'ContentVariables' => json_encode(["app_name" => $app_name, "name" => $user->name, "email" => $user->email, "plan_name" => $expiryPlanDetails['planName'], "expiry_date" => $expiryPlanDetails['expiryDate']]),
                                ];

                                Http::withBasicAuth($sid, $token)
                                    ->asForm()
                                    ->post($url, $data);
                            }
                        }

                        // Send Sms notification msg91
                        if (DB::table('msg91_sms_notification_settings')->exists() && File::exists(base_path('plugins/MSG91SmsNotification/plugin.json'))) {
                            if ($user->billing_phone == null || $user->billing_phone == '') {
                                continue;
                            }
                            $notification_template = DB::table('msg91_sms_notification_templates')->where('template_name', 'User Plan Expiry Remainder')->first();

                            // check is enabled
                            if ($notification_template->is_enabled == 1) {
                                // Template
                                $msg91_notification_details = DB::table('msg91_sms_notification_settings')->first();
                                $phone_code                 = DB::table('countries')->where('country_name', $user->billing_country)->first()->phone_code;
                                $app_name                   = config('app.name');

                                $auth_key  = $msg91_notification_details->auth_key;
                                $sender_id = $msg91_notification_details->sender_id;

                                $url = "https://control.msg91.com/api/v5/flow/";

                                $variables = [
                                    "app_name"    => $app_name,
                                    "name"        => $user->name,
                                    "email"       => $user->email,
                                    "plan_name"   => $expiryPlanDetails['planName'],
                                    "expiry_date" => $expiryPlanDetails['expiryDate'],
                                ];

                                $payload = [
                                    "flow_id" => $notification_template->template_id,
                                    "sender"  => $sender_id,
                                    "mobiles" => $phone_code . $user->billing_phone,
                                    "vars"    => $variables,
                                ];

                                Http::withHeaders([
                                    'authkey'      => $auth_key,
                                    'Content-Type' => 'application/json',
                                ])->post($url, $payload);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // $this->error("Failed to send email to {$user->email}: {$e->getMessage()}");
                }
            }
        }
    }
}
