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
global $time_unit;

if ( ! $product->is_purchasable() ) {
	return;
}
?>

<?php //if ( $product->is_in_stock() ) : ?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div class="add-to-cart">

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );
		?>
	
			
		<div class="form-row">
			<div class="row-component">
				<div class="form-label">
					<label>Số lượng: </label>
				</div>

				<?php $stock = $product->get_stock_quantity(); ?>

				<div class="form-input quantity-input-increment">
					<input id="quantity-input" class="number-spinner" name="quantity" type="number" value="1" min="1" step="1" />
				</div>
			</div>

			<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>	

			<div class="row-component">
				<div class="form-label">
					<label>Thời gian thuê:</label>
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
							<input id="duration" class="number-spinner" name="duration" type="number" value="<?php echo $time_min ?>" min="<?php echo $time_min ?>" <?php if ( $time_max ) echo 'max="' . $time_max .'"' ?> step="<?php echo $time_step ?>" />
						</div>

						<span class="time-delimiter">
							<span class="time-unit">
								<?php echo __( $time_unit, 'aharent'); ?>
							</span>, 
						</span>
					
				</div>
			</div>

			<div class="row-component">
				<div class="form-label">
					<label>Từ <?php echo __( 'day', 'aharent' ); ?>:</label>
					<div class="validate"><span>(*Vui lòng chọn ngày)</span></div>
				</div>

				<div class="form-input">

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
				
					<input type="text" class="date" id="date-from" name="_date_from" <?php if ($hold_date_str) echo 'date-hold-to="' . $hold_date_str . '"' ?>  placeholder="Ngày" autocomplete="off" />
					<!-- <input type="text" id="date-to" name="_date_to" placeholder="Đến ngày" autocomplete="off" />	 -->


					
				</div>
			</div>

		</div>

		<div class="form-row">
			<div class="price-wrapper d-flex">

			<?php
				$security_deposit = $product->get_meta( 'security_deposit' );

				$display_security_deposit = !empty( $security_deposit ) && is_numeric($security_deposit);

			?>

			<?php if ( $display_security_deposit ): ?>
			<div class="price-item deposit">
				
				<div class="price-item-title">
					<span><?php echo __( 'Security deposit', 'aharent' ); ?></span>
					<div class="loading-price">
						<div class="loading-icon">
							<img src="<?php echo get_template_directory_uri() ?>/assets/img/loading.gif" />
						</div>
					</div>
				</div>
				
				<div class="price-item-value">
					<?php echo wc_price( $security_deposit ) ?>
				</div>
			</div>
			<?php endif ?>

			<div class="price-item">
				
				<div class="price-item-title">
					<span><?php echo __('Rental fee', 'aharent'); ?></span>
					<div class="loading-price">
						<div class="loading-icon">
							<img src="<?php echo get_template_directory_uri() ?>/assets/img/loading.gif" />
						</div>
					</div>
				</div>

				<div class="price-item-value rental">

					-
					
				</div>
			</div>


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
						<input id="delivery_option_pick-up" type="radio" name="delivery_option" value="pick-up" <?php if ( !$delivery_terms) echo 'checked'; ?> />
						<label for="delivery_option_pick-up">Nhận hàng tại <?php echo $address ?>.</label>
					</div>
				<?php endif ?>
			</div>
		</div>

		<div class="form-row submit-button">

			<input type="hidden" name="rent-now" value="0" />

			<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="aha-button single_add_to_cart_button button alt add-to-cart-button">GIỎ HÀNG</button>
			<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="aha-button single_add_to_cart_button button alt rent-now-button">THUÊ NGAY</button>
		
		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
</div>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php //else: ?>

<!-- Out of stock -->
<!-- <div class="stock-status">
	<?php echo wc_get_stock_html( $product ); // WPCS: XSS ok. ?>
</div> -->


<?php //endif ?>