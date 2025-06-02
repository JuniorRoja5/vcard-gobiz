<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use App\EmailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mews\Purifier\Facades\Purifier;

class EmailTemplateController extends Controller
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

    // Email Templates
    public function emailTemplatesIndex(Request $request, $id)
    {
        // Queries
        $email_templates = EmailTemplate::where('email_template_id', $id)->first();

        // Check if the email template is not found
        if (empty($email_templates)) {
            return redirect()->route('admin.dashboard')->with('failed', __('Not Found!'));
        }

        $settings = Setting::where('status', 1)->first();

        return view('admin.pages.email-templates.index', compact('email_templates', 'settings'));
    }

    // Update Email Template Content
    public function updateEmailTemplateContent(Request $request)
    {
        // Queries
        $email_templates = EmailTemplate::where('email_template_id', $request->email_template_id)->first();

        // Check is_enabled is checked
        $is_enabled = 0;
        if ($request->is_enabled != null) {
            $is_enabled = 1;
        }

        // Update Email Template Content
        $email_templates->email_template_subject = $request->email_template_subject;
        $email_templates->email_template_content = Purifier::clean($request->email_template_content);
        $email_templates->is_enabled = $is_enabled;
        $email_templates->save();

        return redirect()->route('admin.email.templates.index', ['id' => $request->email_template_id])->with('success', __('Updated!'));
    }
}
