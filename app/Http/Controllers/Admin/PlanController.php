<?php

namespace App\Http\Controllers\Admin;

use App\Plan;
use App\Setting;
use App\Currency;
use App\Classes\SavePlan;
use App\Classes\UpdatePlan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class PlanController extends Controller
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

    // All Plans
    public function plans(Request $request)
    {
        // Queries
        $settings = Setting::active()->first();
        $config = DB::table('config')->get();
        $plans = Plan::where('status', '!=', 2)->get();

        // Currency symbol
        $currencies = Currency::pluck('symbol', 'iso_code')->toArray();
        $symbol = $currencies[$config[1]->config_value] ?? '';

        if ($request->ajax()) {
            return DataTables::of($plans)
                ->addIndexColumn()
                ->addColumn('plan_type', function ($plan) {
                    return '<span class="badge bg-primary text-white">' . __($plan->plan_type ?: "-") . '</span>';
                })
                ->addColumn('plan_name', function ($plan) {
                    return '<span class="fw-bold">' . __($plan->plan_name) . '</span>';
                })
                ->addColumn('plan_price', function ($plan) use ($symbol) {
                    // Price
                    $price = $plan->plan_price != 0 ? $symbol . $plan->plan_price : __('Free');
                    return '<span class="fw-bold">' . $price . '</span>';
                })
                ->addColumn('validity', function ($plan) {
                    return '<span class="fw-bold">' . match ($plan->validity) {
                        9999 => __('Forever'),
                        31, 30   => __('Monthly'),
                        366, 365  => __('Yearly'),
                        default => $plan->validity . ' ' . __('Days'),
                    } . '</span>';
                })
                ->addColumn('status', function ($plan) {
                    $statusLabel = $plan->status == 0 ? 'bg-red' : 'bg-green';
                    $statusText = $plan->status == 0 ? __('Discontinued') : __('Active');
                    return '<span class="badge ' . $statusLabel . ' text-white">' . $statusText . '</span>';
                })
                ->addColumn('action', function ($plan) {
                    $actions = '<span class="dropdown">
                            <button class="btn small-btn dropdown-toggle align-text-top" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="' . route('admin.edit.plan', $plan->plan_id) . '">' . __('Edit') . '</a>';
                    if ($plan->status == 0) {
                        $actions .= '<a class="dropdown-item" href="#" onclick="getPlan(`' . $plan->plan_id . '`, `activated`); return false;">' . __('Activate') . '</a>';
                    } else {
                        $actions .= '<a class="dropdown-item" href="#" onclick="getPlan(`' . $plan->plan_id . '`, `deactivated`); return false;">' . __('Deactivate') . '</a>';
                    }
                    $actions .= '<a class="dropdown-item" href="#" onclick="deletePlan(`' . $plan->plan_id . '`); return false;">' . __('Delete') . '</a>';
                    $actions .= '</div>
                        </span>';
                    return $actions;
                })
                ->rawColumns(['plan_type', 'plan_name', 'plan_price', 'validity', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pages.plans.plans', compact('settings', 'config'));
    }

    // Add Plan
    public function addPlan()
    {
        // Queries
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        return view('admin.pages.plans.add-plan', compact('settings', 'config'));
    }

    // Save Plan
    public function savePlan(Request $request)
    {
        // Save
        $plan = new SavePlan;
        $plan->create($request);

        // Check result
        if ($plan->result != 0) {
            return redirect()->route('admin.add.plan')->with('success', trans('Created!'));
        } else {
            return redirect()->route('admin.add.plan')->with('failed', trans('Creation failed!'));
        }
    }

    // Edit Plan
    public function editPlan(Request $request, $id)
    {
        // Queries
        $plan_id = $request->id;
        $plan_details = Plan::where('plan_id', $plan_id)->first();
        $settings = Setting::where('status', 1)->first();

        if ($plan_details == null) {
            return redirect()->route('admin.plans')->with('failed', trans('Not Found!'));
        } else {
            return view('admin.pages.plans.edit-plan', compact('plan_details', 'settings'));
        }
    }

    // Update Plan
    public function updatePlan(Request $request)
    {
        // Update
        $updatePlan = new UpdatePlan;
        $updatePlan->create($request);

        // Check result
        if ($updatePlan->result != 0) {
            return redirect()->route('admin.edit.plan', $request->plan_id)->with('success', trans('Updated!'));
        } else {
            return redirect()->route('admin.edit.plan', $request->plan_id)->with('failed', trans('Updated Failed!'));
        }
    }

    // Status Plan
    public function statusPlan(Request $request)
    {
        // Queries
        $plan_details = Plan::where('plan_id', $request->query('id'))->first();

        if ($plan_details == null) {
            return redirect()->route('admin.plans')->with('failed', trans('Not Found!'));
        }

        if ($plan_details->status == 0) {
            $status = 1;
        } else {
            $status = 0;
        }

        Plan::where('plan_id', $request->query('id'))->update(['status' => $status]);

        return redirect()->route('admin.plans')->with('success', trans('Updated!'));
    }

    // Delete Plan
    public function deletePlan(Request $request)
    {
        // Queries
        Plan::where('plan_id', $request->query('id'))->update(['status' => 2]);

        return redirect()->route('admin.plans')->with('success', trans('Deleted!'));
    }
}
