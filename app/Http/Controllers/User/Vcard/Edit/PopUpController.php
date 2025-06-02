<?php

namespace App\Http\Controllers\User\Vcard\Edit;

use App\Setting;
use App\BusinessCard;
use App\InformationPop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PopUpController extends Controller
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

    // Show popups
    public function popups(Request $request, $id)
    {
        // Queries
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);
        $settings = Setting::where('status', 1)->first();
        $business_card = BusinessCard::where('card_id', $id)->first();

        // Check business card
        if ($business_card == null) {
            return redirect()->route('user.cards')->with('failed', trans('Card not found!'));
        }

        // Get information popup details from information pops table
        $informationPopUpDetails = InformationPop::where('card_id', $id)->first();

        return view('user.pages.edit-cards.edit-popups', compact('business_card', 'informationPopUpDetails', 'plan_details', 'settings'));
    }

    // Update popups
    public function updatePopups(Request $request, $id)
    {
        // Queries
        $business_card = BusinessCard::where('card_id', $id)->first();

        // Check business card
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);

        // Check business card
        if ($business_card == null) {
            return redirect()->route('user.cards')->with('failed', trans('Card not found!'));
        } else {
            // Check is newsletter popup is "enabled"
            $is_newsletter_pop_active = 0;
            if ($request->is_newsletter_pop_active == "1") {
                $is_newsletter_pop_active = 1;
            }

            // Check is information popup is "enabled"
            $is_info_pop_active = 0;
            if ($request->is_info_pop_active == "1") {
                $is_info_pop_active = 1;
            }

            // Check information popup is "enabled"
            if ($is_info_pop_active == 1) {

                // Validate and upload the information popup image
                if ($request->hasFile('info_pop_image') && $request->file('info_pop_image')->isValid()) {
                    $allowedExtensions = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
                    $extension = $request->info_pop_image->extension();

                    if (in_array($extension, $allowedExtensions)) {
                        // Generate unique file name and upload the image
                        $info_pop_image = 'uploads/information_pop_images/IMG-' . uniqid() . '-' . time() . '.' . $request->info_pop_image->extension();
                        $request->info_pop_image->move(public_path('uploads/information_pop_images'), $info_pop_image);
                    }
                }
                
                // Confetti effect
                $confetti_effect = 0;
                if ($request->confetti_effect == "1") {
                    $confetti_effect = 1;
                }

                // Check card_id is exists in information pops table
                if (InformationPop::where('card_id', $id)->exists()) {
                    // Update information popup
                    InformationPop::where('card_id', $id)->update([
                        'confetti_effect' => $confetti_effect,
                        'info_pop_image' => $info_pop_image,
                        'info_pop_title' => $request->info_pop_title,
                        'info_pop_desc' => $request->info_pop_desc,
                        'info_pop_button_text' => $request->info_pop_button_text,
                        'info_pop_button_url' => $request->info_pop_button_url,
                    ]);
                } else {
                    // Create information popup
                    $saveInfoPop = new InformationPop();
                    $saveInfoPop->information_pop_id = uniqid();
                    $saveInfoPop->card_id = $id;
                    $saveInfoPop->confetti_effect = $confetti_effect;
                    $saveInfoPop->info_pop_image = $info_pop_image;
                    $saveInfoPop->info_pop_title = $request->info_pop_title;
                    $saveInfoPop->info_pop_desc = $request->info_pop_desc;
                    $saveInfoPop->info_pop_button_text = $request->info_pop_button_text;
                    $saveInfoPop->info_pop_button_url = $request->info_pop_button_url;
                    $saveInfoPop->save();
                }
            }

            // Update popups
            BusinessCard::where('card_id', $id)->update([
                'is_newsletter_pop_active' => $is_newsletter_pop_active,
                'is_info_pop_active' => $is_info_pop_active,
            ]);

            // Check business hours is "ENABLED"
            if ($plan_details->business_hours == 1) {
                return redirect()->route('user.edit.business.hours', $id)->with('success', trans('Popups are updated.'));
            } elseif ($plan_details->appointment == 1) {
                return redirect()->route('user.edit.appointment', $id)->with('success', trans('Business hours are updated.'));
            } elseif ($plan_details->contact_form == 1) {
                return redirect()->route('user.edit.contact.form', $id)->with('success', trans('Testimonials are updated.'));
            } elseif ($plan_details->password_protected == 1 || $plan_details->advanced_settings == 1) {
                // Check contact form is "ENABLED"
                return redirect()->route('user.edit.advanced.setting', $id)->with('success', trans('Testimonials are updated.'));
            } else {
                return redirect()->route('user.cards')->with('success', trans('Your virtual business card is ready.'));
            }
        }
    }
}
