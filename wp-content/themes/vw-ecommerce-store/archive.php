<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package VW Ecommerce Store
 */

get_header(); ?>

<div class="container">
  <main id="maincontent" role="main" class="middle-align">
    <?php
      $vw_ecommerce_store_theme_lay = get_theme_mod( 'vw_ecommerce_store_theme_options','Right Sidebar');
      if($vw_ecommerce_store_theme_lay == 'Left Sidebar'){ ?>
      <div class="row m-0">
        <div class="col-lg-4 col-md-4" id="sidebar"><?php dynamic_sidebar('sidebar-1');?></div>
        <div id="our-services" class="services col-lg-8 col-md-8">
          <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
          ?>
          <?php if ( have_posts() ) :
            /* Start the Loop */  
            while ( have_posts() ) : the_post();
              get_template_part( 'template-parts/content',get_post_format()); 
            endwhile;

            else :
              get_template_part( 'no-results' ); 
            endif; 
          ?>
          <?php if( get_theme_mod( 'vw_ecommerce_store_blog_pagination_hide_show',true) != '') { ?>
            <div class="navigation">
              <?php vw_ecommerce_store_blog_posts_pagination(); ?>
                <div class="clearfix"></div>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="clearfix"></div>
    <?php }else if($vw_ecommerce_store_theme_lay == 'Right Sidebar'){ ?>
      <div class="row m-0">
        <div id="our-services" class="services col-lg-8 col-md-8">
          <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
          ?>
          <?php if ( have_posts() ) :
            /* Start the Loop */ 
            while ( have_posts() ) : the_post();
              get_template_part( 'template-parts/content',get_post_format()); 
            endwhile;

            else :
              get_template_part( 'no-results' ); 
            endif; 
          ?>
          <?php if( get_theme_mod( 'vw_ecommerce_store_blog_pagination_hide_show',true) != '') { ?>
            <div class="navigation">
              <?php vw_ecommerce_store_blog_posts_pagination(); ?>
                <div class="clearfix"></div>
            </div>
          <?php } ?>
        </div>
        <div class="col-lg-4 col-md-4" id="sidebar"><?php dynamic_sidebar('sidebar-1');?></div>
      </div>
    <?php }else if($vw_ecommerce_store_theme_lay == 'One Column'){ ?>
      <div id="our-services" class="services">
        <?php
          the_archive_title( '<h1 class="page-title">', '</h1>' );
          the_archive_description( '<div class="taxonomy-description">', '</div>' );
        ?>
        <?php if ( have_posts() ) :
          /* Start the Loop */  
          while ( have_posts() ) : the_post();
            get_template_part( 'template-parts/content',get_post_format()); 
          endwhile;

          else :
            get_template_part( 'no-results' ); 
          endif; 
        ?>
        <?php if( get_theme_mod( 'vw_ecommerce_store_blog_pagination_hide_show',true) != '') { ?>
          <div class="navigation">
            <?php vw_ecommerce_store_blog_posts_pagination(); ?>
              <div class="clearfix"></div>
          </div>
        <?php } ?>
      </div>
    <?php }else if($vw_ecommerce_store_theme_lay == 'Three Columns'){ ?>
      <div class="row m-0">
        <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-1');?></div>
        <div id="our-services" class="services col-lg-6 col-md-6">
          <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
          ?>
          <?php if ( have_posts() ) :
            /* Start the Loop */  
            while ( have_posts() ) : the_post();
              get_template_part( 'template-parts/content',get_post_format()); 
            endwhile;

            else :
              get_template_part( 'no-results' ); 
            endif; 
          ?>
          <?php if( get_theme_mod( 'vw_ecommerce_store_blog_pagination_hide_show',true) != '') { ?>
            <div class="navigation">
              <?php vw_ecommerce_store_blog_posts_pagination(); ?>
                <div class="clearfix"></div>
            </div>
          <?php } ?>
        </div>
        <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-2');?></div>
      </div>
    <?php }else if($vw_ecommerce_store_theme_lay == 'Four Columns'){ ?>
      <div class="row m-0">
        <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-1');?></div>
        <div id="our-services" class="services col-lg-3 col-md-3">
          <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
          ?>
          <?php if ( have_posts() ) :
            /* Start the Loop */  
            while ( have_posts() ) : the_post();
              get_template_part( 'template-parts/content',get_post_format()); 
            endwhile;

            else :
              get_template_part( 'no-results' ); 
            endif; 
          ?>
          <?php if( get_theme_mod( 'vw_ecommerce_store_blog_pagination_hide_show',true) != '') { ?>
            <div class="navigation">
              <?php vw_ecommerce_store_blog_posts_pagination(); ?>
                <div class="clearfix"></div>
            </div>
          <?php } ?>
        </div>
        <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-2');?></div>
        <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-3');?></div>
      </div>
    <?php }else if($vw_ecommerce_store_theme_lay == 'Grid Layout'){ ?>
      <div class="row m-0">
        <div id="our-services" class="services flipInX col-lg-9 col-md-9">
          <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
          ?>
          <div class="row">
            <?php if ( have_posts() ) :
              /* Start the Loop */  
              while ( have_posts() ) : the_post();
                get_template_part( 'template-parts/grid-layout' ); 
              endwhile;

              else :
                get_template_part( 'no-results' ); 
              endif; 
            ?>
          </div>
          <?php if( get_theme_mod( 'vw_ecommerce_store_blog_pagination_hide_show',true) != '') { ?>
            <div class="navigation">
              <?php vw_ecommerce_store_blog_posts_pagination(); ?>
                <div class="clearfix"></div>
            </div>
          <?php } ?>
        </div>
        <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-1');?></div>
      </div>
    <?php }else{ ?>
      <div class="row m-0">
        <div id="our-services" class="services col-lg-8 col-md-8">
          <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
          ?>
          <?php if ( have_posts() ) :
            /* Start the Loop */  
            while ( have_posts() ) : the_post();
              get_template_part( 'template-parts/content',get_post_format()); 
            endwhile;

            else :
              get_template_part( 'no-results' ); 
            endif; 
          ?>
          <?php if( get_theme_mod( 'vw_ecommerce_store_blog_pagination_hide_show',true) != '') { ?>
            <div class="navigation">
              <?php vw_ecommerce_store_blog_posts_pagination(); ?>
                <div class="clearfix"></div>
            </div>
          <?php } ?>
        </div>
        <div class="col-lg-4 col-md-4" id="sidebar"><?php dynamic_sidebar('sidebar-1');?></div>
      </div>
    <?php } ?>
    <div class="clearfix"></div>
  </main>
</div>

<?php get_footer(); ?>