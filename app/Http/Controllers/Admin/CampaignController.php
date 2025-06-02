<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use App\Setting;
use App\Campaign;
use App\CampaignEmail;
use App\Mail\MarketingEmail;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
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

    // All Campaigns
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Campaign::where('status', '!=', -1)->get();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('campaign_name', function ($row) {
                    return $row->campaign_name;
                })
                ->addColumn(('campaign_desc'), function ($row) {
                    return $row->campaign_desc;
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 0
                        ? '<span class="badge bg-red text-white text-white">' . __('Inactive') . '</span>'
                        : '<span class="badge bg-green text-white text-white">' . __('Active') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    // Recampaign button
                    $recampaignButton = '<a href="' . route('admin.marketing.campaigns.recampaign') . '?id=' . $row->campaign_id . '" class="dropdown-item">' . __('Recampaign') . '</a>';

                    // Activate/Deactivate button
                    $activateDeactivate = $row->status == 0 ? trans('Activate') : trans('Deactivate');
                    $activateDeactivateFunction = $row->status == 0 ? 'activateCampaign' : 'deactivateCampaign';

                    return '<span class="dropdown text-end">
                                <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                <div class="actions dropdown-menu dropdown-menu-end">
                                    ' . $recampaignButton . '
                                    <a class="dropdown-item" href="#" onclick="' . $activateDeactivateFunction . '(\'' . $row->campaign_id . '\'); return false;">' . __($activateDeactivate) . '</a>
                                    <a class="dropdown-item" href="#" onclick="deleteCampaign(\'' . $row->campaign_id . '\'); return false;">' . __('Delete') . '</a>
                                </div>
                            </span>';
                })
                ->rawColumns(['campaign_name', 'campaign_desc', 'status', 'action'])
                ->make(true);
        }

        // Get groups
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.marketing.campaigns.index', compact('settings', 'config'));
    }

    // Create Campaign
    public function createCampaign()
    {
        // Queries
        $groups = Group::where('status', 1)->get();
        $settings = Setting::where('status', 1)->first();

        return view('admin.pages.marketing.campaigns.create', compact('groups', 'settings'));
    }

    // Save Campaign
    public function saveCampaign(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'campaign_name' => 'required|string|min:3',
            'campaign_description' => 'required|string|max:255',
            'group' => 'required|string|max:255',
            'email_subject' => 'required|string|max:255',
            'email_body' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Generate campaign id
        $campaignId = uniqid();

        // Save
        $campaign = new Campaign;
        $campaign->campaign_id = $campaignId;
        $campaign->campaign_name = ucfirst($request->campaign_name);
        $campaign->campaign_desc = $request->campaign_description;
        $campaign->save();

        // Campaign emails
        $campaign_emails = new CampaignEmail;
        $campaign_emails->campaign_email_id = uniqid();
        $campaign_emails->campaign_id = $campaignId;
        $campaign_emails->group_id = $request->group;
        $campaign_emails->subject = $request->email_subject;
        $campaign_emails->body = $request->email_body;
        $campaign_emails->save();

        // Retrieve email configuration from the $config table
        $config = DB::table('config')->get();

        // Check if the required keys are missing
        if ($config[57]->config_value == '' || $config[58]->config_value == '' || $config[59]->config_value == '') {
            return redirect()->route('admin.marketing.campaigns')->with('failed', trans('Email configuration not found or incomplete'));
        }

        // Set the email configuration dynamically
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.mailgun.org', // Mailgun SMTP host
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.username' => $config[57]->config_value, // Mailgun SMTP username
            'mail.mailers.smtp.password' => $config[58]->config_value, // Mailgun SMTP password (API key)
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.from.address' => $config[59]->config_value, // From email address
            'mail.from.name' => config('app.name'), // From name
        ]);

        // Get email addresses from group
        $emails = Group::where('group_id', $request->group)->first()->emails;
        $emails = json_decode($emails, true);

        // Email content
        $messageContent = [
            'subject' => $request->email_subject,
            'message' => $request->email_body,
        ];

        // Send email to each user
        try {
            foreach ($emails as $email) {
                // Replace #name with customer name from user table in the email
                $customer = User::where('email', $email)->first();
                $messageContent['message'] = str_replace('#name', $customer->name, $messageContent['message']);

                // Send email to each user
                Mail::to($email)->send(new MarketingEmail($messageContent));
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.marketing.campaigns')->with('failed', trans('Failed to send email to the user!'));
        }

        // Redirect
        return redirect()->route('admin.marketing.campaigns')->with('success', trans('Created!'));
    }

    // Recampaign
    public function recampaign(Request $request)
    {
        // Campaign details and CampaignEmail details in single query (use joins)
        $campaign_details = Campaign::where('campaigns.campaign_id', $request->query('id'))->join('campaign_emails', 'campaign_emails.campaign_id', '=', 'campaigns.campaign_id')->first();

        $groups = Group::where('status', 1)->get();
        $settings = Setting::where('status', 1)->first();

        if ($campaign_details == null) {
            return redirect()->route('admin.marketing.campaigns')->with('failed', trans('Not Found!'));
        }

        return view('admin.pages.marketing.campaigns.recampaign', compact('campaign_details', 'groups', 'settings'));
    }

    // Resend Campaign
    public function resendCampaign(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'campaign_name' => 'required|string|max:255',
            'campaign_description' => 'required|string|max:255',
            'group' => 'required|string|max:255',
            'email_subject' => 'required|string|max:255',
            'email_body' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Get campaign details
        $campaign_details = Campaign::where('campaign_id', $request->query('id'))->first();

        if ($campaign_details == null) {
            return redirect()->route('admin.marketing.campaigns')->with('failed', trans('Not Found!'));
        }

        // Retrieve email configuration from the $config table
        $config = DB::table('config')->get();

        // Check if the required keys are missing
        if ($config[57]->config_value == '' || $config[58]->config_value == '' || $config[59]->config_value == '') {
            return redirect()->route('admin.marketing.campaigns')->with('failed', trans('Email configuration not found or incomplete.'));
        }

        // Set the email configuration dynamically
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.mailgun.org', // Mailgun SMTP host
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.username' => $config[57]->config_value, // Mailgun SMTP username
            'mail.mailers.smtp.password' => $config[58]->config_value, // Mailgun SMTP password (API key)
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.from.address' => $config[59]->config_value, // From email address
            'mail.from.name' => config('app.name'), // From name
        ]);

        // Get email addresses from group
        $emails = Group::where('group_id', $request->group)->first()->emails;
        $emails = json_decode($emails, true);

        // Email content
        $messageContent = [
            'subject' => $request->email_subject,
            'message' => $request->email_body,
        ];

        // Send email to each user
        try {
            foreach ($emails as $email) {
                // Replace #name with customer name from user table in the email
                $customer = User::where('email', $email)->first();
                $messageContent['message'] = str_replace('#name', $customer->name, $messageContent['message']);

                // Send email to each user
                Mail::to($email)->send(new MarketingEmail($messageContent));
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.marketing.campaigns')->with('failed', trans('Failed to send email to the user!'));
        }

        // Update campaign
        $campaign_details->campaign_name = ucfirst($request->campaign_name);
        $campaign_details->campaign_desc = ucfirst($request->campaign_description);
        $campaign_details->save();

        // Update campaign emails
        $campaign_emails = CampaignEmail::where('campaign_id', $campaign_details->campaign_id)->first();
        $campaign_emails->group_id = $request->group;
        $campaign_emails->subject = $request->email_subject;
        $campaign_emails->body = clean($request->email_body);
        $campaign_emails->save();

        return redirect()->route('admin.marketing.campaigns')->with('success', trans('Resent!'));
    }

    // Status Campaign
    public function statusCampaign(Request $request)
    {
        // Queries
        $campaign_details = Campaign::where('campaign_id', $request->query('id'))->first();

        if ($campaign_details == null) {
            return redirect()->route('admin.marketing.campaigns')->with('failed', trans('Not Found!'));
        } else {
            // Get status from Campaign
            $campaignStatus = Campaign::where('campaign_id', $request->query('id'))->first();

            // Check status
            if ($campaignStatus->status == 0) {
                $campaign_details->status = 1;
            } else {
                $campaign_details->status = 0;
            }
            $campaign_details->save();

            return redirect()->route('admin.marketing.campaigns')->with('success', trans('Updated!'));
        }
    }

    // Delete Campaign
    public function deleteCampaign(Request $request)
    {
        // Update status
        $campaign_details = Campaign::where('campaign_id', $request->query('id'))->first();
        $campaign_details->status = -1;
        $campaign_details->save();

        return redirect()->route('admin.marketing.campaigns')->with('success', trans('Removed!'));
    }
}
