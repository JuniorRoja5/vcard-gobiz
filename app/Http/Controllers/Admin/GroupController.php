<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Group;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Use Query Builder for DataTables to work efficiently
            $data = Group::orderBy('created_at', 'desc')->where('status', '!=', -1)->select('group_id', 'group_name', 'group_desc', 'emails', 'status');

            return DataTables::of($data)
                // Add index column
                ->addIndexColumn()
                // Add custom "group_name" column
                ->addColumn('group_name', function ($row) {
                    return '<a href="' . route('admin.marketing.groups.view', $row->group_id) . '"><span class="fw-bold">' . $row->group_name . '</span></a>';
                })
                ->addColumn('email_count', function ($row) {
                    // Count the number of emails in the group
                    $emailCounts = json_decode($row->emails, true);
                    $emailCount = count($emailCounts);
                    return '<span class="badge bg-blue text-white fw-bold">' . $emailCount . '</span>';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-red text-white text-white">' . trans('Disabled') . '</span>';
                    } else {
                        return '<span class="badge bg-green text-white text-white">' . trans('Active') . '</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $actions = '<span class="dropdown">
                                    <button class="btn small-btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">' . __('Actions') . '</button>
                                    <div class="actions dropdown-menu dropdown-menu-end">
                                        <a href="' . route('admin.marketing.groups.view', $row->group_id) . '" class="dropdown-item">' . trans('View') . '</a>
                                        <a href="' . route('admin.marketing.groups.edit', $row->group_id) . '" class="dropdown-item">' . trans('Edit') . '</a>
                                        <a onclick="onDelete(`' . $row->group_id . '`); return false;" class="dropdown-item">' . trans('Delete') . '</a>
                                    </div>
                                </span>';

                    return $actions;
                })
                ->rawColumns(['group_name', 'group_desc', 'email_count', 'status', 'action'])
                ->make(true);
        }

        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.marketing.group.index', compact('settings', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function createGroup()
    {
        // Queries
        $marketingCustomers = User::where('role_id', 2)->get();
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.marketing.group.create', compact('marketingCustomers', 'settings', 'config'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveGroup(Request $request)
    {
        $this->validate($request, [
            'group_name' => 'required|string|max:255',
            'group_desc' => 'required|string|max:255',
            'emails' => 'required|array'
        ]);

        // Don't save "all" option
        $request->emails = array_filter($request->emails, function ($email) {
            return $email !== "all";
        });

        // Save group
        $group = new Group;
        $group->group_id = uniqid();
        $group->group_name = ucfirst($request->group_name);
        $group->group_desc = ucfirst($request->group_desc);
        $group->emails = json_encode($request->emails);
        $group->save();

        return redirect()->route('admin.marketing.groups')->with('success', trans('Created!'));
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewGroup($id)
    {
        // Queries
        $group = Group::where('group_id', $id)->first();
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.marketing.group.view', compact('group', 'settings', 'config'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editGroup($id)
    {
        // Queries
        $marketingCustomers = User::where('role_id', 2)->get();
        $group = Group::where('group_id', $id)->first();
        $settings = Setting::where('status', 1)->first();
        $config = DB::table('config')->get();

        return view('admin.pages.marketing.group.edit', compact('marketingCustomers', 'group', 'settings', 'config'));
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateGroup(Request $request, $id)
    {
        $this->validate($request, [
            'group_name' => 'required|string|max:255',
            'group_desc' => 'required|string|max:255',
            'emails' => 'required|array'
        ]);

        // Don't save "all" option
        $request->emails = array_filter($request->emails, function ($email) {
            return $email !== "all";
        });

        // Update
        $group = Group::where('group_id', $id)->first();
        $group->group_name = ucfirst($request->group_name);
        $group->group_desc = ucfirst($request->group_desc);
        $group->emails = json_encode($request->emails);
        $group->save();

        return redirect()->route('admin.marketing.groups')->with('success', trans('Updated!'));
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteGroup(Request $request)
    {
        // Update status
        $group = Group::where('group_id', $request->query('id'))->first();
        $group->status = -1;
        $group->save();

        return redirect()->route('admin.marketing.groups')->with('success', trans('Deleted!'));
    }
}
