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

            <?php if ( $notices ) : ?>

            <!-- <div class="woocommerce-info"<?php echo wc_get_notice_data_attr( $notice ); ?>>
				<?php echo wc_kses_notice( $notice['notice'] ); ?>
			</div> -->
            <?php endif ?>

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
                    <div class="cart-item">
                        <div class="cart-item-thumbnail">
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
                            <div class="cart-item-title">
                                <div class="title">
                                    <?php
											if ( ! $product_permalink ) {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;' );
											} else {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s"><h4>%s</h4></a>', esc_url( $product_permalink ), $_product->get_title() ), $cart_item, $cart_item_key ) );
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


                            </div>

                            <div class="info-section d-flex">
                                <table class="item-data">
                                    <tbody>

                                        <?php
													$product = wc_get_product( $_product->id );
													$time_units = $product->get_attribute( 'time_unit' );
												?>
                                        <?php if ( $time_units ) : $time_units = explode( ' | ', $time_units ); ?>

                                        <tr class="time-unit">
                                            <td class="label">Thuê theo:</td>
                                            <td>
                                                <div class="time-unit-row">
                                                    <select data-key="<?php echo $cart_item_key ?>"
                                                        class="form-select time-unit" id="time_unit"
                                                        name="cart[<?php echo $cart_item_key ?>][time_unit]">
                                                        <?php foreach ( $time_units as $time_unit ) : ?>
                                                        <option
                                                            <?php echo ($time_unit == $cart_item['time-unit']) ? 'selected' : '' ?>
                                                            value="<?php echo $time_unit ?>">
                                                            <?php echo ucfirst(__( $time_unit, 'woocommerce' )) ?>
                                                        </option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>

                                        <?php endif ?>

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
																	'min_value'    => '1',
																	'product_name' => $_product->get_name(),
																	'classes'		=> 'number-spinner'
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
                                                <div class="time-period-wrapper">
                                                    <div class="duration">

                                                        <?php
															$time_min = $product->get_meta( 'time_min' );
															if ( !$time_min) $time_min = 1;

															$time_max = $product->get_meta( 'time_max' );
															
															$time_step = $product->get_meta( 'time_step' );
															if ( !$time_step ) $time_step = 1;
														?>

                                                        <input id="duration" class="number-spinner"
                                                            name="cart[<?php echo $cart_item_key ?>][duration]"
                                                            type="number" value="<?php echo $cart_item['duration'] ?>"
                                                            min="<?php echo $time_min?>"
                                                            <?php if ($time_max) echo 'max="' . $time_max . '"' ?>
                                                            step="<?php echo $time_step ?>" />
                                                    </div>

                                                    <span class="time-unit-wrapper">
                                                        <span class="time-unit" data-key="<?php echo $cart_item_key ?>">
                                                            <?php
																if (isset($cart_item['time-unit']))
																	$time_unit = __( $cart_item['time-unit'], 'woocommerce' );
																else
																{
																	$time_unit = __( 'day', 'woocommerce' );
																	$time_block = $_product->get_meta( '_time_block' );
																	if ( !empty( $time_block) )
																		$time_unit = __( $time_block, 'woocommerce' );
																}

																echo $time_unit;
															?>
                                                        </span>, từ <?php echo __( 'day', 'woocommerce' ); ?>
                                                    </span>

                                                    <div class="date-picker-input">
                                                        <div><input type="text" id="date-from"
                                                                name="cart[<?php echo $cart_item_key ?>][_date_from]"
                                                                value="<?php echo $cart_item['date-from'] ?>"
                                                                placeholder="Từ ngày"></div>
                                                        <!-- <div class="delimeter"><span>đến</span></div>
																<div><input type="text" id="date-to" name="_date_to" value="<?php echo $cart_item['date-to'] ?>" placeholder="Đến ngày"></div> -->
                                                    </div>


                                                </div>

                                            </td>
                                        </tr>

                                        <tr class="delivery-option">
                                            <td class="label">Giao/nhận:</td>
                                            <td>
                                                <?php
                                                        $vendor_login = $product->get_meta( 'vendor' );
                                                        $vendor_profiles = get_vendor_profiles( $vendor_login );
                                                        $address = $vendor_profiles['address']['street_2'] . ', ' . $vendor_profiles['address']['city'];

                                                        
                                                        $vendor = get_product_vendor ( wc_get_product( $product_id)->post );
                                                        $delivery_terms = get_user_meta( $vendor, 'delivery_terms', true );
                                                    ?>

                                                <div class="radio-options">
                                                    <?php if ( $delivery_terms ): ?>
                                                    <div class='radio-option-row'>
                                                        <input id="delivery_option_delivery" type="radio"
                                                            name="cart[<?php echo $cart_item_key ?>][delivery_option]"
                                                            value="delivery"
                                                            <?php echo ('delivery' == $cart_item['delivery-option']) ? 'checked' : ''  ?> />
                                                        <label for="delivery_option_delivery">Giao hàng tận nơi.</label>
                                                    </div>
                                                    <?php endif ?>

                                                    <?php if ( !empty( $address )): ?>
                                                    <div class='radio-option-row'>
                                                        <input id="delivery_option_pick-up" type="radio"
                                                            name="cart[<?php echo $cart_item_key ?>][delivery_option]"
                                                            value="pick-up"
                                                            <?php echo ('pick-up' == $cart_item['delivery-option']) ? 'checked' : ''  ?> />
                                                        <label for="delivery_option_pick-up">Nhận hàng tại
                                                            <?php echo $address ?>.</label>
                                                    </div>
                                                    <?php endif ?>
                                                </div>

                                            </td>
                                        </tr>


                                    </tbody>
                                </table>
                            </div>


                            <div class="price-section">


                                <div class="deposit-price">
                                    <span class="label"><?php echo __( 'Deposit', 'woocommerce' ) ?></span>
                                    <h2 class="price">
                                        <?php
											$amount = $cart_item['deposit'] * $cart_item['quantity'];

											$discount = $cart_item['discount'];
											if ( $discount )
											{
												if ( 'percentage' == $discount['type'] )
													echo '<div class="original-price"><s>' .  wc_price( $amount ) . '</s></div>' . wc_price( $amount * (100 - $discount['value']) / 100 );
											}
											else
												echo wc_price( $amount );
											
										?>
                                    </h2>
                                </div>

                                <div class="rental-price">
                                    <span class="label">Trả sau</span>
                                    <h3 class="price">
                                        <?php
											$amount = ( $cart_item['rental_price'] - $cart_item['deposit']) * $cart_item['quantity'];

											if ( $discount )
											{
												if ( 'percentage' == $discount['type'] )
													echo '<div class="original-price"><s>' .  wc_price( $amount ) . '</s></div>' . wc_price( $amount * (100 - $discount['value']) / 100 );
											}
											else
												echo wc_price( $amount );
										?>
                                    </h3>
                                </div>


                                <!-- <table>
											<tr>
												<td>Giá thuê:</td>
												<td>
													<?php echo wc_price( $cart_item['rental_price'] * $cart_item['quantity'] * $cart_item['duration'] ); ?>
												</td>
											</tr>
											<tr>
												<td>Đặt cọc:</td>
												<td>
													<?php echo wc_price( $cart_item['deposit'] * $cart_item['quantity'] * $cart_item['duration']); ?>
												</td>
											</tr>
										</table> -->
                            </div>






                        </div>

                        <div class="button-remove">

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

                    <?php $index++; } ?>
                    <?php } ?>

                    <div class="control-section">


                        <button type="submit" class="button" name="update_cart"
                            value="<?php esc_attr_e( 'Cập nhật', 'woocommerce' ); ?>"><?php esc_html_e( 'Cập nhật', 'woocommerce' ); ?></button>

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


<div class="popular-products">
    <div class="title-bar">
        <h3>SẢN PHẨM NỔI BẬT</h3>
    </div>


    <?php

            woocommerce_product_loop_start();

                $args = get_featured_products_query();

                $loop = new WP_Query( $args );

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