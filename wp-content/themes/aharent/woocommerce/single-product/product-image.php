<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);

$attachment_ids = $product->get_gallery_image_ids();

?>
<!-- <div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		if ( $post_thumbnail_id ) {
			$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</figure>
</div> -->

<!-- Primary carousel image -->

<div class="product-image-gallery">
	<!-- Slider main container -->
	<div class="swiper">
		<!-- Additional required wrapper -->
		<div class="swiper-wrapper">
			<!-- Slides -->
			<div class="swiper-slide"><img src="<?php echo wp_get_attachment_url( $post_thumbnail_id ); ?>" class="" alt=""></div>
			
			<?php if ( $attachment_ids && $product->get_image_id() ) : ?>
				<?php foreach ( $attachment_ids as $attachment_id ) : ?>
			
					<div class="swiper-slide">
						<img src="<?php echo wp_get_attachment_url( $attachment_id ); ?>" class="" alt="" />
					</div>

				<?php endforeach ?>
			<?php endif ?>
		</div>
		

		<!-- If we need navigation buttons -->
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>

		<!-- If we need pagination -->
		<div class="swiper-pagination"></div>	

	
	</div>
</div>


<!-- <div class="product-image-gallery">

	<div class="show" href="<?php //echo wp_get_attachment_url( $post_thumbnail_id ); ?>">
		<img src="<?php // echo wp_get_attachment_url( $post_thumbnail_id ); ?>" id="show-img">
	</div> -->

	<!-- Secondary carousel image thumbnail gallery -->

	<!-- <div class="scroll-wrapper">
		<div class="small-img">

			<img src="<?php // echo get_template_directory_uri() . '/assets/vendors/jquery-zoom-image-carousel/images/next-icon.png' ?>" class="icon-left" alt="" id="prev-img" />
			
			<div class="small-container">
				<div id="small-img-roll">
					
					<img src="<?php // echo wp_get_attachment_url( $post_thumbnail_id ); ?>" class="show-small-img" alt="">

					<?php 
						// if ( $attachment_ids && $product->get_image_id() ) :
						// 	foreach ( $attachment_ids as $attachment_id ) : 
					?>
					
					<img src="<?php // echo wp_get_attachment_url( $attachment_id ); ?>" class="show-small-img" alt="" />

					<?php // endforeach ?>
					<?php // endif ?>
				
				</div>
			</div>
			
			<img src="<?php // echo get_template_directory_uri() . '/assets/vendors/jquery-zoom-image-carousel/images/next-icon.png' ?>" class="icon-right" alt="" id="next-img">

		</div>
	</div>
</div> -->