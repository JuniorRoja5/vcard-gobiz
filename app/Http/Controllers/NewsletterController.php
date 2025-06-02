<?php

namespace App\Http\Controllers;

use App\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    // Subscribe to newsletter
    public function subscribe(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        // Check validation
        if ($validator->fails()) { 
            $response = [
                'status' => 'failed',
                'message' => trans('Email is required.')
            ];
    
            return response()->json($response);
        }

        // Check email is exists
        $email_exists = Newsletter::where('card_id', $request->card_id)->where('email', $request->email)->exists();
        if ($email_exists) {
            $response = [
                'status' => 'failed',
                'message' => trans('This email is already subscribed.')
            ];
    
            return response()->json($response);
        }

        // Save email in Newsletters table
        $newsletter = new Newsletter();
        $newsletter->newsletter_id = uniqid();
        $newsletter->card_id = $request->card_id ?? '';
        $newsletter->email = $request->email;
        $newsletter->save();

        // Return json
        $response = [
            'status' => 'success',
            'message' => trans('Thank you for subscribing to our newsletter.')
        ];

        return response()->json($response);
    }
}
