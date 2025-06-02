<?php

namespace App\Http\Controllers\User\Vcard\Create;

use App\Medias;
use App\Setting;
use App\Testimonial;
use App\BusinessCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
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

    // Testimonials
    public function testimonials()
    {
        // Queries
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);
        $media = Medias::where('user_id', Auth::user()->user_id)->orderBy('id', 'desc')->get();
        $settings = Setting::where('status', 1)->first();

        if($plan_details->no_testimonials > 0) {
            return view('user.pages.cards.testimonials', compact('plan_details', 'media', 'settings'));
        } else {
            return redirect()->route('user.popups', request()->segment(3));
        }
    }

    // Save Testimonial
    public function saveTestimonial(Request $request, $id)
    {
        // Queries
        $business_card = BusinessCard::where('card_id', $id)->first();

        // Check business card
        if ($business_card == null) {
            return redirect()->route('user.cards')->with('failed', trans('Card not found!'));
        } else {

            // Get plan details
            $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
            $plan_details = json_decode($plan->plan_details);

            // Check Testimonial review
            if ($request->review != null) {

                // Check Testimonial limit
                if (count($request->review) <= $plan_details->no_testimonials) {

                    // Delete previous Testimonials
                    Testimonial::where('card_id', $id)->delete();

                    // Check dynamic fields foreach
                    for ($i = 0; $i < count($request->review); $i++) {

                        // Save
                        $testimonial = new Testimonial();
                        $testimonial->card_id = $id;
                        $testimonial->reviewer_name = $request->reviewer_name[$i];
                        $testimonial->reviewer_image = $request->reviewer_image[$i];
                        $testimonial->review_subtext = $request->review_subtext[$i];
                        $testimonial->review = $request->review[$i];
                        $testimonial->save();
                    }

                    return redirect()->route('user.popups', $id)->with('success', trans('Testimonials are updated.'));
                } else {
                    return redirect()->route('user.testimonials', $id)->with('failed', trans('You have reached the plan limit!'));
                }
            } else {
                return redirect()->route('user.popups', $id)->with('success', trans('Testimonials are updated.'));
            }
        }
    }
}
