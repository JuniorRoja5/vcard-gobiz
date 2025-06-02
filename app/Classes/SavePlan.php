<?php

namespace App\Classes;

use App\Plan;
use Illuminate\Support\Facades\Validator;

class SavePlan
{
    public function create($request)
    {
        // Default
        $this->result = 0;

        // Check plan type
        switch ($request->plan_type) {
            case 'VCARD':

                // Validate
                $validator = Validator::make($request->all(), [
                    'plan_type' => 'required',
                    'plan_name' => 'required',
                    'plan_description' => 'required',
                    'plan_price' => 'required',
                    'validity' => 'required',
                    'no_of_vcards' => 'required',
                    'no_of_services' => 'required',
                    'no_of_vcard_products' => 'required',
                    'no_of_links' => 'required',
                    'no_of_payments' => 'required',
                    'no_testimonials' => 'required',
                    'no_of_galleries' => 'required',
                    'business_hours' => 'required',
                    'contact_form' => 'required',
                    'appointment' => 'required',
                    'no_of_enquires' => 'required'
                ]);

                if ($validator->fails()) {
                    return back()->with('failed', $validator->messages()->all()[0])->withInput();
                }

                // List of fields to check
                $fields = [
                    'business_hours', 'contact_form', 'appointment', 'custom_domain', 'pwa', 'password_protected',
                    'advanced_settings', 'personalized_link', 'hide_branding',
                    'is_private', 'free_setup', 'free_support', 'recommended', 'nfc_card'
                ];

                // Initialize an empty array to store the values
                $settings = [];

                foreach ($fields as $field) {
                    // Set the value to 0 if the field is "off", else set it to 1
                    $settings[$field] = $request->$field == null ||  $request->$field == "off" ? 0 : 1;
                }

                // Create a new Plan instance and set its attributes
                $plan = new Plan;
                $plan->plan_id = uniqid();  // Unique ID for the plan
                $plan->plan_type = $request->plan_type;
                $plan->plan_name = ucfirst($request->plan_name);
                $plan->plan_description = ucfirst($request->plan_description);
                $plan->recommended = $settings['recommended'];  // Dynamic value
                $plan->plan_price = $request->plan_price;
                $plan->validity = $request->validity;
                $plan->no_of_vcards = $request->no_of_vcards;
                $plan->no_of_services = $request->no_of_services;
                $plan->no_of_vcard_products = $request->no_of_vcard_products;
                $plan->no_of_galleries = $request->no_of_galleries;
                $plan->no_of_links = $request->no_of_links;
                $plan->no_of_payments = $request->no_of_payments;
                $plan->no_testimonials = $request->no_testimonials;
                $plan->business_hours = $settings['business_hours'];  // Dynamic value
                $plan->contact_form = $settings['contact_form'];  // Dynamic value
                $plan->appointment = $settings['appointment'];  // Dynamic value
                $plan->custom_domain = $settings['custom_domain'];  // Dynamic value
                $plan->no_of_enquires = $request->no_of_enquires;
                $plan->pwa = $settings['pwa'];  // Dynamic value
                $plan->password_protected = $settings['password_protected'];  // Dynamic value
                $plan->advanced_settings = $settings['advanced_settings'];  // Dynamic value
                $plan->additional_tools = 0;  // Dynamic value
                $plan->personalized_link = $settings['personalized_link'];  // Dynamic value
                $plan->hide_branding = $settings['hide_branding'];  // Dynamic value
                $plan->free_setup = $settings['free_setup'];  // Dynamic value
                $plan->free_support = $settings['free_support'];  // Dynamic value
                $plan->is_private = $settings['is_private'];  // Dynamic value
                $plan->nfc_card = $settings['nfc_card'];  // Dynamic value

                // Save the plan to the database
                $plan->save();

                return $this->result = 1;
                break;

            case 'STORE':

                // Validate
                $validator = Validator::make($request->all(), [
                    'plan_type' => 'required',
                    'plan_name' => 'required',
                    'plan_description' => 'required',
                    'plan_price' => 'required',
                    'validity' => 'required',
                    'no_of_stores' => 'required',
                    'no_of_categories' => 'required',
                    'no_of_store_products' => 'required'
                ]);

                if ($validator->fails()) {
                    return back()->with('failed', $validator->messages()->all()[0])->withInput();
                }

                // List of fields to check
                $fields = [
                    'custom_domain', 'pwa', 'advanced_settings',
                    'personalized_link', 'hide_branding', 'is_private', 'free_setup', 'free_support', 'recommended', 'nfc_card'
                ];

                // Initialize an empty array to store the values
                $settings = [];

                foreach ($fields as $field) {
                    // Check the value and set it to 0 if "off", else set it to 1
                    $settings[$field] = $request->$field == null || $request->$field == "off" ? 0 : 1;
                }

                // Save
                $plan = new Plan;
                $plan->plan_id = uniqid();
                $plan->plan_type = $request->plan_type;
                $plan->plan_name = ucfirst($request->plan_name);
                $plan->plan_description = ucfirst($request->plan_description);
                $plan->recommended = $settings['recommended'];
                $plan->plan_price = $request->plan_price;
                $plan->validity = $request->validity;
                $plan->no_of_stores = $request->no_of_stores;
                $plan->no_of_categories = $request->no_of_categories;
                $plan->no_of_store_products = $request->no_of_store_products;
                $plan->custom_domain = $settings['custom_domain'];
                $plan->pwa = $settings['pwa'];
                $plan->advanced_settings = $settings['advanced_settings'];
                $plan->additional_tools = 0;
                $plan->personalized_link = $settings['personalized_link'];
                $plan->hide_branding = $settings['hide_branding'];
                $plan->free_setup = $settings['free_setup'];
                $plan->free_support = $settings['free_support'];
                $plan->is_private = $settings['is_private'];
                $plan->nfc_card = $settings['nfc_card'];
                $plan->save();

                return $this->result = 1;
                break;
            
            default:

                // Validate
                $validator = Validator::make($request->all(), [
                    'plan_type' => 'required',
                    'plan_name' => 'required',
                    'plan_description' => 'required',
                    'plan_price' => 'required',
                    'validity' => 'required',
                    'no_of_vcards' => 'required',
                    'no_of_services' => 'required',
                    'no_of_vcard_products' => 'required',
                    'no_of_links' => 'required',
                    'no_of_payments' => 'required',
                    'no_testimonials' => 'required',
                    'no_of_galleries' => 'required',
                    'business_hours' => 'required',
                    'contact_form' => 'required',
                    'appointment' => 'required',
                    'no_of_enquires' => 'required',
                    'no_of_stores' => 'required',
                    'no_of_categories' => 'required',
                    'no_of_store_products' => 'required'
                ]);

                if ($validator->fails()) {
                    return back()->with('failed', $validator->messages()->all()[0])->withInput();
                }

                // List of fields to check
                $fields = [
                    'business_hours', 'contact_form', 'custom_domain', 'appointment', 'pwa', 'password_protected',
                    'advanced_settings', 'personalized_link', 'hide_branding',
                    'is_private', 'free_setup', 'free_support', 'recommended', 'nfc_card'
                ];

                // Initialize an empty array to store the values
                $settings = [];

                foreach ($fields as $field) {
                    // Set the value to 0 if "off", otherwise 1
                    $settings[$field] = $request->$field == null || $request->$field == "off" ? 0 : 1;
                }

                // Save
                $plan = new Plan;
                $plan->plan_id = uniqid();
                $plan->plan_type = $request->plan_type;
                $plan->plan_name = ucfirst($request->plan_name);
                $plan->plan_description = ucfirst($request->plan_description);
                $plan->recommended = $settings['recommended'];
                $plan->plan_price = $request->plan_price;
                $plan->validity = $request->validity;
                $plan->no_of_vcards = $request->no_of_vcards;
                $plan->no_of_services = $request->no_of_services;
                $plan->no_of_vcard_products = $request->no_of_vcard_products;
                $plan->no_of_galleries = $request->no_of_galleries;
                $plan->no_of_links = $request->no_of_links;
                $plan->no_testimonials = $request->no_testimonials;
                $plan->no_of_payments = $request->no_of_payments;
                $plan->no_of_enquires = $request->no_of_enquires;
                $plan->no_of_stores = $request->no_of_stores;
                $plan->no_of_categories = $request->no_of_categories;
                $plan->no_of_store_products = $request->no_of_store_products;

                $plan->business_hours = $settings['business_hours'];
                $plan->contact_form = $settings['contact_form'];
                $plan->appointment = $settings['appointment'];
                $plan->custom_domain = $settings['custom_domain'];
                $plan->pwa = $settings['pwa'];
                $plan->password_protected = $settings['password_protected'];
                $plan->advanced_settings = $settings['advanced_settings'];
                $plan->additional_tools = 0;
                $plan->personalized_link = $settings['personalized_link'];
                $plan->hide_branding = $settings['hide_branding'];
                $plan->free_setup = $settings['free_setup'];
                $plan->free_support = $settings['free_support'];
                $plan->is_private = $settings['is_private'];
                $plan->nfc_card = $settings['nfc_card'];

                $plan->save();

                return $this->result = 1;
                break;
        }
    }
}
