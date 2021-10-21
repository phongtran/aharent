<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <meta property="og:site_name" content="<?php echo get_bloginfo() ?>">
        <meta property="og:title" content="<?php echo get_bloginfo() ?>" />
        <meta property="og:description" content="<?php echo get_bloginfo() ?>" />
        <meta property="og:image" itemprop="image" content="<?php
            if ( is_single() )
            {
                global $product;
                echo get_the_post_thumbnail_url( $product-> ID );
            }
            else
            {
                echo get_template_directory_uri() . '/assets/img/thumb.jpg';
            }
        
        ?>">
        <meta property="og:type" content="website" />

        <title>
            <?php 
                echo get_bloginfo();
                    
                if ( is_archive() || is_single() )
                {
                    echo ' &raquo; ';
                    woocommerce_page_title();
                }

                if ( is_single() || is_page() )
                    wp_title();
            ?>
        </title>

        <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri() ?>/assets/img/favicon.png"/>


        <?php wp_head(); ?>
    </head>

    <body>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PK83XVS"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div class="page-loading"></div>

        <header>

            <div class="mobile-header container h-100 d-flex align-items-center">
                <div class="row">

                    <a class="logo-link" href="/">
                        <div class="logo col-sm-1"></div>
                    </a>

                    <div class="cart col-sm-1">
                        <span class="shopping-cart">
                            <a href="<?php echo wc_get_cart_url(); ?>">
                                <img src="<?php echo get_template_directory_uri() . '/assets/img/shopping-cart.png' ?>" />
                            </a>

                            <?php $cart_item_count = WC()->cart->get_cart_contents_count(); ?>

                            <?php if ( $cart_item_count > 0 ) : ?>
                                <div class="cart-quantity">
                                    <span><?php echo $cart_item_count ?></span>
                                </div>
                            <?php endif ?>
                        </span>

                        
                    </div>

                    <div class="product-categories">
                        
                        <a class="product-categories-pop-up" href="#">    
                            
                                <div class="product-categories-icon flex-column align-self-center">
                                        <div class="top-bar align-self-top"></div>
                                        <div class="middle-bar align-self-center"></div>
                                        <div class="bottom-bar align-self-bottom"></div>
                                </div>
                                    
                            
                        </a>

                        <div class="pop-up-menu">

                            <div class="categories-pop-up-list">
                                <?php
                                    $orderby = 'name';
                                    $order = 'asc';
                                    $hide_empty = false ;
                                    $cat_args = array(
                                        'orderby'    => $orderby,
                                        'order'      => $order,
                                        'hide_empty' => $hide_empty,
                                    );
                                    
                                    $product_categories = get_terms( 'product_cat', $cat_args );
                                ?>
                                <?php if ( !empty( $product_categories )) : ?>
                        
                        
                                    <?php foreach ( $product_categories as $key => $category ) : ?>
                                        <?php if ( $category->term_id == 15) continue; ?>

                                        
                                        
                                            <a href="<?php echo get_term_link( $category->term_id ) ?>">
                                                <?php
                                                    $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ); 
                                                    $image = wp_get_attachment_url( $thumbnail_id );
                                                ?>
                                                
                                                <div class="category-item"> 
                                                    <div class="category-image">
                                                        <img src="<?php echo $image ?>" />
                                                    </div>

                                                    
                                                    <div class="category-name">
                                                        <?php echo $category->name ?>
                                                    </div>

                                                </div>
                                            </a>
                                        
                                        
                                    <?php endforeach ?>

                                    
                                <?php endif ?>

                            </div>
                        </div>

                        
                    </div>
                </div>

                <div class="row search">
                    <div class="search-box col-sm-7 d-flex align-items-center">

                        <?php echo do_shortcode('[fibosearch]'); ?>
                        <!-- <input name="search-box" class="form-control" type="text" placeholder="Tìm kiếm sản phẩm..." />
                        <button name="search-button" class="aha-button btn btn-outline-secondary search-btn" type="button">
                            <img src="<?php echo get_template_directory_uri() . '/assets/img/lookup.png' ?>" />
                        </button> -->
                    </div>

                </div>

                <!-- <div class="user-account col-sm-1"></div> -->

            </div>
        
                
            <div class="desktop-header container">
                <a class="logo-link" href="/">
                    <div class="logo col-sm-1"></div>
                </a>

                <div class="product-categories col-sm-2">
                    
                    <div class="product-categories-pop-up">
                        <a class="" href="#">    
                            
                                <div class="product-categories-icon flex-column align-self-center">
                                        <div class="top-bar align-self-top"></div>
                                        <div class="middle-bar align-self-center"></div>
                                        <div class="bottom-bar align-self-bottom"></div>
                                </div>
                                    
                                <div class="product-categories-label">
                                    <p>Danh mục</p>
                                    <p>Sản phẩm</p>
                                    <!-- <span class="product-categories-more-icon"></span> -->
                                </div>        
                        </a>
                    </div>

                    <div class="pop-up-menu">

                        <div class="categories-pop-up-list">
                            <?php
                                $orderby = 'name';
                                $order = 'asc';
                                $hide_empty = false ;
                                $cat_args = array(
                                    'orderby'    => $orderby,
                                    'order'      => $order,
                                    'hide_empty' => $hide_empty,
                                );
                                
                                $product_categories = get_terms( 'product_cat', $cat_args );
                            ?>
                            <?php if ( !empty( $product_categories )) : ?>
                    
                    
                                <?php foreach ( $product_categories as $key => $category ) : ?>
                                    <?php if ( $category->term_id == 15 ) continue; ?>

                                    
                                    
                                        <a href="<?php echo get_term_link( $category->term_id ) ?>">
                                            <?php
                                                $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ); 
                                                $image = wp_get_attachment_url( $thumbnail_id );
                                            ?>
                                            
                                            <div class="category-item"> 
                                                <div class="category-image">
                                                    <img src="<?php echo $image ?>" />
                                                </div>

                                                
                                                <div class="category-name">
                                                    <?php echo $category->name ?>
                                                </div>

                                            </div>
                                        </a>
                                    
                                    
                                <?php endforeach ?>

                                
                            <?php endif ?>

                        </div>

                    </div>

                    
                </div>

                <div class="desktop-search search-box col-sm-7 d-flex align-items-center">

                    <?php echo do_shortcode('[fibosearch]'); ?>
                    <!-- <input name="search-box" class="form-control" type="text" placeholder="Tìm kiếm sản phẩm..." />
                    <button name="search-button" class="aha-button btn btn-outline-secondary search-btn" type="button">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/lookup.png' ?>" />
                    </button> -->
                </div>

                <div class="cart col-sm-1">
                    <span class="shopping-cart">
                        <a href="<?php echo wc_get_cart_url(); ?>">
                            <img src="<?php echo get_template_directory_uri() . '/assets/img/shopping-cart.png' ?>" />
                        </a>

                        <?php $cart_item_count = WC()->cart->get_cart_contents_count(); ?>

                        <?php if ( $cart_item_count > 0 ) : ?>
                            <div class="cart-quantity">
                                <span><?php echo $cart_item_count ?></span>
                            </div>
                        <?php endif ?>
                    </span>

                   
                    <?php if ( !is_checkout()) $notices = wc_get_notices( 'success' ); ?>

                    <?php foreach ( $notices as $notice ) : ?>
                        <?php $message = $notice['notice']; ?>
                            <?php if ( strpos( $message, 'close-message button wc-forward' ) ) : ?>
                                <div class="message-wrapper">
                                    <div class="arrow-up"></div>
                                    <div class="add-to-cart-message">    
                                        <?php echo $message; ?>

                                        <script type="text/javascript">
                                            (function($) {
                                                $('html, body').animate({ scrollTop: 0 }, 'fast');
                                            })(jQuery)
                                        </script>
                                    </div>
                                </div>

                            <?php endif ?>
                    <?php endforeach ?>
                        

                    
                </div>
                

                <!-- <div class="user-account col-sm-1"></div> -->

            </div>


            

        </header>


