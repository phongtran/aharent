<?php
/**
 * Security deposit tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

// global $post;

global $post;

$vendor = get_product_vendor ( $post );

$vendor_rental_terms = get_user_meta( $vendor, 'vendor_rental_terms', true );


if ( !empty( $vendor_rental_terms) )
    echo $vendor_rental_terms;
