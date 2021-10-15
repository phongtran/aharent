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
				$time_unit = __( 'day', 'woocommerce' );
				$time_block = $product->get_meta( 'time_unit' );
				if ( !empty( $time_block) )
					$time_unit = __( $time_block, 'woocommerce' );
			?>

			<table>
				<thead>
					<th><?php echo ucfirst(__( 'Time', 'woocommerce' )) ?></th>
					<th><?php echo __( 'Giá thuê', 'woocommerce' ) ?></th>
					<!-- <th><?php echo __( 'Deposit', 'woocommerce' ) ?></th> -->
				</thead>
				<tbody>
					<tr>
						<td><?php echo '1 ' . __( $time_unit, 'woocommerce' ) ?></td>
						<td><?php echo wc_price( $product->price )  ?>/<?php echo __( $time_unit, 'woocommerce' ) ?></td>
						<!-- <td><?php echo get_product_deposit_percentage( $product ) ?>%</td> -->
					</tr>
				</tbody>
			</table>

		<?php else: ?>
			<table>
				<thead>
					<th><?php echo ucfirst(__( 'Time', 'woocommerce' )) ?></th>
					<th><?php echo __( 'Price', 'woocommerce' ) ?></th>
					<!-- <th rowspan="<?php echo 1 + count($prices, COUNT_RECURSIVE) ?>"><?php echo __( 'Deposit', 'woocommerce' ) ?></th> -->
				</thead>
				<tbody>
					<?php $deposit = get_product_deposit_percentage( $product ); $incre = 0; ?>
					<?php foreach ( $prices as $time_unit => $price ) :?>
						<?php $level = 1;?>
						<?php foreach ( $price as $duration => $value ) : ?>
							<tr>
								<td>
									<?php 
										if ( is_numeric( $duration ) )
										{
											if ( $level < $duration )
												echo $level . ' - ' . $duration . ' ' . __( $time_unit, 'woocommerce' );
											else
												echo $duration . ' ' . __( $time_unit, 'woocommerce' );
										}
										else
										{
											if ( in_array( $duration, array( 'more', 'extra' )) && !empty($value['more_label']))
												echo ucfirst(__( $value['more_label'], 'woocommerce' ));
											elseif ( 'single' == $duration )	
												echo ucfirst(__( $time_unit, 'woocommerce' ));
											else
												echo ucfirst( __( $duration, 'woocommerce') );
										}
											
										
										$level = $duration + 1;
									?>
								</td>
								<td>
									<?php echo wc_price( $value['price'] )  ?>
									<?php
										if ( isset( $value['block_price']) && !$value['block_price'] )
											echo '/' . __( $time_unit, 'woocommerce' )
									?>
								</td>
								
								<?php //if ( $incre == 0 ) : $incre++; ?>
									<!-- <td rowspan="0"><?php echo $deposit ?>%</td> -->
								<?php //endif ?>
							</tr>
						<?php endforeach ?>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>
		
	</div>
</div>

<?php if ( $product->is_in_stock() ) : ?>
<div class="price-wrapper d-flex">

	<div class="note">
		<span>Chọn thông tin bên dưới để tính giá thuê.</span>
	</div>

	<div class="price-item deposit">
		
		<div class="price-item-title">
			<span>Phí trả trước</span>
			<div class="loading-price">
				<div class="loading-icon">
					<img src="<?php echo get_template_directory_uri() ?>/assets/img/loading.gif" />
				</div>
			</div>
		</div>
		
		<div class="price-item-value">
			-
		</div>
	</div>

	<div class="price-item">
		
		<div class="price-item-title">
			<span>Thanh toán khi nhận hàng</span>
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


	<?php
		$_security_deposit = $product->get_meta( '_security_deposit_amount' );
		
		if ( !empty($_security_deposit) && ($_security_deposit > 0) ) :
	?>

	<div class="price-item">
		
		<div class="price-item-title">
			<span>Thế chân</span>
		</div>
		
		<div class="price-item-value">
			<?php echo wc_price( $product->get_meta( '_security_deposit_amount' )); ?>
		</div>
	</div>

	<?php endif ?>


	

</div>
<?php endif ?>
