<?php
/**
 * WP Ultimate Exporter.
 *
 * WP Ultimate Exporter plugin file.
 *
 * @package   Smackcoders\SMEXP
 * @copyright Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: WP Ultimate Exporter
 * Version:     1.6.1
 * Plugin URI:  https://www.smackcoders.com/ultimate-exporter.html
 * Description: Backup tool to export all your WordPress data as CSV file. eCommerce data of WooCommerce, MarketPress, eCommerce, eShop, Custom Post and Custom field information along with default WordPress modules.
 * Author:      Smackcoders
 * Author URI:  https://www.smackcoders.com/wordpress.html
 * Text Domain: wp-ultimate-exporter
 * Domain Path: /languages
 * License:     GPL v3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Smackcoders\SMEXP;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

require_once('Plugin.php');
require_once('SmackExporterInstall.php');
require_once('exportExtensions/ExportExtension.php');
	
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if (is_plugin_active('wp-ultimate-exporter/wp-ultimate-exporter.php')) {	
	$plugin_pages = ['com.smackcoders.csvimporternew.menu'];
	include __DIR__ . '/wp-exp-hooks.php';
	global $plugin_ajax_hooks;
	
	$request_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
	$request_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	if (in_array($request_page, $plugin_pages) || in_array($request_action, $plugin_ajax_hooks)) {
		require_once('exportExtensions/ExportHandler.php');
		require_once('exportExtensions/CustomerReviewsExport.php');
		require_once('exportExtensions/PostExport.php');
		require_once('exportExtensions/WooComExport.php');	
	}
}

class ExpCSVHandler extends ExportExtension{

	protected static $instance = null,$install,$exp_instance;
	public $version = '1.6.1';

	public function __construct(){ 
		$this->plugin = Plugin::getInstance();
	}

	public static function getInstance() {
		if (ExpCSVHandler::$instance == null) {
			ExpCSVHandler::$instance = new ExpCSVHandler;	
			ExpCSVHandler::$install = ExpInstall::getInstance();
			ExpCSVHandler::$exp_instance = ExportExtension::getInstance();
			add_filter( 'plugin_row_meta' . plugin_basename( __FILE__ ),  array(ExpCSVHandler::$install, 'plugin_row_meta'), 10, 2 );
			add_action('plugin_action_links_' . plugin_basename( __FILE__ ), array(ExpCSVHandler::$install, 'plugin_row_meta'), 10, 3);

			self::init_hooks();

			return ExpCSVHandler::$instance;
		}
		return ExpCSVHandler::$instance;
	}

	public static function init_hooks() {

		add_action( 'admin_notices', array(ExpCSVHandler::$instance,'admin_notice_exporter_free'));
	}

	public static function admin_notice_exporter_free() {
		global $pagenow;
		$active_plugins = get_option( "active_plugins" );
		if ( $pagenow == 'plugins.php' && !in_array('wp-ultimate-csv-importer/wp-ultimate-csv-importer.php', $active_plugins) ) {
?>
				    <div class="notice notice-warning is-dismissible" >
					<p> WP Ultimate Exporter is an addon of <a href="https://wordpress.org/plugins/wp-ultimate-csv-importer" target="blank" style="cursor: pointer;text-decoration:none">WP Ultimate CSV Importer</a> plugin, kindly install it to continue using WP ultimate exporter. </p>
				    </div>
<?php 
		}
	}

	/**
	 * Init UserSmCSVHandlerPro when WordPress Initialises.
	 */
	public function init() {
		if(is_admin()) {
			// Init action.
			do_action('uci_init');
		}
	}
}

add_action( 'plugins_loaded', 'Smackcoders\\SMEXP\\onpluginsload' );


function onpluginsload(){
	$plugin_pages = ['com.smackcoders.csvimporternew.menu'];
	include __DIR__ . '/wp-exp-hooks.php';
	global $plugin_ajax_hooks;

	$request_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
	$request_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	if (in_array($request_page, $plugin_pages) || in_array($request_action, $plugin_ajax_hooks)) {
		$plugin = ExpCSVHandler::getInstance();
	}
}

$plugin_pages = ['com.smackcoders.csvimporternew.menu'];
include __DIR__ . '/wp-exp-hooks.php';
global $plugin_ajax_hooks;

$request_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
$request_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
if (in_array($request_page, $plugin_pages) || in_array($request_action, $plugin_ajax_hooks)) {
	global $export_class;
	$export_class = new ExpCSVHandler();
}
