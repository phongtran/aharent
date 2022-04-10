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
global $prices;
global $time_unit;
if ( !isset($prices) )
	$prices = get_product_prices( $product );

?>

<div class="form-row price-details">
	
	<div class="form-input ">

		<?php if ( $product->is_type( 'simple' )) : ?>
			
			<?php
				$time_unit = __('day', 'aharent');
				$time_block = $product->get_meta( 'time_unit' );
				if ( !empty( $time_block) )
					$time_unit = __( $time_block, 'woocommerce' );
			?>

			<table>
				<thead>
					<th><?php echo ucfirst(__('Time', 'aharent')) ?></th>
					<th><?php echo __( 'Rental fee', 'aharent' ) ?></th>
				</thead>
				<tbody>
					<tr>
						<td><?php echo '1 ' . __( $time_unit, 'woocommerce' ) ?></td>
						<td><?php echo wc_price( $product->price )  ?>/<?php echo __( $time_unit, 'woocommerce' ) ?></td>
					</tr>
				</tbody>
			</table>

		<?php else: ?>
			<table>
				<thead>
					<th><?php echo ucfirst(__( 'Time', 'aharent' )) ?></th>
					<th><?php echo ucfirst(__( 'Rental fee', 'aharent' )) ?></th>
				</thead>
				<tbody>
					<?php $deposit = get_product_deposit_percentage( $product ); $incre = 0; ?>
					<?php foreach ( $prices as $time_unit => $price ) :?>
						<?php $level = 1; ?>
						<?php foreach ( $price as $duration => $value ) : ?>
							<tr>
								<td>
									<?php 
										if ( is_numeric( $duration ) )
										{
											if ( $value['block_price'] )
											{
												echo $duration . ' ' . __( $time_unit, 'aharent' );
											}
											else
											{
												if ( $level < $duration )
													echo $level . ' - ' . $duration . ' ' . __( $time_unit, 'aharent' );
												else
													echo $duration . ' ' . __( $time_unit, 'aharent' );
											}

											$level = $duration + 1;
										}
										else
										{
											if ( in_array( $duration, array( 'more', 'extra' )) )
												echo 'Nhiều hơn';//ucfirst(__( $value['more_label'], 'woocommerce' ));
											elseif ( 'single' == $duration ) { 
												echo ucfirst(__( $time_unit, 'aharent' ));
											}
											else
												echo ucfirst( __( $duration, 'aharent') );
										}
											
									?>
								</td>
								<td>
									<?php echo wc_price( $value['price'] )  ?>
									<?php
										if ( isset( $value['block_price']) && !$value['block_price'] )
											echo '/' . __( $time_unit, 'aharent' )
									?>
								</td>
								
								<?php //if ( $incre == 0 ) : $incre++; ?>
									<!-- <td rowspan="0"><?php //echo $deposit ?>%</td> -->
								<?php //endif ?>
							</tr>
						<?php endforeach ?>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>
		
	</div>
</div>

<?php //if ( $product->is_in_stock() ) : ?>
<div class="price-wrapper d-flex">

	<?php
		$security_deposit = $product->get_meta( 'rental_terms' );

		$display_security_deposit = !empty( $security_deposit ) && is_numeric($security_deposit);
	
	?>


	<div class="note">
		<p>Chọn thông tin bên dưới để tính giá thuê.</p>
		<p>Giá thuê có thể thay đổi và phụ thuộc vào tình trạng xe sẵn có tại thời điểm đặt xe.</p>
		<?php if ( !$display_security_deposit ): ?>
			<p>Số tiền cọc sẽ được thông báo đến Khách hàng khi xác nhận đơn hàng.</p>
		<?php endif ?>
	</div>
	

</div>
<?php //endif ?>
