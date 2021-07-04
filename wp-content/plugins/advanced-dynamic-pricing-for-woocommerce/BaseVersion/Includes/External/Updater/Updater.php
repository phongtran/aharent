<?php

namespace ADP\BaseVersion\Includes\External\Updater;

use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Updater
{
    const DB_VERSION_KEY = "wdp_db_version";

    private static $db_updates = array(
        '2.2.3' => array(
            'migrateTo_2_2_3',
        ),
//		'3.0.0' => array(
//			'migrate_to_3_0_0',
//		),
        '3.1.0' => array(
            'migrate_options_to_3_1_0',
            'migrate_free_products_to_3_1_0',
        ),
        '3.2.1' => array(
            'migrate_options_to_3_2_1'
        )
    );

    public static function update()
    {
        $current_version = get_option(self::DB_VERSION_KEY, "");

        if (version_compare($current_version, WC_ADP_VERSION, '<')) {
            Factory::get("PluginActions", WC_ADP_PLUGIN_PATH . WC_ADP_PLUGIN_FILE)->install();

            foreach (self::$db_updates as $version => $update_callbacks) {
                if (version_compare($current_version, $version, '<')) {
                    foreach ($update_callbacks as $update_callback) {
                        UpdateFunctions::call_update_function($update_callback);
                    }
                }
            }

            update_option(self::DB_VERSION_KEY, WC_ADP_VERSION, false);
        }
    }
}
