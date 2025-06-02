<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Carbon\Carbon;
use Spatie\Health\Health;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Commands\RunHealthChecksCommand;

class ApplicationHealthCheckController extends Controller
{
    // Check application health
    public function health(Request $request, ResultStore $resultStore, Health $health)
    {
        // Queries
        $settings = Setting::first();
        $config = DB::table('config')->get();

        if ($request->has('fresh')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();

        return view('admin.pages.health-check.index', [
            'lastRanAt' => new Carbon($checkResults?->finishedAt),
            'checkResults' => $checkResults->storedCheckResults,
            'settings' => $settings,
            'config' => $config,
        ]);
    }
}
