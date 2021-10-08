<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// require_once THEME_DIR . 'inc/product-tabs/pricing-policy-tab.php';
require_once THEME_DIR . 'inc/product-tabs/add-tabs.php';


/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */

$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );


if ( ! empty( $product_tabs ) ) : ?>

<div class="product-description container">
	<div class="woocommerce-tabs wc-tabs-wrapper">
		
		<ul class="nav nav-tabs mb-3 tabs wc-tabs" role="tablist">
			
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>		
				<li class="nav-item <?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="presentation" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
					<a href="#tab-<?php echo esc_attr( $key ); ?>">
						<?php if ( $product_tab['title'] == 'Description' ) $product_tab['title'] = __( 'Thông tin sản phẩm', 'woocommerce'); ?>
						<h3 class="text-uppercase"><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
						</h3>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		
		<div class="tab-content">
			<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
				<div class="tab-pane woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
					<?php
					if ( isset( $product_tab['callback'] ) ) {
						call_user_func( $product_tab['callback'], $key, $product_tab );
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php do_action( 'woocommerce_product_after_tabs' ); ?>
	</div>
</div>

<?php endif; ?>
