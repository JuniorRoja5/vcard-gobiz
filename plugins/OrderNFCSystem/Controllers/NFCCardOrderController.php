<?php
namespace Plugins\OrderNFCSystem\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NFCCardOrderController extends Controller
{
    // Enable/Disable NFC Card Orders 
    public function index()
    {
        // Queries
        $settings = Setting::active()->first();
        $config   = DB::table('config')->get();

        // Update Enable/Disable NFC Card Orders
        return view()->file(base_path('plugins/OrderNFCSystem/Views/index.blade.php'), compact('settings', 'config'));
    }

    // Update Enable/Disable NFC Card Orders
    public function update(Request $request)
    {

        // Check if the form is valid
        $nfcCardOrderSystem = $request->enable_disable_nfc_card_order == '1' ? 1 : 0;

        // Update the database
        DB::table('config')->where('config_key', 'nfc_order_system')->update(['config_value' => $nfcCardOrderSystem]);

        return redirect()->route('admin.plugin.status.nfc.cards.order')->with('success', trans('NFC Card Order System Updated Successfully!'));
    }
}
