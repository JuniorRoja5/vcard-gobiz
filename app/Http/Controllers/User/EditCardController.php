<?php

namespace App\Http\Controllers\User;

use App\BusinessCard;
use App\Http\Controllers\Controller;
use App\Plan;
use App\Setting;
use App\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EditCardController extends Controller
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

    // Edit Card
    public function editCard(Request $request, $id)
    {
        // Queries
        $themes = Theme::where('theme_description', 'vCard')->orderBy('id', 'desc')->where('status', 1)->get();
        $business_card = BusinessCard::where('card_id', $id)->first();

        $config = DB::table('config')->get();

        // Check business card
        if ($business_card == null) {
            return redirect()->route('user.cards')->with('failed', trans('Card not found!'));
        } else {
            // Check card type
            if ($business_card->card_type == "store") {
                return redirect()->route('user.edit.store', $id);
            } else {
                $settings = Setting::where('status', 1)->first();
                $plan_details = Plan::where('plan_id', Auth::user()->plan_id)->first();

                return view('user.pages.edit-cards.edit-card', compact('themes', 'business_card', 'settings', 'plan_details', 'config'));
            }
        }
    }

    // Update card 
    public function updateBusinessCard(Request $request, $id)
    {
        // Validator
        $validator = Validator::make($request->all(), [
            'theme_id' => 'required',
            'card_lang' => 'required',
            'title' => 'required',
            'cover_type' => 'required',
            'subtitle' => 'required',
            'description' => 'required',
        ]);

        // Validate alert
        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Queries
        $business_card = BusinessCard::where('card_id', $id)->first();

        // Generate Unique card id
        if ($request->link) {
            $personalized_link = $request->link;
        } else {
            $personalized_link = $business_card->card_url;
        }

        $card_url = strtolower(preg_replace('/\s+/', '-', $personalized_link));

        // Already exists
        $current_card = BusinessCard::where('card_url', $card_url)->where('card_status', '!=', 'deleted')->count();

        if ($current_card == 0 || $business_card->card_url == $personalized_link) {

            // Check card is exists
            if ($business_card == null) {
                return redirect()->route('user.cards')->with('failed', trans('Card not found!'));
            } else {
                //Cover Type - Validation
                if($request->cover_type != "none") {
                    if (in_array($request->cover_type, ["youtube", "youtube-ap", "vimeo", "vimeo-ap", "photo"], TRUE)) {
                        // Cover URL no need to update for photo type.
                        if ($request->cover_type != "photo") {
                            if ($request->cover_type == "youtube" || $request->cover_type == "youtube-ap") {
                                // Remove the "https://youtube.com/watch?v=" from the URL
                                try {
                                    // Without www
                                    $cover = str_replace("https://youtube.com/watch?v=", "", $request->cover_url);
                                    // With www
                                    $cover = str_replace("https://www.youtube.com/watch?v=", "", $cover);
                                } catch (\Exception $e) {
                                    $cover = str_replace("https://youtu.be/", "", $request->cover_url);
                                    // With www
                                    $cover = str_replace("https://www.youtu.be/", "", $cover);
                                }
                            }
                            // Vimeo URL
                            if ($request->cover_type == "vimeo" || $request->cover_type == "vimeo-ap") {
                                // Remove the "https://vimeo.com/" from the URL
                                try {
                                    $cover = str_replace("https://vimeo.com/", "", $request->cover_url);
                                    // With www
                                    $cover = str_replace("https://www.vimeo.com/", "", $cover);
                                } catch (\Exception $e) {
                                    $cover = str_replace("https://vimeo.com/album/", "", $request->cover_url);
                                    // With www
                                    $cover = str_replace("https://www.vimeo.com/album/", "", $cover);
                                }
                            }
                            $cover_type = $request->cover_type;
                        } else {
                            $cover = $request->cover;
                            $cover_type = $request->cover_type;
                        }
                    }   
                } else {
                    $cover = "";
                    $cover_type = $request->cover_type;
                }
                
                // Update
                BusinessCard::where('card_id', $id)->update([
                    'profile' => $request->logo,
                    'cover_type' => $cover_type,
                    'cover' => $cover,
                    'theme_id' => $request->theme_id,
                    'card_lang' => $request->card_lang,
                    'card_url' => $personalized_link,
                    'title' => $request->title,
                    'sub_title' => $request->subtitle,
                    'description' => $request->description,
                ]);

                return redirect()->route('user.edit.social.links', $id)->with('success', trans('Details have been updated.'));
            }
        } else {
            return redirect()->route('user.edit.card', $id)->with('failed', trans('Sorry, the personalized link was already registered.'));
        }
    }
}
