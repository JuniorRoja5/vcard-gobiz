<?php

namespace App\Http\Controllers\User\Vcard\Create;

use App\Setting;
use App\Newsletter;
use App\BusinessCard;
use App\InformationPop;
use Illuminate\Http\Request;
use Spatie\Sitemap\Tags\News;
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

        return view('user.pages.cards.popups', compact('plan_details', 'settings'));
    }

    // Save popups
    public function savePopups(Request $request, $id)
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
                        $info_pop_image = 'uploads/information_pop_images/IMG-' . uniqid() . '-' . time() . '.' . $extension;
                        $request->info_pop_image->move(public_path('uploads/information_pop_images'), $info_pop_image);
                    }
                }

                // Remove old information popup
                try {
                    InformationPop::where('card_id', $id)->delete();
                } catch (\Exception $e) {
                    // Do nothing
                }

                // Confetti effect
                $confetti_effect = 0;
                if ($request->confetti_effect == "1") {
                    $confetti_effect = 1;
                }

                // Insert information popup
                $information_pop = new InformationPop();
                $information_pop->information_pop_id = uniqid();
                $information_pop->card_id = $id;
                $information_pop->confetti_effect = $confetti_effect;
                $information_pop->info_pop_image = $info_pop_image;
                $information_pop->info_pop_title = $request->info_pop_title;
                $information_pop->info_pop_desc = $request->info_pop_desc;
                $information_pop->info_pop_button_text = $request->info_pop_button_text;
                $information_pop->info_pop_button_url = $request->info_pop_button_url;
                $information_pop->save();
            }

            // Update popups
            BusinessCard::where('card_id', $id)->update([
                'is_newsletter_pop_active' => $is_newsletter_pop_active,
                'is_info_pop_active' => $is_info_pop_active,
            ]);

            // Check business hours is "ENABLED"
            if ($plan_details->business_hours == 1) {
                return redirect()->route('user.business.hours', $id)->with('success', trans('Popups are updated.'));
            } elseif ($plan_details->appointment == 1) {
                return redirect()->route('user.appointment', $id)->with('success', trans('Business hours are updated.'));
            } elseif ($plan_details->contact_form == 1) {
                return redirect()->route('user.contact.form', $id)->with('success', trans('Testimonials are updated.'));
            } elseif ($plan_details->password_protected == 1 || $plan_details->advanced_settings == 1) {
                // Check contact form is "ENABLED"
                return redirect()->route('user.advanced.setting', $id)->with('success', trans('Testimonials are updated.'));
            } else {
                return redirect()->route('user.cards')->with('success', trans('Your virtual business card is ready.'));
            }
        }
    }
}
