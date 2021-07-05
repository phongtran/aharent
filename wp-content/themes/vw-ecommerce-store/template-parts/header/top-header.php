<?php
/**
 * The template part for header
 *
 * @package VW Ecommerce Store 
 * @subpackage vw_ecommerce_store
 * @since VW Ecommerce Store 1.0
 */
?>
<?php if( get_theme_mod( 'vw_ecommerce_store_topbar_hide_show', false) != '' || get_theme_mod( 'vw_ecommerce_store_resp_topbar_hide_show', false) != '') { ?>
  <div class="container">
    <div class="lower-header">
      <div class="row">
        <div class="col-lg-3 col-md-3">
          <?php if(class_exists('woocommerce')){ ?>
            <button class="product-btn"><i class="fas fa-bars"></i><?php echo esc_html_e('CATEGORIES','vw-ecommerce-store'); ?></button>
            <div class="product-cat">
              <?php
                $args = array(
                  'orderby'    => 'title',
                  'order'      => 'ASC',
                  'hide_empty' => 0,
                  'parent'  => 0
                );
                $product_categories = get_terms( 'product_cat', $args );
                $count = count($product_categories);
                if ( $count > 0 ){
                    foreach ( $product_categories as $product_category ) {
                      $product_cat_id   = $product_category->term_id;
                      $cat_link = get_category_link( $product_cat_id );
                      if ($product_category->category_parent == 0) { ?>
                    <li class="drp_dwn_menu"><a href="<?php echo esc_url(get_term_link( $product_category ) ); ?>">
                    <?php
                  }
                  echo esc_html( $product_category->name ); ?></a><i class="fas fa-chevron-right"></i></li>
              <?php } } ?>
            </div>
          <?php }?>
        </div>
        <div class="col-lg-5 col-md-6">
          <?php if( get_theme_mod( 'vw_ecommerce_store_location') != '' ) { ?>
            <span><i class="<?php echo esc_attr(get_theme_mod('vw_ecommerce_store_location_address_icon','fas fa-map-marker-alt')); ?>"></i><?php echo esc_html(get_theme_mod('vw_ecommerce_store_location','') ); ?></span>
          <?php }?>
        </div>
        <div class="col-lg-4 col-md-3">
          <?php if( get_theme_mod( 'vw_ecommerce_store_order_tracking') != '') { ?>
            <div class="order-track">
              <span><i class="<?php echo esc_attr(get_theme_mod('vw_ecommerce_store_order_tracking_icon','fas fa-truck')); ?>"></i><?php echo esc_html_e('Order Tracking','vw-ecommerce-store'); ?></span>
              <div class="order-track-hover">
                <?php echo do_shortcode('[woocommerce_order_tracking]','vw-ecommerce-store'); ?>
              </div>
            </div>
          <?php }?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>