<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce\Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $product;
?>
<div class="product-title-wrapper">
	<?php the_title( '<h1 class="product_title entry-title">', '</h1>' ); ?>
	<div class="vendor-info">
			<?php
				$vendor = get_vendor_profiles ($product->get_meta( 'vendor' ));
				echo $vendor['store_name'] . ' (' . $product->sku . ')';
			?>
		
	</div>
	<?php
		
		$discount = get_discount( $product );
		if ( $discount ) :
	?>
	<div class="discount-tag">
		<span>-<?php echo $discount['value']; echo ('percentage' == $discount['type'])? '%':'đ' ?></span>
	</div>

	<?php endif ?>
</div>
