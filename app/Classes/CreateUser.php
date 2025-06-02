<?php

namespace App\Classes;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser
{
    public function create($request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // List of permissions to check
        $fields = [
            'themes', 'plans', 'customers', 'payment_methods', 'coupons', 'transactions',
            'pages', 'blogs', 'users', 'custom_domain', 'backup', 'general_settings', 'translations',
            'marketing', 'sitemap', 'invoice_tax', 'maintenance_mode', 'demo_mode', 'software_update',
            'nfc_card_design', 'nfc_card_orders', 'nfc_card_order_transactions', 'nfc_card_key_generations', 'referral_system', 'email_templates', 'plugins'
        ];

        // Initialize an empty array for permissions
        $permissions = [];

        // Loop through each field and set the corresponding value in the permissions array
        foreach ($fields as $field) {
            // Check if the field exists in the request and handle its value
            $permissions[$field] = $request->has($field) && $request->$field === 'on' ? 1 : 0;
        }

        // Convert the permissions array to a JSON string
        $permissionsJson = json_encode($permissions);

        // Save
        $user = new User;
        $user->user_id = uniqid();
        $user->role_id = $request->role;
        $user->name = ucfirst($request->name);
        $user->email = $request->email;
        $user->email_verified_at = now();
        $user->password = Hash::make($request->password);
        $user->permissions = $permissionsJson;
        $user->save();

        return true;
    }
}
