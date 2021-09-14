<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div class="add-to-cart">

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );
		?>
	
			
			<div class="form-row">
				<div class="form-label">
					<label>Số lượng: </label>
				</div>

				<div class="form-input quantity-input-increment">
					<input id="quantity-input" name="quantity" type="number" value="1" min="1" max="10" step="1" />
				</div>
			</div>
			
			<?php
			
				// woocommerce_quantity_input(
				// 	array(
				// 		'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				// 		'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				// 		'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
				// 	)
				// );

			?>
		

		<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>	
		
		
		<?php 
			// wp_enqueue_style( 'style', get_template_directory_uri() . '/assets/css/minified/jquery.datetimepicker.css' );
			// wp_enqueue_script( 'script', get_template_directory_uri() . '/assets/js/minified/jquery.datetimepicker.js', array ( 'jquery' ));	
			wp_enqueue_style( 'style', get_template_directory_uri() . '/assets/css/unminified/rental-time-period.css' );
			wp_enqueue_script( 'script', get_template_directory_uri() . '/assets/js/unminified/rental-time-period.js');	
		?>
		
		<div class="form-row">
			<div class="form-label">
				<label>Thời gian thuê:</label>
				<div class="validate"><span>*Vui lòng chọn ngày</span></div>
			</div>

			<div class="form-input time-period">
		
				
					<input type="text" id="date-from" name="_date_from" value="" placeholder="Từ ngày" autocomplete="off">
					<input type="text" id="date-to" name="_date_to" value="" placeholder="Đến ngày" autocomplete="off">
				
			</div>
		</div>

		
		

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="aha-button single_add_to_cart_button button alt">CHỌN THUÊ</button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
</div>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
