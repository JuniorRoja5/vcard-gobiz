<?php

namespace App\Http\Controllers\User;

use App\Gallery;
use App\Payment;
use App\Service;
use App\Category;
use App\Newsletter;
use App\ContactForm;
use App\Testimonial;
use App\BusinessCard;
use App\BusinessHour;
use App\StoreProduct;
use App\VcardProduct;
use App\BusinessField;
use App\InformationPop;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DuplicateController extends Controller
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

    // Create duplicate
    public function duplicate(Request $request)
    {
        // Params
        $id = $request->query('id');
        $type = $request->query('type');

        // Default route
        $route = 'user.cards';
        $message = 'vCard not found!';

        if ($type == 'store') {
            $route = 'user.stores';
            $message = 'Store not found!';
        }

        // Active plan details in user
        $plan = DB::table('users')->where('user_id', Auth::user()->user_id)->where('status', 1)->first();
        $plan_details = json_decode($plan->plan_details);

        // No of cards to created
        $cards = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_type', 'vcard')->where('card_status', 'activated')->count();

        // Check unlimited cards
        if ($plan_details->no_of_vcards == 999) {
            $no_cards = 999999;
        } else {
            $no_cards = $plan_details->no_of_vcards;
        }

        // Chech vcard creation limit
        if ($cards < $no_cards) {
            // Queries
            $businessCard = BusinessCard::where('user_id', Auth::user()->user_id)->where('card_id', $id)->first();

            // Check business card
            if ($businessCard == null) {
                return redirect()->route($route)->with('failed', trans($message));
            }

            // Generate card ID
            $generateCardId = uniqid();

            // Create duplicate
            $duplicateCard = new BusinessCard();
            $duplicateCard->card_id = $generateCardId;
            $duplicateCard->user_id = $businessCard->user_id;
            $duplicateCard->type = $businessCard->type;
            $duplicateCard->theme_id = $businessCard->theme_id;
            $duplicateCard->card_lang = $businessCard->card_lang;
            $duplicateCard->cover_type = $businessCard->cover_type;
            $duplicateCard->cover = $businessCard->cover;
            $duplicateCard->profile = $businessCard->profile;
            $duplicateCard->card_url = $businessCard->card_url . '-' . Str::random(5);
            $duplicateCard->custom_domain = $businessCard->custom_domain;
            $duplicateCard->card_type = $businessCard->card_type;
            $duplicateCard->title = $businessCard->title . ' (Duplicate)';
            $duplicateCard->sub_title = $businessCard->sub_title;
            $duplicateCard->description = $businessCard->description;
            $duplicateCard->enquiry_email = $businessCard->enquiry_email;
            $duplicateCard->appointment_receive_email = $businessCard->appointment_receive_email;
            $duplicateCard->is_newsletter_pop_active = $businessCard->is_newsletter_pop_active;
            $duplicateCard->is_info_pop_active = $businessCard->is_info_pop_active;
            $duplicateCard->custom_css = $businessCard->custom_css;
            $duplicateCard->custom_js = $businessCard->custom_js;
            $duplicateCard->password = $businessCard->password;
            $duplicateCard->expiry_time = $businessCard->expiry_time;
            $duplicateCard->card_status = 'activated';
            $duplicateCard->status = 1;
            $duplicateCard->save();

            // Check type
            if ($type == 'vcard') {
                // Duplicate social links
                $socialLinks = BusinessField::where('card_id', $id)->get();
                foreach ($socialLinks as $socialLink) {
                    try {
                        // Save social link
                        $duplicateSocialLink = new BusinessField();
                        $duplicateSocialLink->card_id = $duplicateCard->card_id;
                        $duplicateSocialLink->type = $socialLink->type;
                        $duplicateSocialLink->icon = $socialLink->icon;
                        $duplicateSocialLink->label = $socialLink->label;
                        $duplicateSocialLink->content = $socialLink->content;
                        $duplicateSocialLink->position = $socialLink->position;
                        $duplicateSocialLink->status = 1;
                        $duplicateSocialLink->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate payment links
                $paymentLinks = Payment::where('card_id', $id)->get();
                foreach ($paymentLinks as $paymentLink) {
                    try {
                        // Save payment link
                        $duplicatePaymentLink = new Payment();
                        $duplicatePaymentLink->card_id = $duplicateCard->card_id;
                        $duplicatePaymentLink->type = $paymentLink->type;
                        $duplicatePaymentLink->icon = $paymentLink->icon;
                        $duplicatePaymentLink->label = $paymentLink->label;
                        $duplicatePaymentLink->content = $paymentLink->content;
                        $duplicatePaymentLink->position = $paymentLink->position;
                        $duplicatePaymentLink->status = 1;
                        $duplicatePaymentLink->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate services
                $services = Service::where('card_id', $id)->get();
                foreach ($services as $service) {
                    try {
                        // Save service
                        $duplicateService = new Service();
                        $duplicateService->card_id = $duplicateCard->card_id;
                        $duplicateService->service_name = $service->service_name;
                        $duplicateService->service_image = $service->service_image;
                        $duplicateService->service_description = $service->service_description;
                        $duplicateService->enable_enquiry = $service->enable_enquiry;
                        $duplicateService->status = 1;
                        $duplicateService->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate products
                $products = VcardProduct::where('card_id', $id)->get();
                foreach ($products as $product) {
                    try {
                        // Save product
                        $duplicateProduct = new VcardProduct();
                        $duplicateProduct->card_id = $duplicateCard->card_id;
                        $duplicateProduct->product_id = uniqid();
                        $duplicateProduct->badge = $product->badge;
                        $duplicateProduct->currency = $product->currency;
                        $duplicateProduct->product_image = $product->product_image;
                        $duplicateProduct->product_name = $product->product_name;
                        $duplicateProduct->product_subtitle = $product->product_subtitle;
                        $duplicateProduct->regular_price = $product->regular_price;
                        $duplicateProduct->sales_price = $product->sales_price;
                        $duplicateProduct->product_status = $product->product_status;
                        $duplicateProduct->status = 1;
                        $duplicateProduct->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate galleries
                $galleries = Gallery::where('card_id', $id)->get();
                foreach ($galleries as $gallery) {
                    try {
                        $duplicateGallery = new Gallery();
                        $duplicateGallery->card_id = $duplicateCard->card_id;
                        $duplicateGallery->caption = $gallery->caption;
                        $duplicateGallery->gallery_image = $gallery->gallery_image;
                        $duplicateGallery->status = 1;
                        $duplicateGallery->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate testimonials
                $testimonials = Testimonial::where('card_id', $id)->get();
                foreach ($testimonials as $testimonial) {
                    try {
                        // Save testimonial
                        $duplicateTestimonial = new Testimonial();
                        $duplicateTestimonial->card_id = $duplicateCard->card_id;
                        $duplicateTestimonial->reviewer_image = $testimonial->reviewer_image;
                        $duplicateTestimonial->reviewer_name = $testimonial->reviewer_name;
                        $duplicateTestimonial->reviewer_subtext = $testimonial->reviewer_subtext;
                        $duplicateTestimonial->review = $testimonial->review;
                        $duplicateTestimonial->status = 1;
                        $duplicateTestimonial->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate newsletter
                $newsletter = Newsletter::where('card_id', $id)->first();
                if ($newsletter) {
                    try {
                        // Save newsletter
                        $duplicateNewsletter = new Newsletter();
                        $duplicateNewsletter->newsletter_id = $newsletter->newsletter_id;
                        $duplicateNewsletter->card_id = $duplicateCard->card_id;
                        $duplicateNewsletter->email = $newsletter->email;
                        $duplicateNewsletter->status = 1;
                        $duplicateNewsletter->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate  information_pops
                $popups = InformationPop::where('card_id', $id)->get();
                foreach ($popups as $popup) {
                    try {
                        // Save information_pop
                        $duplicateInformationPop = new InformationPop();
                        $duplicateInformationPop->information_pop_id = $popup->information_pop_id;
                        $duplicateInformationPop->card_id = $duplicateCard->card_id;
                        $duplicateInformationPop->confetti_effect = $popup->confetti_effect;
                        $duplicateInformationPop->info_pop_image = $popup->info_pop_image;
                        $duplicateInformationPop->info_pop_title = $popup->info_pop_title;
                        $duplicateInformationPop->info_pop_desc = $popup->info_pop_desc;
                        $duplicateInformationPop->info_pop_button_text = $popup->info_pop_button_text;
                        $duplicateInformationPop->info_pop_button_url = $popup->info_pop_button_url;
                        $duplicateInformationPop->status = 1;
                        $duplicateInformationPop->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate business hours
                $businessHours = BusinessHour::where('card_id', $id)->get();
                foreach ($businessHours as $businessHour) {
                    try {
                        // Save business hour
                        $duplicateBusinessHour = new BusinessHour();
                        $duplicateBusinessHour->card_id = $duplicateCard->card_id;
                        $duplicateBusinessHour->monday = $businessHour->monday;
                        $duplicateBusinessHour->tuesday = $businessHour->tuesday;
                        $duplicateBusinessHour->wednesday = $businessHour->wednesday;
                        $duplicateBusinessHour->thursday = $businessHour->thursday;
                        $duplicateBusinessHour->friday = $businessHour->friday;
                        $duplicateBusinessHour->saturday = $businessHour->saturday;
                        $duplicateBusinessHour->sunday = $businessHour->sunday;
                        $duplicateBusinessHour->is_always_open = $businessHour->is_always_open;
                        $duplicateBusinessHour->is_display = $businessHour->is_display;
                        $duplicateBusinessHour->status = 1;
                        $duplicateBusinessHour->save();
                    } catch (\Exception $e) {
                    }
                }

                // Duplicate contact forms
                $contactForms = ContactForm::where('card_id', $id)->get();
                foreach ($contactForms as $contactForm) {
                    try {
                        // Save contact form
                        $duplicateContactForm = new ContactForm();
                        $duplicateContactForm->contact_form_id = uniqid();
                        $duplicateContactForm->card_id = $duplicateCard->card_id;
                        $duplicateContactForm->user_id = $contactForm->user_id;
                        $duplicateContactForm->name = $contactForm->name;
                        $duplicateContactForm->email = $contactForm->email;
                        $duplicateContactForm->phone = $contactForm->phone;
                        $duplicateContactForm->message = $contactForm->message;
                        $duplicateContactForm->status = 1;
                        $duplicateContactForm->save();
                    } catch (\Exception $e) {
                    }
                }
            } else {
                // Product category
                $categories = Category::where('user_id', $businessCard->user_id)->get();
                foreach ($categories as $category) {
                    try {
                        // Save category
                        $category = new Category();
                        $category->user_id = $categories->user_id;
                        $category->category_id = uniqid();
                        $category->thumbnail = $category->thumbnail;
                        $category->category_name = $category->category_name;
                        $category->status = 1;
                        $category->save();
                    } catch (\Exception $e) {
                    }
                }
                
                // Save products
                $products = StoreProduct::where('card_id', $id)->get();
                foreach ($products as $product) {
                    try {
                        // Save product
                        $duplicateStoreProduct = new StoreProduct();
                        $duplicateStoreProduct->card_id = $duplicateCard->card_id;
                        $duplicateStoreProduct->product_id = uniqid();
                        $duplicateStoreProduct->category_id = $product->category_id;
                        $duplicateStoreProduct->badge = $product->badge;
                        $duplicateStoreProduct->product_image = $product->product_image;
                        $duplicateStoreProduct->product_name = $product->product_name;
                        $duplicateStoreProduct->product_subtitle = $product->product_subtitle;
                        $duplicateStoreProduct->regular_price = $product->regular_price;
                        $duplicateStoreProduct->sales_price = $product->sales_price;
                        $duplicateStoreProduct->product_status = $product->product_status;
                        $duplicateStoreProduct->status = 1;
                        $duplicateStoreProduct->save();
                    } catch (\Exception $e) {
                    }
                }
            }

            // Redirect
            return redirect()->route($route)->with('success', trans('Your ' . $type . ' duplicated successfully.'));
        } else {
            return redirect()->route($route)->with('failed', trans('The maximum limit has been exceeded. Please upgrade your plan.'));
        }
    }
}
