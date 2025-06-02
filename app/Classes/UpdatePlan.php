<?php

namespace App\Classes;

use App\Plan;
use Illuminate\Support\Facades\Validator;

class UpdatePlan
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
                    'plan_id' => 'required',
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
                    'no_of_galleries' => 'required',
                    'no_testimonials' => 'required',
                    'business_hours' => 'required',
                    'contact_form' => 'required',
                    'appointment' => 'required',
                    'no_of_enquires' => 'required',
                ]);

                if ($validator->fails()) {
                    return back()->with('failed', $validator->messages()->all()[0])->withInput();
                }

                // List of fields to check
                $fields = [
                    'business_hours', 'contact_form', 'custom_domain', 'appointment', 'pwa',
                    'password_protected', 'advanced_settings', 'personalized_link',
                    'hide_branding', 'is_private', 'free_setup', 'free_support', 'recommended', 'nfc_card'
                ];

                // Initialize an empty array to store the values
                $settings = [];

                foreach ($fields as $field) {
                    // Set the value to 0 if the field is "off", else set it to 1
                    $settings[$field] = $request->$field == null || $request->$field == "off" ? 0 : 1;
                }

                // Prepare the update data
                $updateData = [
                    'plan_type' => $request->plan_type,
                    'plan_name' => ucfirst($request->plan_name),
                    'plan_description' => ucfirst($request->plan_description),
                    'recommended' => $settings['recommended'],
                    'plan_price' => $request->plan_price,
                    'validity' => $request->validity,
                    'no_of_vcards' => $request->no_of_vcards,
                    'no_of_services' => $request->no_of_services,
                    'no_of_vcard_products' => $request->no_of_vcard_products,
                    'no_of_galleries' => $request->no_of_galleries,
                    'no_of_links' => $request->no_of_links,
                    'no_of_payments' => $request->no_of_payments,
                    'no_testimonials' => $request->no_testimonials,
                    'business_hours' => $settings['business_hours'],
                    'contact_form' => $settings['contact_form'],
                    'appointment' => $settings['appointment'],
                    'custom_domain' => $settings['custom_domain'],
                    'no_of_enquires' => $request->no_of_enquires,
                    'pwa' => $settings['pwa'],
                    'password_protected' => $settings['password_protected'],
                    'advanced_settings' => $settings['advanced_settings'],
                    'additional_tools' => 0,
                    'personalized_link' => $settings['personalized_link'],
                    'hide_branding' => $settings['hide_branding'],
                    'free_setup' => $settings['free_setup'],
                    'free_support' => $settings['free_support'],
                    'is_private' => $settings['is_private'],
                    'nfc_card' => $settings['nfc_card'],
                ];

                // Update the plan
                Plan::where('plan_id', $request->plan_id)->update($updateData);

                return $this->result = 1;
                break;

            case 'STORE':
                
                // Validate
                $validator = Validator::make($request->all(), [
                    'plan_id' => 'required',
                    'plan_type' => 'required',
                    'plan_name' => 'required',
                    'plan_description' => 'required',
                    'plan_price' => 'required',
                    'validity' => 'required',
                    'no_of_stores' => 'required',
                    'no_of_categories' => 'required',
                    'no_of_store_products' => 'required',
                ]);

                if ($validator->fails()) {
                    return back()->with('failed', $validator->messages()->all()[0])->withInput();
                }

                // List of fields to check
                $fields = [
                    'custom_domain', 'pwa', 'advanced_settings', 'personalized_link', 
                    'hide_branding', 'is_private', 'free_setup', 'free_support', 'recommended', 'nfc_card'
                ];

                // Initialize an empty array to store the values
                $settings = [];

                foreach ($fields as $field) {
                    // If the field is "off", set it to 0, else set it to 1
                    $settings[$field] = $request->$field == null || $request->$field == "off" ? 0 : 1;
                }

                // Prepare the update data
                $updateData = [
                    'plan_type' => $request->plan_type,
                    'plan_name' => ucfirst($request->plan_name),
                    'plan_description' => ucfirst($request->plan_description),
                    'recommended' => $settings['recommended'],
                    'plan_price' => $request->plan_price,
                    'validity' => $request->validity,
                    'no_of_stores' => $request->no_of_stores,
                    'no_of_categories' => $request->no_of_categories,
                    'no_of_store_products' => $request->no_of_store_products,
                    'custom_domain' => $settings['custom_domain'],
                    'pwa' => $settings['pwa'],
                    'advanced_settings' => $settings['advanced_settings'],
                    'additional_tools' => 0,
                    'personalized_link' => $settings['personalized_link'],
                    'hide_branding' => $settings['hide_branding'],
                    'free_setup' => $settings['free_setup'],
                    'free_support' => $settings['free_support'],
                    'is_private' => $settings['is_private'],
                    'nfc_card' => $settings['nfc_card'],
                ];

                // Update the plan
                Plan::where('plan_id', $request->plan_id)->update($updateData);

                return $this->result = 1;
                break;
            
            default:
                
                // Validate
                $validator = Validator::make($request->all(), [
                    'plan_id' => 'required',
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
                    'no_of_galleries' => 'required',
                    'no_testimonials' => 'required',
                    'business_hours' => 'required',
                    'contact_form' => 'required',
                    'appointment' => 'required',
                    'no_of_enquires' => 'required',
                    'no_of_stores' => 'required',
                    'no_of_categories' => 'required',
                    'no_of_store_products' => 'required',
                ]);

                if ($validator->fails()) {
                    return back()->with('failed', $validator->messages()->all()[0])->withInput();
                }

                // List of fields to check
                $fields = [
                    'business_hours', 'contact_form', 'custom_domain', 'appointment', 'pwa',
                    'password_protected', 'advanced_settings', 'personalized_link',
                    'hide_branding', 'is_private', 'free_setup', 'free_support', 'recommended', 'nfc_card'
                ];

                // Initialize an empty array to store the values
                $settings = [];

                // Loop through each field and check the value in the request
                foreach ($fields as $field) {
                    // If the field is "off", set it to 0, else set it to 1
                    $settings[$field] = $request->$field == null || $request->$field == "off" ? 0 : 1;
                }

                // Prepare the update data
                $updateData = [
                    'plan_type' => $request->plan_type,
                    'plan_name' => ucfirst($request->plan_name),
                    'plan_description' => ucfirst($request->plan_description),
                    'recommended' => $settings['recommended'],  // Dynamic value
                    'plan_price' => $request->plan_price,
                    'validity' => $request->validity,
                    'no_of_vcards' => $request->no_of_vcards,
                    'no_of_services' => $request->no_of_services,
                    'no_of_vcard_products' => $request->no_of_vcard_products,
                    'no_of_galleries' => $request->no_of_galleries,
                    'no_of_links' => $request->no_of_links,
                    'no_of_payments' => $request->no_of_payments,
                    'no_testimonials' => $request->no_testimonials,
                    'business_hours' => $settings['business_hours'],  // Dynamic value
                    'contact_form' => $settings['contact_form'],  // Dynamic value
                    'appointment' => $settings['appointment'],  // Dynamic value
                    'custom_domain' => $settings['custom_domain'],  // Dynamic value
                    'no_of_enquires' => $request->no_of_enquires,
                    'no_of_stores' => $request->no_of_stores,
                    'no_of_categories' => $request->no_of_categories,
                    'no_of_store_products' => $request->no_of_store_products,
                    'pwa' => $settings['pwa'],  // Dynamic value
                    'password_protected' => $settings['password_protected'],  // Dynamic value
                    'advanced_settings' => $settings['advanced_settings'],  // Dynamic value
                    'additional_tools' => 0,  // Dynamic value
                    'personalized_link' => $settings['personalized_link'],  // Dynamic value
                    'hide_branding' => $settings['hide_branding'],  // Dynamic value
                    'free_setup' => $settings['free_setup'],  // Dynamic value
                    'free_support' => $settings['free_support'],  // Dynamic value
                    'is_private' => $settings['is_private'],  // Dynamic value
                    'nfc_card' => $settings['nfc_card'],  // Dynamic value
                ];

                // Update the plan
                Plan::where('plan_id', $request->plan_id)->update($updateData);
                
                return $this->result = 1;
                break;
        }
    }
}