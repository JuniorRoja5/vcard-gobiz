<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SupportActivatorController extends Controller
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


    // Support Activator
    public function index()
    {
        // Queries
        $settings = Setting::first();
        $config = DB::table('config')->get();

        return view('admin.pages.support-activate.index', compact('settings', 'config'));
    }

    // Upgrade Support
    public function upgradeSupport(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'support_code' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Check support code
        $support_code = $request->support_code;

        try {

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://verify.nativecode.in/support-validation');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                'purchase_code' => env('PURCHASE_CODE'),
                'license_key' => $support_code
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            // Decode response
            $response = json_decode($response, true);


            // Check response
            if (isset($response['status']) && $response['status'] == true) {
                // Update support license code
                DB::table('config')->where('config_key', 'support_license_code')->update([
                    'config_value' => $support_code
                ]);

                return redirect()->route('admin.support.activate')->with('success', trans('Support has been successfully upgraded and your support is valid until the date '.$response['support_until'].'.'));
            } else {
                return redirect()->route('admin.support.activate')->with('failed', trans($response['message'] ?? $response['error']));
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.support.activate');
        }
    }
}
