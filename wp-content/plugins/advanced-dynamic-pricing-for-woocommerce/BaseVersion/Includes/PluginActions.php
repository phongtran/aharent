<?php

namespace ADP\BaseVersion\Includes;

use ADP\BaseVersion\Includes\Admin\Settings;
use ADP\BaseVersion\Includes\Common\Database;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class PluginActions
{
    /**
     * @var string
     */
    protected $pluginFileFullPath;

    /**
     * @param string|null $pluginFileFullPath
     */
    public function __construct($pluginFileFullPath)
    {
        $this->pluginFileFullPath = $pluginFileFullPath;
    }

    /**
     *  Only a static class method or function can be used in an uninstall hook.
     */
    public function register()
    {
        if ($this->pluginFileFullPath && file_exists($this->pluginFileFullPath)) {
            register_activation_hook($this->pluginFileFullPath, array($this, 'install'));
            register_deactivation_hook($this->pluginFileFullPath, array($this, 'deactivate'));
        }
    }

    public function install()
    {
        Database::create_database();
        do_action('wdp_install');
    }

    public function deactivate()
    {
        delete_option(Settings::$activation_notice_option);
    }

    /**
     * Method required for tests
     */
    public function uninstall()
    {
        $file = WC_ADP_PLUGIN_PATH . 'uninstall.php';
        if (file_exists($file)) {
            include_once $file;
        }
    }
}
