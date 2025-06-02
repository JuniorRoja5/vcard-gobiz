<?php
namespace App\Providers;

use App\Services\PluginManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PluginManager::class, function ($app) {
            $pluginManager = new PluginManager();
            $pluginManager->loadPlugins();
            return $pluginManager;
        });
    }

    public function boot()
    {
        $pluginManager = app(PluginManager::class);

        foreach ($pluginManager->getPlugins() as $plugin) {

            $pluginServiceProviderPath = $plugin['path'] . "/{$plugin['plugin_id']}ServiceProvider.php";

            if (File::exists($pluginServiceProviderPath)) {
                // Extract namespace dynamically
                $serviceProviderClass = $this->getNamespaceFromFile($pluginServiceProviderPath) . "\\{$plugin['plugin_id']}ServiceProvider";

                if (class_exists($serviceProviderClass)) {                    
                    $this->app->register($serviceProviderClass);
                }
            }
        }
    }

    public function getNamespaceFromFile($filePath)
    {
        $lines = file($filePath);
        foreach ($lines as $line) {
            if (strpos($line, 'namespace') !== false) {
                return trim(str_replace(['namespace', ';'], '', $line));
            }
        }
        return null;
    }
}
