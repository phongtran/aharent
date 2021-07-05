<?php
/**
 * The template part for header
 *
 * @package VW Ecommerce Store 
 * @subpackage vw_ecommerce_store
 * @since VW Ecommerce Store 1.0
 */
?>
<div class="container">
	<div id="header" class="menubar">
		<div class="header-menu <?php if( get_theme_mod( 'vw_ecommerce_store_sticky_header', false) != '' || get_theme_mod( 'vw_ecommerce_store_stickyheader_hide_show', false) != '') { ?> header-sticky"<?php } else { ?>close-sticky <?php } ?>">
			<div class="row">
				<div class="<?php if( get_theme_mod( 'vw_ecommerce_store_header_search',true) != '') { ?>col-lg-11 col-md-11 col-6"<?php } else { ?>col-lg-12 col-md-12 <?php } ?>">
					<?php if(has_nav_menu('primary')){ ?>
						<div class="toggle-nav mobile-menu">
						    <button role="tab" onclick="vw_ecommerce_store_menu_open_nav()" class="responsivetoggle"><i class="<?php echo esc_attr(get_theme_mod('vw_ecommerce_store_res_menus_open_icon','fas fa-bars')); ?>"></i><span class="screen-reader-text"><?php esc_html_e('Open Button','vw-ecommerce-store'); ?></span></button>
						</div> 
					<?php } ?>
					<div id="mySidenav" class="nav sidenav">
			          	<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Top Menu', 'vw-ecommerce-store' ); ?>">
				            <?php 
								if(has_nav_menu('primary')){
									wp_nav_menu( array( 
										'theme_location' => 'primary',
										'container_class' => 'main-menu clearfix' ,
										'menu_class' => 'clearfix',
										'items_wrap' => '<ul id="%1$s" class="%2$s mobile_nav">%3$s</ul>',
										'fallback_cb' => 'wp_page_menu',
									) ); 
								}
							?>
				            <a href="javascript:void(0)" class="closebtn mobile-menu" onclick="vw_ecommerce_store_menu_close_nav()"><i class="<?php echo esc_attr(get_theme_mod('vw_ecommerce_store_res_close_menus_icon','fas fa-times')); ?>"></i><span class="screen-reader-text"><?php esc_html_e('Close Button','vw-ecommerce-store'); ?></span></a>
			          	</nav>
			        </div>
				</div>
				<div class="col-lg-1 col-md-1 col-6 search-box">
			        <?php if( get_theme_mod( 'vw_ecommerce_store_header_search',true) != '') { ?>
			        	<span><a href="#"><i class="<?php echo esc_attr(get_theme_mod('vw_ecommerce_store_search_icon','fas fa-search')); ?>"></i></a></span>
			        <?php }?>
			    </div>
			    <div class="serach_outer">
			        <div class="closepop"><a href="#maincontent"><i class="<?php echo esc_attr(get_theme_mod('vw_ecommerce_store_search_close_icon','fa fa-window-close')); ?>"></i></a></div>
			        <div class="serach_inner">
				        <?php if(class_exists('woocommerce')){ ?>
					        <?php get_product_search_form(); ?>
				        <?php }
					    else { ?>
					        <?php get_search_form(); ?>
					    <?php } ?>
				    </div>
			    </div>
			</div>
		</div>
	</div>
</div>