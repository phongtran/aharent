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
                    
                    <div class="product-categories-icon flex-column align-self-center">
                        <div class="top-bar align-self-top"></div>
                        <div class="middle-bar align-self-center"></div>
                        <div class="bottom-bar align-self-bottom"></div>
                    </div>
                    
                    <div class="product-categories-label">
                        <span>Danh mục sản phẩm</span>
                        <!-- <span class="product-categories-more-icon"></span> -->
                    </div>
                                
                </div>

                <div class="search-box col-sm-7 d-flex align-items-center">
                    <input name="search-box" class="form-control" type="text" placeholder="Search your product" />
                    <button name="search-button" class="aha-button btn btn-outline-secondary search-btn" type="button">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/lookup.png' ?>" />
                    </button>
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


