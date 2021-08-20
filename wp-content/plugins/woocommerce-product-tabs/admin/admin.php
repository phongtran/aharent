<?php
/**
 * Admin functions
 *
 * @package Woocommerce_Product_Tabs
 */

/**
 * Render admin page.
 *
 * @since 1.0.0
 */
function wpt_render_admin_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-2">

				<div id="post-body-content">

					<div class="tab-wrapper">
						<ul class="tabs-nav">
							<li class="tab-active"><a href="#tab-features" class="button"><?php esc_html_e( 'Features', 'woocommerce-product-tabs' ); ?></a></li>
							<li><a href="#tab-free-vs-pro" class="button"><?php esc_html_e( 'Free vs Pro', 'woocommerce-product-tabs' ); ?></a></li>
						</ul>
					</div><!-- .tab-wrapper -->

					<div class="tabs-stage">
						<div id="tab-features" class="meta-box-sortables ui-sortable active">
							<div class="postbox">
								<div class="inside inside-content">
									<h4>Key features</h4>
									<ul>
									<li>Create multiple tabs; no limit in number of tabs</li>
									<li>Supports shortcodes and embedded codes</li>
									<li>Setup common tab for all products</li>
									<li>Enable tab based on product category</li>
									<li>Change order of the tabs</li>
									</ul>
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .meta-box-sortables -->

						<div id="tab-free-vs-pro" class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<div class="inside inside-content">
									<img src="<?php echo plugin_dir_url( __DIR__ ) . 'admin/images/free-vs-pro.png'; ?>" alt="" />
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .meta-box-sortables -->

					</div><!-- .tabs-stage -->

				</div><!-- #post-body-content -->

				<div id="postbox-container-1" class="postbox-container">

					<div class="meta-box-sortables">
						<div class="postbox">

							<h3><span>Upgrade to Pro</span></h3>
							<div class="inside">
								<p>Buy pro plugin unlock more awesome features.</p>
								<a href="<?php echo esc_url( WPT_UPGRADE_URL ); ?>" id="purchase" class="button button-primary" target="_blank">Buy Pro Plugin</a>
							</div> <!-- .inside -->

						</div><!-- .postbox -->
					</div><!-- .meta-box-sortables -->

					<div class="meta-box-sortables">
						<div class="postbox">

							<h3><span>Our Products</span></h3>
							<div class="inside">
								<ul>
								<li><a href="https://wpconcern.com/plugins/post-grid-elementor-addon/" target="_blank">Post Grid Elementor Addon</a></li>
								<li><a href="https://wpconcern.com/plugins/woocommerce-product-tabs/" target="_blank">WooCommerce Product Tabs</a></li>
								<li><a href="https://wpconcern.com/plugins/advanced-google-recaptcha/" target="_blank">Advanced Google reCAPTCHA</a></li>
								</ul>
							</div> <!-- .inside -->

						</div><!-- .postbox -->
					</div><!-- .meta-box-sortables -->

					<div class="meta-box-sortables">
						<div class="postbox">

							<h3><span>Have any queries?</span></h3>
							<div class="inside">
								<p>If you have any queries or feedback, please feel free to send us an email to <code>support@wpconcern.com</code></p>
							</div><!-- .inside -->

						</div><!-- .postbox -->
					</div><!-- .meta-box-sortables -->

					<div class="meta-box-sortables">
						<div class="postbox">

							<h3><span>Important Links</span></h3>
							<div class="inside">
								<ul>
								<li><a href="https://wpconcern.com/documentation/woocommerce-product-tabs/" target="_blank">Documentation</a></li>
								<li><a href="https://wpconcern.com/request-customization/" target="_blank">Customization Request</a></li>
								<li><a href="https://wordpress.org/plugins/woocommerce-product-tabs/#reviews" target="_blank">Submit a Review</a></li>
								</ul>
							</div> <!-- .inside -->

						</div><!-- .postbox -->
					</div><!-- .meta-box-sortables -->

				</div><!-- #postbox-container-1 .postbox-container -->

			</div><!-- #post-body -->
		</div><!-- #poststuff -->

	</div><!-- .wrap -->
	<?php
}

/**
 * Register menu page.
 *
 * @since 1.0.0
 */
function wpt_register_menu() {
	add_submenu_page( 'edit.php?post_type=woo_product_tab', esc_html__( 'WooCommerce Product Tabs', 'woocommerce-product-tabs' ), esc_html__( 'Getting Started', 'woocommerce-product-tabs' ), 'manage_options', 'wpt-welcome', 'wpt_render_admin_page' );
}

add_action( 'admin_menu', 'wpt_register_menu' );

/**
 * Load admin assets.
 *
 * @since 1.0.0
 *
 * @param string $hook Hook name.
 */
function wpt_load_admin_scripts( $hook ) {
	if ( 'woo_product_tab_page_wpt-welcome' === $hook ) {
		wp_enqueue_style( 'wpt-admin-style', plugins_url( 'admin/css/admin.css', dirname( __FILE__ ) ), array(), '1.0.0' );
		wp_enqueue_script( 'wpt-admin-script', plugins_url( 'admin/js/admin.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0.0', true );
	}
}

add_action( 'admin_enqueue_scripts', 'wpt_load_admin_scripts' );
