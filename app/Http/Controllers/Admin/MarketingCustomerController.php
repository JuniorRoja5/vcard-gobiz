<?php

namespace App\Http\Controllers\Admin;

use App\Plan;
use App\User;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class MarketingCustomerController extends Controller
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
    // All Customers
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Use Query Builder for DataTables to work efficiently
            $data = User::where('role_id', 2)->orderBy('created_at', 'desc')
                ->select('user_id', 'name', 'email', 'email_verified_at', 'plan_id', 'plan_validity', 'status');
    
            return DataTables::of($data)
                // Add index column
                ->addIndexColumn()
                // Add custom "name" column
                ->addColumn('name', function ($row) {
                    return '<span class="fw-bold">' . $row->name . '</span>';
                })
                // Add custom "email" column
                ->addColumn('email', function ($row) {
                    return '<a href="mailto:' . $row->email . '">' . $row->email . '</a>';
                })
                // Add custom "email_verified_at" badge
                ->addColumn('email_verified_at', function ($row) {
                    return $row->email_verified_at 
                        ? '<span class="badge bg-green text-white fw-bold">' . trans('Verified') . '</span>' 
                        : '<span class="badge bg-red text-white fw-bold">' . trans('Not Verified') . '</span>';
                })
                // Add custom "subscriped_plan" column
                ->addColumn('subscriped_plan', function ($row) {
                    $plan = Plan::where('plan_id', $row->plan_id)->first();
                    $columnName = $plan ? trans($plan->plan_name) : trans('-');
                    return '<span class="fw-bold">' . trans($columnName) . '</span>';
                })
                // Add custom "valid_until" column
                ->addColumn('valid_until', function ($row) {
                    $columnName = $row->plan_validity ? Carbon::parse($row->plan_validity)->format('jS, F Y') : '-';
                    return '<span class="fw-bold">' . trans($columnName) . '</span>';
                })
                // Add custom "subscriped_badge" badge
                ->addColumn('subscriped_badge', function ($row) {
                    return $row->plan_id 
                        ? '<span class="badge bg-green text-white fw-bold">' . trans('Subscribed') . '</span>' 
                        : '<span class="badge bg-red text-white fw-bold">' . trans('Not Subscribed') . '</span>';
                })
                // Add custom "status" badge
                ->addColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<span class="badge bg-green text-white fw-bold">' . trans('Active') . '</span>'
                        : '<span class="badge bg-red text-white fw-bold">' . trans('Inactive') . '</span>';
                })
                // Add action buttons
                ->addColumn('action', function ($row) {
                    $viewUrl = route('admin.view.customer', $row->user_id);
                    $editUrl = route('admin.edit.customer', $row->user_id);
    
                    return '
                        <div class="dropdown text-end">
                            <button class="btn small-btn dropdown-toggle align-text-top" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                ' . trans('Actions') . '
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . $viewUrl . '">' . trans('View') . '</a>
                                <a class="dropdown-item" href="' . $editUrl . '">' . trans('Edit') . '</a>
                            </div>
                        </div>';
                })
                // Render raw HTML for certain columns
                ->rawColumns(['name', 'email', 'email_verified_at', 'subscriped_plan', 'valid_until', 'subscriped_badge', 'email_verified_at', 'status', 'action'])
                ->make(true);
        }

        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.marketing.customers.index', compact('settings', 'config'));
    }
}
