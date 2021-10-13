<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || exit;

global $product;
global $prices;

if ( !isset( $prices ))
	$prices = get_product_prices( $product );

?>




<?php if ( $product->is_in_stock()) :

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div class="add-to-cart">

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		
		
		<?php $time_units = $product->get_attribute( 'time_unit' ); ?>
		<?php if ( $time_units ) : ?>
			<?php $time_units = explode( ' | ', $time_units ); ?>

			
			<?php if ( count( $time_units ) > 1) : ?>
				<div class="form-row">
					
					<div class="form-label">
						<label>Thuê theo: </label>
					</div>

					<div class="form-input">
						<select class="form-select time-unit" id="time-unit" name="time_unit">
							<?php foreach ( $time_units as $time_unit ) : ?>
								<option value="<?php echo $time_unit ?>"><?php echo ucfirst(__( $time_unit, 'woocommerce' )) ?></option>
							<?php endforeach ?> 
						</select>
					</div>
				</div>

			<?php else: ?>
				<input type="hidden" id="time-unit" name="time_unit" value="<?php echo $time_units[0] ?>" />
			<?php endif ?>

		<?php endif ?>
		
		<?php do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>
			
		<div class="form-row">
			<div class="form-label">
				<label>Số lượng: </label>
			</div>

			<?php $stock = $product->get_stock_quantity(); ?>

			<div class="form-input quantity-input-increment">
				<input class="number-spinner" id="quantity-input" name="quantity" type="number" value="1" min="1" max="<?php echo $stock; ?>" step="1" />
			</div>
		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>	

		
		<div class="form-row">
			<div class="form-label">
				<label>Thời gian thuê:</label>
				<div class="validate"><span>(*Vui lòng chọn ngày)</span></div>
			</div>

			<div class="form-input time-period">

				<?php
					$time_min = $product->get_meta( 'time_min' );
					if ( !$time_min) $time_min = 1;

					$time_max = $product->get_meta( 'time_max' );
					
					$time_step = $product->get_meta( 'time_step' );
					if ( !$time_step ) $time_step = 1;
				?>

				<div class="duration">
					<input id="duration" class="number-spinner" name="duration" type="number" value="<?php echo $time_min ?>" min="<?php echo $time_min ?>" <?php if ($time_max) echo 'max="' . $time_max . '"'; ?> step="<?php echo $time_step; ?>" />
				</div>
	
				<span class="time-delimiter">
					<span class="time-unit">
						<?php							
							$time_unit = __( 'day', 'woocommerce' );
							if ( $time_units && count( $time_units ) > 0 )
								$time_unit = __( $time_units[0], 'woocommerce' );

							echo $time_unit;

						?>
					</span>, từ <?php echo __( 'day', 'woocommerce' ); ?>
				</span>

				<?php
					$hold_date = $product->get_meta( 'date_hold_to' );
					if ( $hold_date )
					{
						$hold_date = DateTime::createFromFormat( 'd/m/Y', $hold_date );
						$date_now = new DateTime();
						if ( $hold_date > $date_now )
							$hold_date_str = $hold_date->format( 'Y.m.d' );
					}
				?>

				<?php $booking_time = $product->get_meta( 'booking_time' ); ?>
				<?php if ( $booking_time ) : ?>
					<?php foreach ( $booking_time as $key => $booking ): ?>
						<?php
							$start_date = DateTime::createFromFormat( 'd/m/Y', $booking['start'] );
							$end_date = DateTime::createFromFormat( 'd/m/Y', $booking['end'] );
						?>
						<input type="hidden" class="booking-time" booking-id="<?php echo $key ?>" start-date="<?php echo $start_date->format('Y/m/d') ?>" end-date="<?php echo $end_date->format('Y/m/d') ?>" />
					<?php endforeach ?>
				<?php endif ?>


				<input type="text" id="date-from" name="_date_from" value="" <?php if ($hold_date_str) echo 'date-hold-to="' . $hold_date_str . '"' ?> placeholder="Ngày" autocomplete="off" />

			</div>
		</div>


		<div class="form-row">
			<div class="form-label">
				<label>Tùy chọn giao/nhận: </label>
			</div>

			<?php
				$vendor_login = $product->get_meta( 'vendor' );
				$vendor_profiles = get_vendor_profiles( $vendor_login );
				$address = $vendor_profiles['address']['street_2'] . ', ' . $vendor_profiles['address']['city'];

				global $vendor;
				if ( !$vendor )
					$vendor = get_product_vendor ( $product->post );

				$delivery_terms = get_user_meta( $vendor, 'delivery_terms', true );
			?>

			<div class="form-input radio-options">
				<?php if ( $delivery_terms ): ?>
					<div class='radio-option-row'>
						<input id="delivery_option_delivery" type="radio" name="delivery_option" value="delivery" checked />
						<label for="delivery_option_delivery">Giao hàng tận nơi.</label>
					</div>
				<?php endif ?>

				<?php if ( !empty( $address )) : ?>
					<div class='radio-option-row'>
						<input id="delivery_option_pick-up" type="radio" name="delivery_option" value="pick-up" <?php if ( !$delivery_terms ) echo 'checked'; ?> />
						<label for="delivery_option_pick-up">Nhận hàng tại <?php echo $address ?>.</label>
					</div>
				<?php endif ?>
			</div>
		</div>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="aha-button single_add_to_cart_button button alt">CHỌN THUÊ</button>

		<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
		<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />


		<input type="hidden" name="variation_id" class="variation_id" value="<?php echo get_product_default_variation( $product->id ); ?>" />
		
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
</div>

<!-- <form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0">
			<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<tr>
						<td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></td>
						<td class="value">
							<?php
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $options,
										'attribute' => $attribute_name,
										'product'   => $product,
									)
								);
								echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form> -->

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
?>

<?php else: ?>


<!-- Out of stock -->
<div class="stock-status">
	<?php echo wc_get_stock_html( $product ); // WPCS: XSS ok. ?>
</div>


<?php endif ?>
