<?php

namespace App\Http\Controllers\User\Vcard\Create;

use App\Setting;
use App\BusinessCard;
use App\BusinessField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SocialLinkController extends Controller
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

    // Social Links
    public function socialLinks()
    {
        // Queries
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $settings = Setting::where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);

        // Check plan details
        if($plan_details->no_of_links > 0) {
            return view('user.pages.cards.social-links', compact('plan_details', 'settings'));
        } else if($plan_details->no_of_payments > 0) {
            return redirect()->route('user.payment.links', request()->segment(3));
        } else if($plan_details->no_of_services > 0) {
            return redirect()->route('user.services', request()->segment(3));
        } else if($plan_details->no_of_vcard_products > 0) {
            return redirect()->route('user.vproducts', request()->segment(3));
        } else if($plan_details->no_of_galleries > 0) {
            return redirect()->route('user.galleries', request()->segment(3));
        } else if($plan_details->no_testimonials > 0) {
            return redirect()->route('user.testimonials', request()->segment(3));
        } else {
            return redirect()->route('user.popups', request()->segment(3));
        }
    }

    // Save social links
    public function saveSocialLinks(Request $request, $id)
    {
        // Queries
        $business_card = BusinessCard::where('card_id', $id)->first();

        // Check business card
        if ($business_card == null) {
            return redirect()->route('user.cards')->with('failed', trans('Card not found!'));
        } else {
            // Check icon
            if ($request->icon != null) {

                // Delete previous links
                BusinessField::where('card_id', $id)->delete();

                // Get plan details
                $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
                $plan_details = json_decode($plan->plan_details);

                // Check social links limit
                if (count($request->icon) <= $plan_details->no_of_links) {

                    // Check dynamic fields foreach
                    for ($i = 0; $i < count($request->icon); $i++) {

                        // Check dynamic fields
                        if (isset($request->icon[$i]) && isset($request->label[$i]) && isset($request->value[$i])) {

                            $customContent = $request->value[$i];

                            // Youtube
                            if ($request->type[$i] == 'youtube') {
                                $customContent = str_replace('https://www.youtube.com/watch?v=', '', $request->value[$i]);
                            }

                            // Google Map
                            if ($request->type[$i] == 'map') {
                                if (substr($request->value[$i], 0, 3) == 'pb=') {
                                    $customContent = $request->value[$i];
                                } else {
                                    $customContent = str_replace('<iframe src="', '', $request->value[$i]);
                                    $customContent = substr($customContent, 0, strpos($customContent, '" '));
                                    $customContent = str_replace('https://www.google.com/maps/embed?', '', $customContent);
                                }
                            }

                            // Save
                            $field = new BusinessField();
                            $field->card_id = $id;
                            $field->type = $request->type[$i];
                            $field->icon = $request->icon[$i];
                            $field->label = $request->label[$i];
                            $field->content = $customContent;
                            $field->position = $i + 1;
                            $field->save();
                        } else {
                            return redirect()->route('user.social.links', $id)->with('failed', trans('At least add one bio link.'));
                        }
                    }

                    // Check type
                    if ($business_card->type == 'personal') {
                        if ($plan_details->appointment == 1) {
                            return redirect()->route('user.appointment', $id)->with('success', trans('Bio links are updated.'));
                        } else {
                            return redirect()->route('user.cards',)->with('success', trans('Your virtual business card is ready.'));
                        }
                    } else {
                        return redirect()->route('user.payment.links', $id)->with('success', trans('Bio links are updated.'));
                    }
                } else {
                    return redirect()->route('user.social.links', $id)->with('failed', trans('The maximum limit was exceeded.'));
                }
            } else {
                return redirect()->route('user.social.links', $id)->with('failed', trans('At least add one bio link.'));
            }
        }
    }
}
