<?php
/**
 * Premium page of YITH WooCommerce Ajax Search Premium
 *
 * @package YITH WooCommerce Ajax Search
 * @since   1.0.0
 * @author  YITH
 */

?>
<style>
	.landing {
		margin-right: 15px;
		border: 1px solid #d8d8d8;
		border-top: 0;
	}

	.section {
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
		background: #fafafa;
	}

	.section h1 {
		text-align: center;
		text-transform: uppercase;
		color: #445674;
		font-size: 35px;
		font-weight: 700;
		line-height: normal;
		display: inline-block;
		width: 100%;
		margin: 50px 0 0;
	}

	.section .section-title h2 {
		vertical-align: middle;
		padding: 0;
		line-height: normal;
		font-size: 24px;
		font-weight: 600;
		color: #445674;
		text-transform: none;
		background: none;
		border: none;
		text-align: center;
	}

	.section p {
		margin: 15px 0;
		font-size: 19px;
		line-height: 32px;
		font-weight: 300;
		text-align: center;
	}

	.section ul li {
		margin-bottom: 4px;
	}

	.section.section-cta {
		background: #fff;
	}

	.cta-container,
	.landing-container {
		display: flex;
		max-width: 1200px;
		margin-left: auto;
		margin-right: auto;
		padding: 30px 0;
		align-items: center;
	}

	.landing-container-wide {
		flex-direction: column;
	}

	.cta-container {
		display: block;
		max-width: 860px;
	}

	.landing-container:after {
		display: block;
		clear: both;
		content: '';
	}

	.landing-container .col-1,
	.landing-container .col-2 {
		float: left;
		box-sizing: border-box;
		padding: 0 15px;
	}

	.landing-container .col-1 {
		width: 58.33333333%;
	}

	.landing-container .col-2 {
		width: 41.66666667%;
	}

	.landing-container .col-1 img,
	.landing-container .col-2 img,
	.landing-container .col-wide img {
		max-width: 100%;
	}

	.premium-cta {
		color: #4b4b4b;
		border-radius: 10px;
		padding: 30px 25px;
		display: flex;
		align-items: center;
		justify-content: space-between;
		width: 100%;
		box-sizing: border-box;
	}

	.premium-cta:after {
		content: '';
		display: block;
		clear: both;
	}

	.premium-cta p {
		margin: 10px 0;
		line-height: 1.5em;
		display: inline-block;
		text-align: left;
	}

	.premium-cta a.button {
		border-radius: 25px;
		float: right;
		background: #e09004;
		box-shadow: none;
		outline: none;
		color: #fff;
		position: relative;
		padding: 10px 50px 8px;
		text-align: center;
		text-transform: uppercase;
		font-weight: 600;
		font-size: 20px;
		line-height: 25px;
		border: none;
	}

	.premium-cta a.button:hover,
	.premium-cta a.button:active,
	.wp-core-ui .yith-plugin-ui .premium-cta a.button:focus {
		color: #fff;
		background: #d28704;
		box-shadow: none;
		outline: none;
	}

	.premium-cta .highlight {
		text-transform: uppercase;
		background: none;
		font-weight: 500;
	}


	@media (max-width: 768px) {
		.landing-container{
			display: block;
			padding: 50px 0 30px;
		}

		.landing-container .col-1,
		.landing-container .col-2{
			float: none;
			width: 100%;
		}

		.premium-cta{
			display: block;
			text-align: center;
		}

		.premium-cta p{
			text-align: center;
			display: block;
			margin-bottom: 30px;
		}
		.premium-cta a.button{
			float: none;
			display: inline-block;
		}
	}

	@media (max-width: 480px) {
		.wrap {
			margin-right: 0;
		}

		.section {
			margin: 0;
		}

		.landing-container .col-1,
		.landing-container .col-2 {
			width: 100%;
			padding: 0 15px;
		}

		.section-odd .col-1 {
			float: left;
			margin-right: -100%;
		}

		.section-odd .col-2 {
			float: right;
			margin-top: 65%;
		}
	}

	@media (max-width: 320px) {
		.premium-cta a.button {
			padding: 9px 20px 9px 70px;
		}

		.section .section-title img {
			display: none;
		}
	}
