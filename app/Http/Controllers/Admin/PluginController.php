<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PluginManager;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session as FacadesSession;
use Illuminate\Support\Facades\Validator;
use ZipArchive;

class PluginController extends Controller
{
    protected $pluginManager;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    // Index
    public function index()
    {
        // Queries
        $settings = Setting::active()->first();
        $config   = DB::table('config')->get();

        // Load plugins
        $this->pluginManager->loadPlugins();

        // Get all plugins
        $plugins = $this->pluginManager->getPlugins();

        return view('admin.pages.plugins.index', compact('settings', 'config', 'plugins'));
    }

    public function deletePlugin(Request $request, $pluginName)
    {

        if ($this->pluginManager->deletePlugin($pluginName)) {
            return redirect()->back()->with('success', trans('Deleted!'));
        }

        return redirect()->back()->with('failed', trans('Plugin not found or could not be deleted.'));
    }

    public function upload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'zip_file' => 'required|mimes:zip|max:' . env("SIZE_LIMIT") . '',
        ]);

        if ($validator->fails()) {
            $limit = env("SIZE_LIMIT");

            FacadesSession::flash('failed', trans('Please upload a valid zip file. File size should be less than :limit Kb Or Increase the upload size limit in settings Panel!', ['limit' => $limit]));
            return response()->json(['message' => trans('Plugin Installation failed!')]);
        }

        $zipFile = $request->file('zip_file');

        // if zip file found
        if (! $zipFile) {
            FacadesSession::flash('failed', trans('Installation failed. File not found!'));
            return response()->json(['message' => trans('Plugin Installation failed!')]);
        }

        $zipName  = pathinfo($zipFile->getClientOriginalName(), PATHINFO_FILENAME);
        $download = uniqid();
        // Store zip file at storage folder
        $zipPath = storage_path('./app/plugins/' . $download . '.zip');
        file_put_contents($zipPath, $zipFile->get());

        $zip = new ZipArchive;
        $out = $zip->open($zipPath);

        if ($out !== true) {
            // remove zip file
            unlink($zipPath);
            FacadesSession::flash('failed', trans('Installation failed. File is corrupted!'));
            return response()->json(['message' => trans('Plugin Installation failed!')]);
        }

        // Check if Views/index.blade.php exists inside ZIP
        $fileStrictValidationCount = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {

            $fileName = $zip->getNameIndex($i);

            // Check if Views/index.blade.php exists inside ZIP
            if ($fileName === $zipName . "/Views/index.blade.php") {
                $fileStrictValidationCount++;
            }

            // Check if routes.php exists inside ZIP
            if ($fileName === $zipName . "/routes.php") {
                $fileStrictValidationCount++;

            }

            // Check if plugin.json exists inside ZIP
            if ($fileName === $zipName . "/plugin.json") {
                $fileStrictValidationCount++;

            }

            // Check if controller file is exists inside ZIP. Controller name is zip name
            if ($fileName === $zipName . "/Controllers/" . $zipName . "Controller.php") {
                $fileStrictValidationCount++;
            }

            // Check if controllers folder exists inside ZIP
            if ($fileName === $zipName . "/Controllers/") {
                $fileStrictValidationCount++;
            }

            // Check if views folder exists inside ZIP
            if ($fileName === $zipName . "/Views/") {
                $fileStrictValidationCount++;
            }

        }

        // If fileStrictValidationCount is not equal to 6 File found false
        if ($fileStrictValidationCount != 6) {
            // Remove zip file
            unlink($zipPath);
            FacadesSession::flash('failed', trans('Installation failed. Some files are missing!'));
            return response()->json(['message' => trans('Plugin Installation failed!')]);
        }

        $extractPath = base_path('plugins'); // Extract to plugins directory
        $zip->extractTo($extractPath);
        $zip->close();
        unlink($zipPath);

        FacadesSession::flash('success', trans('Plugin installation success!'));
        return response()->json(['message' => trans('Plugin installation success!')]);
    } 
}
