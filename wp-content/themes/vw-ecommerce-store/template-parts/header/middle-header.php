<?php
/**
 * The template part for top header
 *
 * @package VW Ecommerce Store 
 * @subpackage vw_ecommerce_store
 * @since VW Ecommerce Store 1.0
 */
?>

<div id="topbar">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-4 align-self-lg-center align-self-md-center">
        <?php dynamic_sidebar('social-links'); ?>
      </div>
      <div class="col-lg-4 col-md-4">
        <div class="logo">
          <?php if ( has_custom_logo() ) : ?>
            <div class="site-logo"><?php the_custom_logo(); ?></div>
          <?php endif; ?>
          <?php $blog_info = get_bloginfo( 'name' ); ?>
            <?php if( get_theme_mod('vw_ecommerce_store_logo_title_hide_show',true) != ''){ ?>
            <?php if ( ! empty( $blog_info ) ) : ?>
              <?php if ( is_front_page() && is_home() ) : ?>
                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
              <?php else : ?>
                <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
              <?php endif; ?>
            <?php endif; ?>
            <?php }?>            
            <?php
              $description = get_bloginfo( 'description', 'display' );
              if ( $description || is_customize_preview() ) :
            ?>
            <?php if( get_theme_mod('vw_ecommerce_store_logo_tagline_hide_show',true) != ''){ ?>
              <p class="site-description">
                <?php echo esc_html($description); ?>
              </p>
            <?php }?>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-3 col-md-2 col-8 align-self-lg-center align-self-md-center">
        <div class="account">
          <?php if(class_exists('woocommerce')){ ?>
            <?php if ( is_user_logged_in() ) { ?>
              <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_attr_e('My Account','vw-ecommerce-store'); ?>"><i class="<?php echo esc_attr(get_theme_mod('vw_ecommerce_store_my_account_icon','fas fa-sign-in-alt')); ?>"></i><?php esc_html_e('My Account','vw-ecommerce-store'); ?><span class="screen-reader-text"><?php esc_html_e( 'My Account','vw-ecommerce-store' );?></span></a>
            <?php }
            else { ?>
              <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_attr_e('Login / Register','vw-ecommerce-store'); ?>"><i class="fas fa-user"></i><?php esc_html_e('Login / Register','vw-ecommerce-store'); ?><span class="screen-reader-text"><?php esc_html_e( 'Login / Register','vw-ecommerce-store' );?></span></a>
            <?php } ?>
          <?php }?>
        </div>
      </div>
      <div class="col-lg-1 col-md-2 col-4 align-self-lg-center align-self-md-center">
        <?php if(class_exists('woocommerce')){ ?>
          <span class="cart_no">
            <a href="<?php if(function_exists('wc_get_cart_url')){ echo esc_url(wc_get_cart_url()); } ?>" title="<?php esc_attr_e( 'shopping cart','vw-ecommerce-store' ); ?>"><i class="<?php echo esc_attr(get_theme_mod('vw_ecommerce_store_cart_icon','fas fa-shopping-basket')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'shopping cart','vw-ecommerce-store' );?></span></a>
            <span class="cart-value"> <?php echo wp_kses_data( WC()->cart->get_cart_contents_count() );?></span>
          </span>
        <?php } ?>
      </div>
    </div>
  </div>
</div>