</style>
<div class="landing">
	<div class="section section-cta section-odd">
		<div class="landing-container">
			<div class="premium-cta">
				<p>
					<?php
					// translators: placeholders html tag.
					echo wp_kses_post( sprintf( __( 'Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Ajax Search%2$s to benefit from all features!', 'yith-woocommerce-ajax-search' ), '<span class="highlight">', '</span>' ) );
					?>
				</p>
				<a href="<?php echo esc_url( $this->get_premium_landing_uri() ); ?>" target="_blank"
					class="premium-cta-button button btn">
					<?php esc_html_e( 'UPGRADE', 'yith-woocommerce-ajax-search' ); ?>
				</a>
			</div>
		</div>
	</div>
	<div class="one section section-even clear">
		<h1><?php esc_html_e( 'Premium Features', 'yith-woocommerce-ajax-search' ); ?></h1>
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo esc_url( YITH_WCAS_ASSETS_URL ); ?>/images/013.webp" alt="Feature 01"/>
			</div>
			<div class="col-2">
				<div class="section-title">
					<h2>
						<?php
						esc_html_e( 'Show a preview of the product', 'yith-woocommerce-ajax-search' );
						?>
					</h2>
				</div>
				<p>
					<?php
					esc_html_e( 'For better usability, you can show an image of the product and choose its position in the preview of search results', 'yith-woocommerce-ajax-search' );
					?>
				</p>
			</div>
		</div>
	</div>
	<div class="two section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<h2><?php esc_html_e( 'Show the price of the product', 'yith-woocommerce-ajax-search' ); ?></h2>
				</div>
				<p>
					<?php
					esc_html_e( 'Show also the price in the search results and highlight discounts and promotions', 'yith-woocommerce-ajax-search' );
					?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo esc_url( YITH_WCAS_ASSETS_URL ); ?>/images/022.webp" alt="feature 02"/>
			</div>
		</div>
	</div>
	<div class="three section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo esc_url( YITH_WCAS_ASSETS_URL ); ?>/images/033.webp" alt="Feature 03"/>
			</div>
			<div class="col-2">
				<div class="section-title">
					<h2>
						<?php
						esc_html_e( 'Show a brief description of the product', 'yith-woocommerce-ajax-search' );
						?>
					</h2>
				</div>
				<p>
					<?php
					esc_html_e( 'Give helpful information about the product immediately', 'yith-woocommerce-ajax-search' );
					?>
				</p>
			</div>
		</div>
	</div>
	<div class="four section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<h2><?php esc_html_e( 'Highlight promotional products', 'yith-woocommerce-ajax-search' ); ?></h2>
				</div>
				<p>
					<?php
					esc_html_e( 'Add a customizable label to emphasize discounted and promotion products', 'yith-woocommerce-ajax-search' );
					?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo esc_url( YITH_WCAS_ASSETS_URL ); ?>/images/043.webp" alt="Feature 04"/>
			</div>
		</div>
	</div>
	<div class="five section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo esc_url( YITH_WCAS_ASSETS_URL ); ?>/images/052.webp" alt="Feature 05"/>
			</div>
			<div class="col-2">
				<div class="section-title">
					<h2>
						<?php
						esc_html_e( 'Extend the product search through tags, SKUs and categories', 'yith-woocommerce-ajax-search' );
						?>
					</h2>
				</div>
				<p>
					<?php
					esc_html_e( 'To make your products easier to find and to improve the quality of the user experience in your shop', 'yith-woocommerce-ajax-search' );
					?>
				</p>
			</div>
		</div>
	</div>

	<div class="six section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<h2><?php esc_html_e( 'Show the search form in two different layouts', 'yith-woocommerce-ajax-search' ); ?></h2>
				</div>
				<p>
					<?php
					esc_html_e( 'A wide style in addition to the default one. The form will adjust to the whole width of the page', 'yith-woocommerce-ajax-search' );
					?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo esc_url( YITH_WCAS_ASSETS_URL ); ?>/images/09.webp" alt="Feature 06"/>
			</div>
		</div>
	</div>

	<div class="eight section section-odd clear">
		<div class="landing-container">

			<div class="col-1">
				<img src="<?php echo esc_url( YITH_WCAS_ASSETS_URL ); ?>/images/08.webp" alt="Feature 08"/>
			</div>
			<div class="col-2">
				<div class="section-title">
					<h2><?php esc_html_e( 'Search products by vendor', 'yith-woocommerce-ajax-search' ); ?></h2>
				</div>
				<p>
					<?php
					esc_html_e( 'By using the plugin in combination with YITH WooCommerce Multi Vendor', 'yith-woocommerce-ajax-search' );
					?>
				</p>
			</div>
		</div>
	</div>

	<div class="section section-cta section-even">
		<div class="landing-container">
			<div class="premium-cta">
				<p>
					<?php
					// translators: placeholders html tag.
					echo sprintf( esc_html( __( 'Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Ajax Search%2$s to benefit from all features!', 'yith-woocommerce-ajax-search' ) ), '<span class="highlight">', '</span>' );
					?>
				</p>
				<a href="<?php echo esc_url( $this->get_premium_landing_uri() ); ?>" target="_blank"
					class="premium-cta-button button btn">
					<?php esc_html_e( 'UPGRADE', 'yith-woocommerce-ajax-search' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>
