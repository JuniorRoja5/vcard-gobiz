<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class PluginManager
{
    protected $plugins = [];

    // constructor
    public function __construct()
    {
        $this->loadPlugins();
    }

    public function loadPlugins()
    {
        $pluginDirectories = File::directories(base_path('plugins'));

        //reset the plugins array
        $this->plugins = [];

        foreach ($pluginDirectories as $directory) {
            $pluginJsonPath = $directory . '/plugin.json';


            if (File::exists($pluginJsonPath)) {
                $pluginData = json_decode(File::get($pluginJsonPath), true);
                $pluginData['path'] = $directory;

                // $this->plugins[$pluginData['name']] = $pluginData;

                // add plugin to the list
                $this->plugins[] = $pluginData; 

            }
        }
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    public function deletePlugin($id)
    {
        // it has array of plugin json
        // dd($this->plugins[0]);

        //check if the plugin exists and remove
        for($i = 0; $i < count($this->plugins); $i++) {
            if ($this->plugins[$i]['plugin_id'] == $id) {
                $pluginPath = $this->plugins[$i]['path'];
                // Delete the plugin directory
                File::deleteDirectory($pluginPath);
                unset($this->plugins[$i]);
                return true;
            }
        }
        return false;
    }
}