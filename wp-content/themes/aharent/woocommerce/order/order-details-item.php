<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}

$payment_method = $order->get_payment_method();

global $delivery;
global $pickup;
$delivery = false;
$delivery_option = $item->get_meta( 'delivery-option' );
if ( $delivery_option == 'delivery')
	$delivery = true;
elseif ( $delivery_option == 'pick-up' )
{
	if ( !$pickup)
		$pickup = array();

	// Get vendor address
	$vendor_login = $item->get_product()->get_meta( 'vendor' );
	
	$vendor_profiles = get_vendor_profiles( $vendor_login );
	$address = $vendor_profiles['address']['street_1'] . ', ' . $vendor_profiles['address']['street_2'] . ', ' . $vendor_profiles['address']['city'];
	$pickup[$item->get_name()] = $address;
}
?>


<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">

	<td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

		echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ) );

		$qty          = $item->get_quantity();
		$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

		if ( $refunded_qty ) {
			$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
		} else {
			$qty_display = esc_html( $qty );
		}

		echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $qty_display ) . '</strong>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

		// wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
		?>
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo $item->get_meta( 'duration' ) . ' ' . __( $item->get_meta( 'time-unit' ), 'aharent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo $item->get_meta( 'date-from' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</td>

	<?php if ( 'cod' == $payment_method ): ?>
		<td class="woocommerce-table__product-total product-total">
			<?php
				$security_deposit = $item->get_meta( 'security-deposit' );
				if ( !$security_deposit )
					echo '<span class="note">*Thông báo khi xác nhận đơn hàng</span>';
				else
					echo wc_price( $security_deposit * $qty );
			?>
		</td>
		<td class="woocommerce-table__product-total product-total">
			<?php
				echo wc_price( $item->get_meta('_rental_price') * $qty );
				// echo $order->get_formatted_line_subtotal( $item ) ; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</td>
	<?php else: ?>
		<td class="woocommerce-table__product-total product-total">
			<?php echo wc_price( ($item->get_meta( '_rental_price' ) - $item->get_meta( 'deposit')) * $qty ) ; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</td>

		<td class="woocommerce-table__product-total product-total">
			<?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</td>
	<?php endif ?>

</tr>

<?php if ( $show_purchase_note && $purchase_note ) : ?>

<tr class="woocommerce-table__product-purchase-note product-purchase-note">

	<td colspan="2"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>

</tr>

<?php endif; ?>
