<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Setting;
use App\BusinessCard;
use App\EmailTemplate;
use App\CustomDomainRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class CustomDomainRequestsController extends Controller
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

    // All custom domain requests
    public function customDomainRequests(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();
        $customDomainRequests = CustomDomainRequest::join('business_cards', 'custom_domain_requests.card_id', '=', 'business_cards.card_id')
            ->select('custom_domain_requests.*', 'business_cards.title', 'business_cards.card_url')
            ->where('custom_domain_requests.transfer_status', 0)
            ->orderBy('custom_domain_requests.id', 'desc')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($customDomainRequests)
                ->addIndexColumn()
                ->addColumn('created_at', function ($domain) {
                    return formatDateForUser($domain->created_at);
                })
                ->addColumn('vcard_id', function ($domain) {
                    return '<a href="' . route('profile', $domain->card_url) . '" target="_blank">' . __($domain->title) . '</a>';
                })
                ->addColumn('previous_domain', function ($domain) {
                    return '<a href="https://' . $domain->previous_domain . '" target="_blank">' . __($domain->previous_domain) . '</a>';
                })
                ->addColumn('current_domain', function ($domain) {
                    return '<a href="https://' . $domain->current_domain . '" target="_blank">' . __($domain->current_domain) . '</a>';
                })
                ->addColumn('transfer_status', function ($domain) {
                    if ($domain->transfer_status == 0) {
                        return '<span class="badge bg-warning text-white text-white">' . __('Processing') . '</span>';
                    } else {
                        return '<span class="badge bg-red text-white text-white">' . __('Rejected') . '</span>';
                    }
                })
                ->addColumn('action', function ($domain) {
                    $actions = '<span class="dropdown">
                                    <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                    <div class="actions dropdown-menu dropdown-menu-end" style="">';
                    if ($domain->transfer_status == 0) {
                        $actions .= '<a class="dropdown-item" href="#" onclick="approveDomain(`' . $domain->custom_domain_request_id . '`); return false;">' . __('Approve') . '</a>';
                        $actions .= '<a class="dropdown-item" href="#" onclick="rejectDomain(`' . $domain->custom_domain_request_id . '`); return false;">' . __('Reject') . '</a>';
                    }
                    $actions .= '</div>
                                </span>';
                    return $actions;
                })
                ->rawColumns(['vcard_id', 'previous_domain', 'current_domain', 'transfer_status', 'action'])
                ->make(true);
        }

        return view('admin.pages.custom-domain-requests.index', compact('customDomainRequests', 'settings', 'config'));
    }

    // Approved custom domains
    public function approvedCustomDomain(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();
        $customDomainRequests = CustomDomainRequest::join('business_cards', 'custom_domain_requests.card_id', '=', 'business_cards.card_id')
            ->select('custom_domain_requests.*', 'business_cards.title', 'business_cards.card_url')
            ->where('custom_domain_requests.transfer_status', 1)
            ->orderBy('custom_domain_requests.id', 'desc')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($customDomainRequests)
                ->addIndexColumn()
                ->addColumn('created_at', function ($domain) {
                    return formatDateForUser($domain->created_at);
                })
                ->addColumn('vcard_id', function ($domain) {
                    return '<a href="' . route('profile', $domain->card_url) . '" target="_blank">' . __($domain->title) . '</a>';
                })
                ->addColumn('previous_domain', function ($domain) {
                    return '<a href="https://' . $domain->previous_domain . '" target="_blank">' . __($domain->previous_domain) . '</a>';
                })
                ->addColumn('current_domain', function ($domain) {
                    return '<a href="https://' . $domain->current_domain . '" target="_blank">' . __($domain->current_domain) . '</a>';
                })
                ->addColumn('transfer_status', function ($domain) {
                    if ($domain->transfer_status == 1) {
                        return '<span class="badge bg-green text-white text-white">' . __('Approved') . '</span>';
                    } else {
                        return '<span class="badge bg-red text-white text-white">' . __('Rejected') . '</span>';
                    }
                })
                ->addColumn('action', function ($domain) {
                    $actions = '<span class="dropdown">
                                        <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                        <div class="actions dropdown-menu dropdown-menu-end" style="">';
                    if ($domain->transfer_status == 1) {
                        $actions .= '<a class="dropdown-item" href="#" onclick="processDomain(`' . $domain->custom_domain_request_id . '`); return false;">' . __('Process') . '</a>';
                        $actions .= '<a class="dropdown-item" href="#" onclick="rejectDomain(`' . $domain->custom_domain_request_id . '`); return false;">' . __('Reject') . '</a>';
                    } elseif ($domain->transfer_status == -1) {
                        $actions .= '<a class="dropdown-item" href="#" onclick="processDomain(`' . $domain->custom_domain_request_id . '`); return false;">' . __('Process') . '</a>';
                        $actions .= '<a class="dropdown-item" href="#" onclick="approveDomain(`' . $domain->custom_domain_request_id . '`); return false;">' . __('Approve') . '</a>';
                    }
                    $actions .= '</div>
                                    </span>';
                    return $actions;
                })
                ->rawColumns(['vcard_id', 'previous_domain', 'current_domain', 'transfer_status', 'action'])
                ->make(true);
        }

        return view('admin.pages.custom-domain-requests.approved', compact('customDomainRequests', 'settings', 'config'));
    }

    // Rejected custom domains
    public function rejectedCustomDomain(Request $request)
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();
        $customDomainRequests = CustomDomainRequest::join('business_cards', 'custom_domain_requests.card_id', '=', 'business_cards.card_id')
            ->select('custom_domain_requests.*', 'business_cards.title', 'business_cards.card_url')
            ->where('custom_domain_requests.transfer_status', -1)
            ->orderBy('custom_domain_requests.id', 'desc')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($customDomainRequests)
                ->addIndexColumn()
                ->addColumn('created_at', function ($domain) {
                    return formatDateForUser($domain->created_at);
                })
                ->addColumn('vcard_id', function ($domain) {
                    return '<a href="' . route('profile', $domain->card_url) . '" target="_blank">' . __($domain->title) . '</a>';
                })
                ->addColumn('previous_domain', function ($domain) {
                    return '<a href="https://' . $domain->previous_domain . '" target="_blank">' . __($domain->previous_domain) . '</a>';
                })
                ->addColumn('current_domain', function ($domain) {
                    return '<a href="https://' . $domain->current_domain . '" target="_blank">' . __($domain->current_domain) . '</a>';
                })
                ->addColumn('transfer_status', function ($domain) {
                    if ($domain->transfer_status == -1) {
                        return '<span class="badge bg-red text-white text-white">' . __('Rejected') . '</span>';
                    } else {
                        return '<span class="badge bg-warning text-white text-white">' . __('Processed') . '</span>';
                    }
                })
                ->addColumn('action', function ($domain) {
                    $actions = '<span class="dropdown">
                                        <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                        <div class="actions dropdown-menu dropdown-menu-end" style="">';
                    if ($domain->transfer_status == -1) {
                        $actions .= '<a class="dropdown-item" href="#" onclick="processDomain(`' . $domain->custom_domain_request_id . '`); return false;">' . __('Process') . '</a>';
                        $actions .= '<a class="dropdown-item" href="#" onclick="approveDomain(`' . $domain->custom_domain_request_id . '`); return false;">' . __('Approve') . '</a>';
                    }
                    $actions .= '</div>
                                    </span>';
                    return $actions;
                })
                ->rawColumns(['vcard_id', 'previous_domain', 'current_domain', 'transfer_status', 'action'])
                ->make(true);
        }

        return view('admin.pages.custom-domain-requests.rejected', compact('customDomainRequests', 'settings', 'config'));
    }

    // Process custom domain requests
    public function processCustomDomainRequests(Request $request)
    {
        // Request Id
        $requestId = $request->query('id');

        // Get custom domain request
        $customDomainRequest = CustomDomainRequest::where('custom_domain_request_id', $requestId)->first();

        // Update the status of the custom domain request
        $customDomainRequest->transfer_status = 0;
        $customDomainRequest->save();

        // Update custom domain in business_cards table
        $businessCard = BusinessCard::where('card_id', $customDomainRequest->card_id)->first();

        // Get business card customer email address
        $customerEmail = User::where('user_id', $customDomainRequest->user_id)->first()->email;

        // Get appointment pending email template content
        $emailTemplateDetails = EmailTemplate::where('email_template_id', '584922675202')->first();

        // Booking mail sent to customer
        if ($emailTemplateDetails->is_enabled == 1) {

            // Email details
            $customDomainRequestDetails = [
                'status' => "",
                'emailSubject' => $emailTemplateDetails->email_template_subject,
                'emailContent' => $emailTemplateDetails->email_template_content,
                'vcardName' => $businessCard->title,
                'previousDomain' => $customDomainRequest->previous_domain,
                'currentDomain' => $customDomainRequest->current_domain
            ];

            // Send email to the customer
            try {
                Mail::to($customerEmail)->send(new \App\Mail\AppointmentMail($customDomainRequestDetails));
            } catch (\Exception $e) {
                // Redirect to custom-domain-requests
                return redirect()->back()->with('failed', trans('Domain processed successfully. but, The email was not sent. Because there was a problem sending the email to the customer.'));
            }
        }

        return redirect()->back()->with('success', trans('Domain processed successfully, and a processed email has been sent to the customer\'s email address.'));
    }

    // Approved custom domain requests
    public function approvedCustomDomainRequests(Request $request)
    {
        // Request Id
        $requestId = $request->query('id');

        // Get custom domain request
        $customDomainRequest = CustomDomainRequest::where('custom_domain_request_id', $requestId)->first();

        // Previous domain transfer status, set 0
        $previousDomain = CustomDomainRequest::where('card_id', $customDomainRequest->card_id)->get();
        foreach ($previousDomain as $domain) {
            $domain->transfer_status = 2;
            $domain->save();
        }

        // Update the status of the custom domain request
        $customDomainRequest->transfer_status = 1;
        $customDomainRequest->save();

        // Update custom domain in business_cards table
        $businessCard = BusinessCard::where('card_id', $customDomainRequest->card_id)->first();
        $businessCard->custom_domain = $customDomainRequest->current_domain;
        $businessCard->save();

        // Get business card customer email address
        $customerEmail = User::where('user_id', $customDomainRequest->user_id)->first();

        if ($customerEmail) {
            $customerEmail = $customerEmail->email;
            // Get appointment pending email template content
            $emailTemplateDetails = EmailTemplate::where('email_template_id', '584922675203')->first();

            // Booking mail sent to customer
            if ($emailTemplateDetails->is_enabled == 1) {

                // Email details
                $customDomainRequestDetails = [
                    'status' => "",
                    'emailSubject' => $emailTemplateDetails->email_template_subject,
                    'emailContent' => $emailTemplateDetails->email_template_content,
                    'vcardName' => $businessCard->title,
                    'previousDomain' => $customDomainRequest->previous_domain,
                    'currentDomain' => $customDomainRequest->current_domain
                ];

                // Send email to the customer
                try {
                    Mail::to($customerEmail)->send(new \App\Mail\AppointmentMail($customDomainRequestDetails));
                } catch (\Exception $e) {
                    // Redirect to custom-domain-requests
                    return redirect()->back()->with('failed', trans('Domain approved successfully. but, the email was not sent. Because there was a problem sending the email to the customer.'));
                }
            }

            return redirect()->back()->with('success', trans('Domain approved successfully, and a confirmation email has been sent to the customer\'s email address.'));
        } else {
            return redirect()->back()->with('success', trans('Domain approved successfully, but, the email was not sent. Because there was a problem sending the email to the customer.'));
        }
    }

    // Rejected custom domain requests
    public function rejectedCustomDomainRequests(Request $request)
    {
        // Request Id
        $requestId = $request->query('id');

        // Get custom domain request
        $customDomainRequest = CustomDomainRequest::where('custom_domain_request_id', $requestId)->first();

        // Update the status of the custom domain request
        $customDomainRequest->transfer_status = -1;
        $customDomainRequest->save();

        // Update custom domain in business_cards table
        $businessCard = BusinessCard::where('card_id', $customDomainRequest->card_id)->first();

        // Get business card customer email address
        $customerEmail = User::where('user_id', $customDomainRequest->user_id)->first();

        if ($customerEmail) {
            $customerEmail = $customerEmail->email;

            // Get appointment pending email template content
            $emailTemplateDetails = EmailTemplate::where('email_template_id', '584922675204')->first();

            // Booking mail sent to customer
            if ($emailTemplateDetails->is_enabled == 1) {

                // Email details
                $customDomainRequestDetails = [
                    'status' => "",
                    'emailSubject' => $emailTemplateDetails->email_template_subject,
                    'emailContent' => $emailTemplateDetails->email_template_content,
                    'vcardName' => $businessCard->title,
                    'previousDomain' => $customDomainRequest->previous_domain,
                    'currentDomain' => $customDomainRequest->current_domain
                ];

                // Send email to the customer
                try {
                    Mail::to($customerEmail)->send(new \App\Mail\AppointmentMail($customDomainRequestDetails));
                } catch (\Exception $e) {
                    // Redirect to custom-domain-requests
                    return redirect()->back()->with('failed', trans('Domain rejected successfully. but, The email was not sent. Because there was a problem sending the email to the customer.'));
                }
            }

            return redirect()->back()->with('success', trans('Domain rejected successfully, and rejected email has been sent to the customer\'s email address.'));
        } else {
            return redirect()->back()->with('success', trans('Domain rejected successfully, but, The email was not sent. Because there was a problem sending the email to the customer.'));
        }
    }
}
