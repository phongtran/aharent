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

<div class="aha-cart container d-flex">
	<div class="cart-steps col-sm-8">

		<div class="cart-step cart-items active">
			<div class="cart-step-title">
				<h3>CART ITEMS</h3>
			</div>

			<div class="cart-step-content">

			<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>

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
								</div>

								<div class="cart-item-description">
									<div class="cart-item-title">
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
																	'max_value'    => $_product->get_max_purchase_quantity(),
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
														<input type="text" id="date-from" name="_date_from" value="<?php echo $cart_item['date-from'] ?>" placeholder="Từ ngày">
														<input type="text" id="date-to" name="_date_to" value="<?php echo $cart_item['date-to'] ?>" placeholder="Đến ngày">
													</td>
												</tr>
												

											</tbody>
										</table>
									</div>


									<div class="price-section">
										<table>
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
										</table>
									</div>


									<div class="control-section">
										<button type="submit" class="button" name="update" value="<?php esc_attr_e( 'Update', 'woocommerce' ); ?>"><?php esc_html_e( 'Update', 'woocommerce' ); ?></button>
										<button type="submit" class="button" name="remove" value="<?php esc_attr_e( 'Remove', 'woocommerce' ); ?>"><?php esc_html_e( 'Remove', 'woocommerce' ); ?></button>
									</div>



								</div>

							</div>

						<?php } ?>
					<?php } ?>

			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
				<!-- <thead>
					<tr>
						<th class="product-remove">&nbsp;</th>
						<th class="product-thumbnail">&nbsp;</th>
						<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
						<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
						<th class="product-rental-period"><?php esc_html_e( 'Rental period', 'woocommerce' ); ?></th>
						<th class="product-price"><?php esc_html_e( 'Rental fee', 'woocommerce' ); echo '<br />(pay at receiving item)' ?></th>
						<th class="product-security-deposit"><?php esc_html_e( 'Security deposit', 'woocommerce' ); echo '<br />(pay at receiving item,' . '<br />get back when returning item)'; ?></th> -->
						<!-- <th class="product-deposit"><?php esc_html_e( 'Pay at delivery/pick-up', 'woocommerce' ); ?></th> -->
						<!-- <th class="product-deposit"><?php esc_html_e( 'Booking fee', 'woocommerce' ); echo '<br />(pay at check-out)' ?></th> -->
						<!-- <th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th> -->
					<!-- </tr> -->
				<!-- </thead> -->
				<tbody>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>

							
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

								<td class="product-remove">
									<?php
										echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												esc_html__( 'Remove this item', 'woocommerce' ),
												esc_attr( $product_id ),
												esc_attr( $_product->get_sku() )
											),
											$cart_item_key
										);
									?>
								</td>

								<td class="product-thumbnail">
								<?php
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

								if ( ! $product_permalink ) {
									echo $thumbnail; // PHPCS: XSS ok.
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
								}
								?>
								</td>

								<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
								<?php
								if ( ! $product_permalink ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
								} else {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
								}

								do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

								// Meta data.
								echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

								// Backorder notification.
								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
								}
								?>
								</td>

								

								<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
								<?php
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $_product->get_max_purchase_quantity(),
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


								<td class="product-rental-period" data-title="<?php esc_attr_e( 'Rental period', 'woocommerce' ); ?>">
								<?php
									echo $cart_item['date-from'] . '<br />- ' . $cart_item['date-to'];
								?>
								</td>


								<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
									<?php
										echo wc_price( $cart_item['rental_price'] * $cart_item['quantity'] * $cart_item['number_of_days'] );
									?>
								</td>

								<td class="product-security-deposit" data-title="<?php esc_attr_e( 'Security deposit', 'woocommerce' ); ?>">
									<?php
										echo wc_price( $cart_item['security_deposit'] * $cart_item['quantity'] * $cart_item['number_of_days'] );
									?>
								</td>

								<td class="product-deposit" data-title="<?php esc_attr_e( 'Deposit', 'woocommerce' ); ?>">
									<?php
										echo wc_price( $cart_item['deposit'] * $cart_item['quantity'] * $cart_item['number_of_days']);
									?>
								</td>

								

								<!-- <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
									<?php
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								</td> -->
							</tr>
							<?php
						}
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>

					<tr>
						<td colspan="8" class="actions">

							<?php if ( wc_coupons_enabled() ) { ?>
								<div class="coupon">
									<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
									<?php do_action( 'woocommerce_cart_coupon' ); ?>
								</div>
							<?php } ?>

							<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

							<?php do_action( 'woocommerce_cart_actions' ); ?>

							<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
						</td>
					</tr>

					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</tbody>
			</table>
			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>
				

			</div>

			
			
		</div>

		<div class="cart-step cart-checkout">
			<div class="cart-step-title">
				<h3>CHECK-OUT INFORMATION</h3>
			</div>
			
			<div class="cart-step-content">

			</div>
		</div>
		

		
		
		
		


	</div>

	<div class="cart-summary col-sm-3">

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
