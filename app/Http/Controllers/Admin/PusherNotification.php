<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Events\NotificationEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\PusherBeamsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SubscriberNotification;

class PusherNotification extends Controller
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

    public function index()
    {
        // Queries
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.marketing.pusher-notification.index', compact('settings', 'config'));
    }

    // Send Notification
    public function send(Request $request, PusherBeamsService $beamsService)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'image' => 'required|MIMEs:png,jpeg,jpg|max:' . env('SIZE_LIMIT'),
            'target_url' => 'required|string|max:255',
        ]);

        // Check if the required keys are missing
        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Upload image
        $image = $request->file('image');
        $image->move(public_path('uploads/notifications'), $image->getClientOriginalName());
        // Get uploaded image url
        $imageUrl = '/uploads/notifications/' . $image->getClientOriginalName();

        // Parameters
        $title = $request->title;
        $message = $request->message;
        $imageUrl = url($imageUrl);
        $targetUrl = $request->target_url;

        $notification = [
            'title' => $title,
            'body'  => $message,
            'icon'  => $imageUrl,
            'deep_link' => $targetUrl, // Optional
        ];

        // Check if $beamsService is properly instantiated
        if ($beamsService) {
            // Send notification to the 'global' interest
            $beamsService->broadcastToInterest('global', $notification);
        } else {
            // Handle error if beamsService is null
            return redirect()->back()->with('failed', trans('Pusher Beams instance ID or secret key is missing.'));
        }

        return redirect()->route('admin.marketing.pusher.notification');
    }
}
