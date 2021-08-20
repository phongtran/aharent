<?php
/**
 * Import Woocommerce plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

namespace Smackcoders\SMWC;

if ( ! defined( 'ABSPATH' ) )
exit; // Exit if accessed directly

require_once('ImportHelpers.php');
require_once('WooCommerceMetaImport.php');

class ProductBundleMetaImport extends ImportHelpers {
	private static $product_bundle_meta_instance = null;

	public static function getInstance() {

		if (ProductBundleMetaImport::$product_bundle_meta_instance == null) {
			ProductBundleMetaImport::$product_bundle_meta_instance = new ProductBundleMetaImport;
			return ProductBundleMetaImport::$product_bundle_meta_instance;
		}
		return ProductBundleMetaImport::$product_bundle_meta_instance;
	}

	function set_product_bundle_meta_values($header_array ,$value_array , $map , $post_id ,$type , $line_number){
		global $wpdb;

		$woocommerce_meta_instance = WooCommerceMetaImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
		$data_array = [];

		$data_array = $helpers_instance->get_header_values($map , $header_array , $value_array);

		if($type == 'WooCommerce Product'){
			$woocommerce_meta_instance->woocommerce_meta_import_function($data_array, $post_id , $type , $line_number, $header_array, $value_array);
		}
	}
}
global $uci_woocomm_bundle_meta;
$uci_woocomm_bundle_meta = new ProductBundleMetaImport;
