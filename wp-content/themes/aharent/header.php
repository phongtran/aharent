<!DOCTYPE html>
<html>
    <head>
        <title><?php echo get_bloginfo(); ?> - <?php echo get_the_title(); ?></title>

        <?php wp_head(); ?>
    </head>

    <body>

        <header>
            <div class="container h-100 d-flex align-items-center">
                <a class="logo-link" href="/">
                    <div class="logo col-sm-1"></div>
                </a>

                <div class="product-categories d-flex col-sm-2">
                    
                    <a class="product-categories-pop-up" href="#">    
                        <div class="d-flex">
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
                                    <?php if ( $category->name == 'Others') continue; ?>

                                    
                                    
                                        <a href="/store?filters=product_cat[<?php echo $category->term_id ?>]">
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

                <div class="search-box col-sm-7 d-flex align-items-center">

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

                    
                </div>
                

                <!-- <div class="user-account col-sm-1"></div> -->

            </div>

        </header>


