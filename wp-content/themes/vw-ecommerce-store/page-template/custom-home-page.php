<?php
/**
 * Template Name: Custom Home Page
 */

get_header(); ?>

<main id="maincontent" role="main">
  <?php do_action( 'vw_ecommerce_store_before_slider' ); ?>

  <?php if( get_theme_mod( 'vw_ecommerce_store_slider_hide_show', false) != '' || get_theme_mod( 'vw_ecommerce_store_resp_slider_hide_show', false) != '') { ?>

    <section id="slider">
      <div class="container">
        <div class="row">
          <div class="<?php if( get_theme_mod( 'vw_ecommerce_store_sale_banner_hide') == true) { ?>col-lg-8 col-md-12"<?php } else { ?>col-lg-12 col-md-12 <?php } ?>">
            <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="<?php echo esc_attr(get_theme_mod( 'vw_ecommerce_store_slider_speed',4000)) ?>"> 
              <?php $vw_ecommerce_store_sliders_page = array();
                for ( $count = 1; $count <= 4; $count++ ) {
                  $mod = intval( get_theme_mod( 'vw_ecommerce_store_slider_page' . $count ));
                  if ( 'page-none-selected' != $mod ) {
                    $vw_ecommerce_store_sliders_page[] = $mod;
                  }
                }
                if( !empty($vw_ecommerce_store_sliders_page) ) :
                  $args = array(
                    'post_type' => 'page',
                    'post__in' => $vw_ecommerce_store_sliders_page,
                    'orderby' => 'post__in'
                  );
                  $query = new WP_Query( $args );
                  if ( $query->have_posts() ) :
                    $i = 1;
              ?>
              <div class="carousel-inner" role="listbox">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                  <div <?php if($i == 1){echo 'class="carousel-item active"';} else{ echo 'class="carousel-item"';}?>>
                    <?php the_post_thumbnail(); ?>
                    <div class="carousel-caption">
                      <div class="inner_carousel">
                        <h1><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                        <p><?php $excerpt = get_the_excerpt(); echo esc_html( vw_ecommerce_store_string_limit_words( $excerpt, esc_attr(get_theme_mod('vw_ecommerce_store_slider_excerpt_number','30')))); ?></p>
                        <?php if( get_theme_mod('vw_ecommerce_store_slider_button_text','SHOP NOW') != ''){ ?>
                          <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_theme_mod('vw_ecommerce_store_slider_button_text',__('SHOP NOW','vw-ecommerce-store')));?><span class="screen-reader-text"><?php echo esc_html(get_theme_mod('vw_ecommerce_store_slider_button_text',__('SHOP NOW','vw-ecommerce-store')));?></span></a>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                <?php $i++; endwhile; 
                wp_reset_postdata();?>
              </div>
              <?php else : ?>
                  <div class="no-postfound"></div>
              <?php endif;
              endif;?>
              <a class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev" role="button">
                <span class="carousel-control-prev-icon w-auto h-auto" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                <span class="screen-reader-text"><?php esc_html_e( 'Previous','vw-ecommerce-store' );?></span>
              </a>
              <a class="carousel-control-next" data-bs-target="#carouselExampleCaptions" data-bs-slide="next" role="button">
                <span class="carousel-control-next-icon w-auto h-auto" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                <span class="screen-reader-text"><?php esc_html_e( 'Next','vw-ecommerce-store' );?></span>
              </a>
            </div>
          </div>
          <?php if( get_theme_mod( 'vw_ecommerce_store_sale_banner_hide') != '') { ?>
          <div class="col-lg-4 col-md-12">
            <div class="Sale-banner">
              <?php $vw_ecommerce_store_sale_pages = array();
                for ( $count = 1; $count <= 2; $count++ ) {
                  $mod = intval( get_theme_mod( 'vw_ecommerce_store_sale_page' . $count ));
                  if ( 'page-none-selected' != $mod ) {
                    $vw_ecommerce_store_sale_pages[] = $mod;
                  }
                }
                if( !empty($vw_ecommerce_store_sale_pages) ) :
                  $args = array(
                    'post_type' => 'page',
                    'post__in' => $vw_ecommerce_store_sale_pages,
                    'orderby' => 'post__in'
                  );
                  $query = new WP_Query( $args );
                  if ( $query->have_posts() ) :
                    $i = 1;
              ?>
              <div class="row">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>            
                  <div class="col-lg-12 col-md-6">
                    <div class="carousel-inner1" role="listbox">
                      <?php the_post_thumbnail(); ?>
                      <div class="carousel-caption1">
                        <h3><?php the_title(); ?></h3>
                        <?php if( get_theme_mod('vw_ecommerce_store_sale_banner_button_text','SHOP NOW') != ''){ ?>
                          <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_theme_mod('vw_ecommerce_store_sale_banner_button_text',__('SHOP NOW','vw-ecommerce-store')));?><span class="screen-reader-text"><?php echo esc_html(get_theme_mod('vw_ecommerce_store_sale_banner_button_text',__('SHOP NOW','vw-ecommerce-store')));?></span></a>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                <?php $i++; endwhile; 
                  wp_reset_postdata();?>
                <?php else : ?>
                  <div class="no-postfound"></div>
                <?php endif;
                endif;?>
              </div>
            </div>
          </div>
          <?php }?>
        </div>
      </div>
      <div class="clearfix"></div>
    </section>

  <?php } ?>

  <?php do_action( 'vw_ecommerce_store_after_slider' ); ?>

  <section id="serv-section">
    <div class="container">
      <?php if( get_theme_mod( 'vw_ecommerce_store_section_title') != '' ) { ?>
        <h2><?php echo esc_html(get_theme_mod('vw_ecommerce_store_section_title','') ); ?></h2>
        <hr>
      <?php }?>
      <?php $vw_ecommerce_store_product_pages = array();
        for ( $count = 0; $count <= 0; $count++ ) {
          $mod = absint( get_theme_mod( 'vw_ecommerce_store_product_page' ));
          if ( 'page-none-selected' != $mod ) {
            $vw_ecommerce_store_product_pages[] = $mod;
          }
        }
        if( !empty($vw_ecommerce_store_product_pages) ) :
          $args = array(
            'post_type' => 'page',
            'post__in' => $vw_ecommerce_store_product_pages,
            'orderby' => 'post__in'
          );
          $query = new WP_Query( $args );
          if ( $query->have_posts() ) :
            $count = 0;
            while ( $query->have_posts() ) : $query->the_post(); ?>
              <?php the_content(); ?>
            <?php $count++; endwhile; ?>
          <?php else : ?>
              <div class="no-postfound"></div>
          <?php endif;
        endif;
        wp_reset_postdata();
      ?>
    </div>
  </section>

  <?php do_action( 'vw_ecommerce_store_after_product_section' ); ?>

  <div class="content-vw">
    <div class="container">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
      <?php endwhile; // end of the loop. ?>
    </div>
  </div>
</main>

<?php get_footer(); ?>