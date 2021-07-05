<?php
/**
 * The template part for header
 *
 * @package VW Ecommerce Store 
 * @subpackage vw_ecommerce_store
 * @since VW Ecommerce Store 1.0
 */
?>

<?php if( get_theme_mod( 'vw_ecommerce_store_top_discount_box') != '') { ?>

<section class="top-discount">
	<?php $vw_ecommerce_store_discount_pages = array();
      for ( $count = 0; $count <= 0; $count++ ) {
        $mod = absint( get_theme_mod( 'vw_ecommerce_store_top_discount_box' ));
        if ( 'page-none-selected' != $mod ) {
          $vw_ecommerce_store_discount_pages[] = $mod;
        }
      }
      if( !empty($vw_ecommerce_store_discount_pages) ) :
        $args = array(
          'post_type' => 'page',
          'post__in' => $vw_ecommerce_store_discount_pages,
          'orderby' => 'post__in'
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) :
          $count = 0;
          while ( $query->have_posts() ) : $query->the_post(); ?>
          	<div class="discount-box">
          		<?php the_post_thumbnail(); ?>
          		<div class="discount-inner-box">
          			<div class="container">	          		
	          			<div class="row">
	          				<div class="col-lg-6 col-md-6">
	          					<h3><?php the_title(); ?></h3>
	          				</div>
	          				<div class="col-lg-6 col-md-6">
	          					<div class="discount-btn">
	          						<a href="<?php the_permalink(); ?>"><?php echo esc_html_e('SHOP NOW','vw-ecommerce-store'); ?><span class="screen-reader-text"><?php esc_html_e( 'SHOP NOW','vw-ecommerce-store' );?></span></a>
	          					</div>
	          				</div>
	          			</div>
	            	</div>
	            </div>
          	</div>
          <?php $count++; endwhile; ?>
        <?php else : ?>
            <div class="no-postfound"></div>
        <?php endif;
      endif;
      wp_reset_postdata();
    ?>
</section>

<?php }?>