<?php
/**
 * WP Ultimate Exporter plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

namespace Smackcoders\SMEXP;

if ( ! defined( 'ABSPATH' ) )
exit; // Exit if accessed directly

class ExpInstall {

	protected static $instance = null,$smack_instance,$tables_instance;
	private static $db_updates = array();
	/**
	 * SmackCSVInstall Constructor
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 * SmackCSVInstall Instance
	 */
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$smack_instance = new ExpInstall();
		}
		return self::$instance;
	}

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Check WPUltimateCSVImporterPro version.
	 */
	public static function check_version() {
		if ( get_option( 'ULTIMATE_CSV_IMP_VERSION' ) != SmackUCI()->version )  {
			self::install();
			do_action( 'sm_uci_pro_updated' );
		}
	}

	/**
	 * Install actions when a update button is clicked.
	 */
	public static function install_actions() {
		if ( ! empty( sanitize_text_field($_GET['do_update_sm_uci_pro'] )) ) {
			self::update();
		}
	}

	/**
	 * Show notice stating update was successful.
	 */
	public static function updated_notice() {
		?>
			<div class='notice updated uci-message wc-connect is-dismissible'>
			<p>Ultimate CSV Importer PRO data update complete. Thank you for updating to the latest version!</p>
			</div>
			<?php
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param       mixed $links Plugin Row Meta
	 * @param       mixed $file  Plugin Base file
	 * @return      array
	 */
	public static function plugin_row_meta( $links, $file ) {
		$active_plugins = get_option('active_plugins');
		if(in_array('wp-ultimate-csv-importer/wp-ultimate-csv-importer.php', $active_plugins)){
			return $links;
		}
		else{
		$row_meta = array(
			'install_csv_importer' => '<a style="font-weight: bold;color: #d54e21;font-size: 105%;" href="' . esc_url( apply_filters( 'install_csv_importer',  'https://wordpress.org/plugins/wp-ultimate-csv-importer/' ) ) . '" title="' . esc_attr( __( 'Install CSV Importer', 'wp-ultimate-csv-importer' ) ) . '" target="_blank">' . __( 'Install CSV Importer', 'wp-ultimate-csv-importer' ) . '</a>'
	);

		return array_merge( $row_meta, $links );
	}
	}

}
