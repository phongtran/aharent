<?php
/**
 * WP Ultimate CSV Importer plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

namespace Smackcoders\FCSV;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class ProductBundleMetaImport {
    private static $product_bundle_meta_instance = null;

    public static function getInstance() {
		
		if (ProductBundleMetaImport::$product_bundle_meta_instance == null) {
			ProductBundleMetaImport::$product_bundle_meta_instance = new ProductBundleMetaImport;
			return ProductBundleMetaImport::$product_bundle_meta_instance;
		}
		return ProductBundleMetaImport::$product_bundle_meta_instance;
    }

    function set_product_bundle_meta_values($header_array ,$value_array , $map , $post_id ,$type , $line_number , $mode){
        global $wpdb;
       
        $woocommerce_meta_instance = WooCommerceMetaImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
		$data_array = [];
			
		$data_array = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
        if(($type == 'WooCommerce Product')){
            $woocommerce_meta_instance->woocommerce_meta_import_function($data_array, $post_id , $type , $line_number , $mode , $header_array, $value_array);
        }
    }

}