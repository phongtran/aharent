<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
	Rental price: <?php echo $product->get_price_html(); ?>
</p>

<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
	Security deposit: <?php echo wc_price( $product->get_meta( '_security_deposit_amount' )); ?>
</p>

<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
	<?php
		$vendor_id = get_post_field( 'post_author',$product->id );
		$dokan_admin_percentage = get_user_meta( $vendor_id, 'dokan_admin_percentage' );
		
		if (!empty($dokan_admin_percentage))
			$dokan_admin_percentage = $dokan_admin_percentage[0];
	
	?>
	Deposit: <?php echo wc_price( $product->get_price() * $dokan_admin_percentage / 100 ); ?>
</p>
