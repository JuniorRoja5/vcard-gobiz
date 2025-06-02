<?php

namespace App\Http\Controllers\Admin;

use App\Gateway;
use App\Setting;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
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

    // All Payment Methods
    public function paymentMethods(Request $request)
    {
        if ($request->ajax()) {
            $payment_methods = Gateway::where('status', '!=', -1)->orderBy('created_at', 'desc')->get();

            return DataTables::of($payment_methods)
                ->addIndexColumn()
                ->addColumn('payment_gateway_logo', function ($payment_method) {
                    return '<span class="avatar me-2" style="background-image: url(' . asset($payment_method->payment_gateway_logo) . ')"></span>';
                })
                ->addColumn('payment_gateway_name', function ($payment_method) {
                    return __($payment_method->payment_gateway_name);
                })
                ->addColumn('is_status', function ($payment_method) {
                    if ($payment_method->is_status == 'disabled') {
                        return '<span class="badge badge-outline text-red">' . __('Not Installed Yet') . '</span>';
                    } else {
                        return '<span class="badge badge-outline text-green">' . __('Installed') . '</span>';
                    }
                })
                ->addColumn('status', function ($payment_method) {
                    if ($payment_method->status == 0) {
                        return '<span class="badge bg-red text-white">' . __('Deactivated') . '</span>';
                    } else {
                        return '<span class="badge bg-green text-white">' . __('Activated') . '</span>';
                    }
                })
                ->addColumn('action', function ($payment_method) {
                    // Edit 
                    $editUrl = route('admin.edit.payment.method', $payment_method->payment_gateway_id);
                    $actionBtn = '<a class="dropdown-item" href="' . $editUrl . '">' . __('Edit') . '</a>';

                    // Configure
                    $configureUrl = route('admin.configure.payment', $payment_method->payment_gateway_id);
                    $actionBtn .= '<a class="dropdown-item" href="' . $configureUrl . '">' . __('Configure') . '</a>';

                    // Activate / Deactivate
                    if ($payment_method->status == 0) {
                        $actionBtn .= '<a class="dropdown-item" href="#" onclick="getPaymentMethod(`' . $payment_method->payment_gateway_id . '`, `activated`); return false;">' . __('Activate') . '</a>';
                    } else {
                        $actionBtn .= '<a class="dropdown-item" href="#" onclick="getPaymentMethod(`' . $payment_method->payment_gateway_id . '`, `deactivated`); return false;">' . __('Deactivate') . '</a>';
                    }

                    // Delete
                    $actionBtn .= '<a class="dropdown-item" href="#" onclick="getPaymentMethod(`' . $payment_method->payment_gateway_id . '`, `deleted`); return false;">' . __('Delete') . '</a>';

                    return '<a class="btn act-btn dropdown-toggle actions-buttons-column" href="#" data-toggle="dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-expanded="false">' . __('Actions') . '</a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <div class="nav-item dropdown">
                                    ' . $actionBtn . '
                                </div>
                            </div>';
                })
                ->rawColumns(['payment_gateway_logo', 'is_status', 'status', 'action'])
                ->make(true);
        }

        // Queries
        $settings = Setting::where('status', 1)->first();

        return view('admin.pages.payment-methods.payment-methods', compact('settings'));
    }

    // Add Payment Method
    public function addPaymentMethod()
    {
        $settings = Setting::where('status', 1)->first();
        return view('admin.pages.payment-methods.add-payment-method', compact('settings'));
    }

    // Save Payment Method
    public function savePaymentMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_gateway_logo' => 'required|payment_gateway_logo|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'payment_gateway_name' => 'required',
            'client_id' => 'required',
            'secret_key' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        $payment_gateway_logo = 'img/payment-method/' . 'IMG-' . time() . '.' . $request->payment_gateway_logo->extension();

        $request->payment_gateway_logo->move(public_path('img/payment-method'), $payment_gateway_logo);

        $paymentMethod = new Gateway;
        $paymentMethod->payment_gateway_id = uniqid();
        $paymentMethod->payment_gateway_logo = $payment_gateway_logo;
        $paymentMethod->payment_gateway_name = $request->payment_gateway_name;
        $paymentMethod->client_id = $request->client_id;
        $paymentMethod->secret_key = $request->secret_key;
        $paymentMethod->save();

        return redirect()->route('admin.add.payment.method')->with('success', trans('Created!'));
    }

    // Edit Payment Method
    public function editPaymentMethod(Request $request, $id)
    {
        $gateway_id = $request->id;
        if ($gateway_id == null) {
            return view('errors.404');
        } else {
            $gateway_details = Gateway::where('payment_gateway_id', $gateway_id)->first();
            $settings = Setting::where('status', 1)->first();
            return view('admin.pages.payment-methods.edit-payment-gateway', compact('gateway_details', 'settings'));
        }
    }

    // Update Payment Method
    public function updatePaymentMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_gateway_id' => 'required',
            'payment_gateway_name' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->with('failed', $validator->messages()->all()[0])->withInput();
        }

        // Check payment method image
        if (isset($request->payment_gateway_image)) {
            // Image validatation
            $validator = Validator::make($request->all(), [
                'payment_gateway_image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp|max:' . env("SIZE_LIMIT") . '',
            ]);

            if ($validator->fails()) {
                return back()->with('failed', $validator->messages()->all()[0])->withInput();
            }

            // get profile image
            $payment_gateway_image = $request->payment_gateway_image->getClientOriginalName();
            $UploadPaymentGatewayImage = pathinfo($payment_gateway_image, PATHINFO_FILENAME);
            $UploadExtension = pathinfo($payment_gateway_image, PATHINFO_EXTENSION);

            // Upload image
            if ($UploadExtension == "jpeg" || $UploadExtension == "png" || $UploadExtension == "jpg" || $UploadExtension == "gif" || $UploadExtension == "svg") {
                // Upload image
                $payment_gateway_image = 'img/payment-method/' . 'IMG-' . $request->payment_gateway_image->getClientOriginalName() . '-' . time() . '.' . $request->payment_gateway_image->extension();
                $request->payment_gateway_image->move(public_path('img/payment-method'), $payment_gateway_image);

                // Update user profile image
                Gateway::where('payment_gateway_id', $request->payment_gateway_id)->update([
                    'payment_gateway_logo' => $payment_gateway_image,
                    'display_name' => $request->payment_gateway_name
                ]);
            }
        }

        Gateway::where('payment_gateway_id', $request->payment_gateway_id)->update([
            'payment_gateway_name' => $request->payment_gateway_name,
            'display_name' => $request->payment_gateway_name
        ]);

        return redirect()->route('admin.edit.payment.method', $request->payment_gateway_id)->with('success', trans('Updated!'));
    }

    // Update Payment Method
    public function deletePaymentMethod(Request $request)
    {
        // Queries
        $payment_gateway_details = Gateway::where('payment_gateway_id', $request->query('id'))->first();

        // Check gateways exist
        if (!$payment_gateway_details) {
            return redirect()->route('admin.payment.methods')->with('failed', __('Not Found!'));
        }

        if ($request->query('status') == 'deleted') {
            $status = -1;
        } else if($request->query('status') == 'deactivated') {
            $status = 0;
        } else {
            $status = 1;
        }
        
        // Update payment method
        Gateway::where('payment_gateway_id', $request->query('id'))->update([
            'status' => $status
        ]);

        return redirect()->route('admin.payment.methods', $request->query('id'))->with('success', trans('Updated!'));
    }
}
