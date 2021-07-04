<?php
/**
 * Plugin Name: Advanced Dynamic Pricing for WooCommerce
 * Plugin URI:
 * Description: Manage discounts/deals for WooCommerce
 * Version: 3.2.5
 * Author: AlgolPlus
 * Author URI: https://algolplus.com/
 * WC requires at least: 3.6
 * WC tested up to: 5.4
 *
 * Text Domain: advanced-dynamic-pricing-for-woocommerce
 * Domain Path: /languages
 */

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//Stop if another version is active!
if (defined('WC_ADP_PLUGIN_FILE')) {
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('Please, <a href="plugins.php">deactivate</a> Free version of Advanced Dynamic Pricing For WooCommerce!',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></p>
        </div>
        <?php
    });

    return;
}

//main constants
define('WC_ADP_PLUGIN_FILE', basename(__FILE__));
define('WC_ADP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WC_ADP_PLUGIN_URL', plugins_url('', __FILE__));
define('WC_ADP_MIN_PHP_VERSION', '7.0.0');
define('WC_ADP_MIN_WC_VERSION', '3.6');
define('WC_ADP_VERSION', '3.2.5');

include_once "AutoLoader.php";
include_once "Factory.php";

\ADP\AutoLoader::register();

\ADP\Factory::get("PluginActions", __FILE__)->register();
\ADP\Factory::get("Loader");

if ( ! function_exists('adp_functions')) {
    function adp_functions()
    {
        return \ADP\BaseVersion\Includes\Functions::getInstance();
    }
}

if ( ! class_exists('WDP_Functions')) {
    include_once "BaseVersion/Includes/Legacy/class-wdp-functions.php";
}

if ( ! class_exists('WDP_Importer')) {
    include_once "BaseVersion/Includes/Legacy/class-wdp-importer.php";
}
