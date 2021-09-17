<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="aha-cart container">
	<div class="cart-steps">

		<div class="cart-step cart-items active">
			<div class="cart-step-title">
				<h3>SẢN PHẨM ĐẶT THUÊ</h3>
			</div>

			<div class="cart-step-content">

			<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					$index = 0;
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>

							<?php if ( $index > 0): ?>

								<div class="item-break"></div>
							<?php endif ?>
							<div class="cart-item d-flex">
								<div class="cart-item-thumbnail col-sm-3">
									<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

									if ( ! $product_permalink ) {
										echo $thumbnail; // PHPCS: XSS ok.
									} else {
										printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
									}
									?>

									<!-- <div class="security-deposit-policy">
										<span><a tabindex="0" role="button" class="popover-dismiss" data-bs-toggle="popover" data-bs-trigger="focus" title="Chính sách thế chân"
  												data-bs-content="<?php
												  $vendor_rental_terms = get_user_meta( $_product->post->post_author, 'vendor_rental_terms', true );

													if ( !empty( $vendor_rental_terms) )
														echo $vendor_rental_terms; ?>">Chính sách thế chân</a></span>
									</div> -->
								</div>

								<div class="cart-item-description">
									<div class="cart-item-title d-flex">
										<div class="title col-sm-9">
										<?php
											if ( ! $product_permalink ) {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
											} else {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s"><h4>%s</h4></a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
											}

											do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

											// Meta data.
											// echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

											// Backorder notification.
											if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
											}
										?>
										</div>

										<div class="button-remove d-flex justify-content-end w-100">

									<?php
										echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s"><span class="delete-icon"></span></a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												esc_attr( $product_id ),
												esc_attr( $_product->get_sku() ),
												$cart_item_key
				
											),
											$cart_item_key
										);
									?>
									</div>
									</div>

									<div class="info-section d-flex">
										<table class="item-data">
											<tbody>
												<tr class="quantity">
													<td class="label">Số lượng:</td>
													<td>
													<?php
														if ( $_product->is_sold_individually() ) {
															$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
														} else {
															$product_quantity = woocommerce_quantity_input(
																array(
																	'input_id'			=> 'quantity-input',
																	'input_name'   => "cart[{$cart_item_key}][qty]",
																	'input_value'  => $cart_item['quantity'],
																	// 'max_value'    => $_product->get_max_purchase_quantity(),
																	'min_value'    => '0',
																	'product_name' => $_product->get_name(),
																),
																$_product,
																false
															);
														}

														echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
														?>
													</td>
												</tr>
												<tr class="time-period">
													<td class="label">Thời gian thuê:</td>
													<td>
														<div class="date-picker-input">
															<div><input type="text" id="date-from" name="_date_from" value="<?php echo $cart_item['date-from'] ?>" placeholder="Từ ngày"></div>
															<div class="delimeter"><span>đến</span></div>
															<div><input type="text" id="date-to" name="_date_to" value="<?php echo $cart_item['date-to'] ?>" placeholder="Đến ngày"></div>
														</div>

														</div>
														
														
													</td>
												</tr>
												

											</tbody>
										</table>
									</div>


									<div class="price-section">
										<div class="rental-price">
											<span class="label">Giá thuê</span>
											<h3 class="price"><?php echo wc_price( $cart_item['rental_price'] * $cart_item['quantity'] * $cart_item['number_of_days'] ); ?></h3>
										</div>

										<div class="deposit-price">
											<span class="label">Đặt cọc</span>
											<h2 class="price"><?php echo wc_price( $cart_item['deposit'] * $cart_item['quantity'] * $cart_item['number_of_days']); ?></h2>
										</div>
										
										
										<!-- <table>
											<tr>
												<td>Giá thuê:</td>
												<td>
													<?php echo wc_price( $cart_item['rental_price'] * $cart_item['quantity'] * $cart_item['number_of_days'] ); ?>
												</td>
											</tr>
											<tr>
												<td>Đặt cọc:</td>
												<td>
													<?php echo wc_price( $cart_item['deposit'] * $cart_item['quantity'] * $cart_item['number_of_days']); ?>
												</td>
											</tr>
										</table> -->
									</div>


									



								</div>

							</div>

						<?php $index++; } ?>
					<?php } ?>

					<div class="control-section">
										
										
					<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Cập nhật', 'woocommerce' ); ?>"><?php esc_html_e( 'Cập nhật', 'woocommerce' ); ?></button>					
										
					<?php do_action( 'woocommerce_cart_actions' ); ?>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
						
				</div>
					
					

			
			

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>
				

			</div>

			
			
		</div>
		
		
		
		


	</div>

	<div class="cart-summary">

		<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

		<div class="cart-collaterals">
			<?php
				/**
				 * Cart collaterals hook.
				 *
				 * @hooked woocommerce_cross_sell_display
				 * @hooked woocommerce_cart_totals - 10
				 */
				do_action( 'woocommerce_cart_collaterals' );
			?>
		</div>

	</div>



</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
