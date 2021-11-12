<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>


<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php  do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				
				<?php do_action( 'woocommerce_checkout_billing' ); ?>

				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>

			<div class="col-2">
				
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
	
				<div class="title-bar">
					<h3 id="order_review_heading"><?php esc_html_e( 'Đơn hàng đặt thuê & Thanh toán', 'woocommerce' ); ?></h3>
				</div>
				
				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</div>

		

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>
	
	

</form>



<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<div class="popular-products">
	<div class="title-bar">
		<h3>SẢN PHẨM CÙNG LOẠI</h3>
	</div>

		<?php
			woocommerce_product_loop_start();

			$args = get_recommended_products_query();

			$loop = new WP_Query( $args );
			shuffle( $loop->posts );
			

			while ( $loop->have_posts() ) :
				$loop->the_post();
				
				wc_get_template_part( 'content', 'product' );
			endwhile;

			wp_reset_query();

			woocommerce_product_loop_end();
		?>

	<div class="button-more">
		<a href="<?php echo get_permalink( get_option( 'woocommerce_shop_page_id' )); ?>">
			<button class="aha-button" type="button">Xem thêm</button>
		</a>
	</div>
	
</div>
