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

<div class='price-wrapper d-flex'>

	<div class="price-item">
		
		<div class="price-item-title">
			<span>Giá thuê</span>
			<div class="loading-price">
				<div class="loading-icon">
					<img src="<?php echo get_template_directory_uri() ?>/assets/img/loading.gif" />
				</div>
			</div>
		</div>
		
		<div class="price-item-value rental">
			<span class="price-value"><?php echo $product->get_price_html(); ?></span><span class="rental-time">/ngày</span>
		</div>
	</div>


	<?php
		$_security_deposit = $product->get_meta( '_security_deposit_amount' );
		
		if ( !empty($_security_deposit) && ($_security_deposit > 0) ) :
	?>

	<div class="price-item">
		
		<div class="price-item-title">
			<span>Thế chân</span>
		</div>
		
		<div class="price-item-value">
			<?php echo wc_price( $product->get_meta( '_security_deposit_amount' )); ?>
		</div>
	</div>

	<?php endif ?>


	<div class="price-item deposit">
		
		<div class="price-item-title">
			<span>Đặt cọc</span>
			<div class="loading-price">
				<div class="loading-icon">
					<img src="<?php echo get_template_directory_uri() ?>/assets/img/loading.gif" />
				</div>
			</div>
		</div>
		
		<div class="price-item-value">
			
		</div>
	</div>

</div>
