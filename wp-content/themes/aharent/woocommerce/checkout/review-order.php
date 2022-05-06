<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table">
	<thead>
		<tr>
			<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-name"><?php echo __( 'Duration', 'aharent' ); ?></th>
			<th class="product-name"><?php echo __( 'From date', 'aharent' ); ?></th>
			<?php $payment_method = WC()->session->get( 'chosen_payment_method' ); ?>
			<?php if ( empty($payment_method) || 'cod' == $payment_method ) : ?>
				<th class="product-name"><?php echo __( 'Security deposit', 'aharent' ); ?></th>
				<th class="product-name"><?php echo __( 'Rental fee', 'aharent' ); ?></th>
			<?php else: ?>
				<th class="product-name"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<th class="product-total"><?php esc_html_e( 'Deposit', 'woocommerce' ); ?></th>
			<?php endif ?>
		</tr>
	</thead>
	<tbody>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		$delivery = false;

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			
			if ( 'delivery' === $cart_item['delivery-option'] )
				$delivery = true;

			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td class="product-name">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?>
						<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php // echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
					<td>
						<?php echo $cart_item['duration'] . ' ' . __( $cart_item['time-unit'] ? $cart_item['time-unit'] : 'day', 'aharent' );  ?>
					</td>
					<td>
						<?php echo $cart_item['date-from'] ?>
					</td>
					<?php if ( !$payment_method || 'cod' == $payment_method ) : ?>
						<td>
							<?php
								$security_deposit = $cart_item['security-deposit'];

								if ( $security_deposit && is_numeric( $security_deposit ))
									echo wc_price($security_deposit * $cart_item['quantity']);
								else
									echo '<span class="note">Thông báo khi xác nhận đơn hàng</span>'

							?>
						</td>
						
						<td>
							<?php
								$amount = $cart_item['rental_price'];

								if ( $cart_item['discount'] )
									if ( 'percentage' == $cart_item['discount']['type'] )
										$amount = $amount * ( 100 - $cart_item['discount']['value'] ) / 100;

								$cart_item['data']->set_price( $amount );
							?>
							<?php echo wc_price( $cart_item['data']->get_price() * $cart_item['quantity']) ?>
						</td>
					<?php else: ?>
						<td>
							<?php
								$amount = ($cart_item['rental_price'] - $cart_item['deposit'])  * $cart_item['quantity'];

								if ( $cart_item['discount'] )
									if ( 'percentage' == $cart_item['discount']['type'] )
										$amount = $amount * ( 100 - $cart_item['discount']['value'] ) / 100;
							
								echo wc_price( $amount );
								
							?>
						</td>
						<td class="product-total">
							<?php
								$amount = $cart_item['deposit'] * $cart_item['quantity'];

								if ( $cart_item['discount'] )
									if ( 'percentage' == $cart_item['discount']['type'] )
										$amount = $amount * ( 100 - $cart_item['discount']['value'] ) / 100;

								echo wc_price( $amount );
								
							?> 
						</td>
					<?php endif ?>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>

		<?php if ( $delivery ): ?>
		<tr>
			<td colspan="5"><span class="note">*<?php echo __( 'Phí vận chuyển sẽ được thông báo khi xác nhận đơn hàng', 'aharent' ); ?></span></td>
		</tr>
		<?php endif ?>
	</tbody>
	<tfoot>

		<?php if ( !WC()->customer->is_vat_exempt() ): ?>
		<tr class="cart-subtotal">
			<th colspan="<?php echo ('cod' == $payment_method) ? '4' : '4'; ?>"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>
		<?php endif ?>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th colspan="<?php echo ('cod' == $payment_method) ? '4' : '4'; ?>"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>


		<tr class="order-total">
			<th colspan="<?php echo ('cod' == $payment_method) ? '4' : '4'; ?>"><?php echo __( 'Total rental fees', 'aharent' ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>
