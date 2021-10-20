<?php get_header(); ?>


<div class="mega-sale">
    <div class="container">
        <img src="<?php echo get_template_directory_uri() . '/assets/img/mega-sale.jpg' ?>" />
    </div>
</div>

<div class="container">
    <div class="home-container">
        <div class="renting-instructions">
            <div class="title-bar d-flex align-items-center">
                <h3>CÁCH THUÊ ĐƠN GIẢN</h3>
            </div>
            <div class="renting-steps">
                
                <div class="col single-step">
                    <div class="step-icon-group d-flex">
                        <img class="step-number" src="<?php echo get_template_directory_uri() . '/assets/img/step-1.png' ?>" />
                        <img class="step-icon" src="<?php echo get_template_directory_uri() . '/assets/img/step-1-icon.png' ?>" />
                    </div>
                    <div class="step-notes">
                        <div class="step-notes-title">
                            <h4>Chọn sản phẩm trên Website</h4>
                        </div>
                        <div class="notes">
                            <p>Lựa chọn đa dạng với thao tác đặt hàng đơn giản.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col single-step">
                    <div class="step-icon-group d-flex">
                        <img class="step-number" src="<?php echo get_template_directory_uri() . '/assets/img/step-2.png' ?>" />
                        <img class="step-icon" src="<?php echo get_template_directory_uri() . '/assets/img/step-2-icon.png' ?>" />
                    </div>
                    <div class="step-notes">
                        <div class="step-notes-title">
                            <h4>Xác thực danh tính</h4>
                        </div>
                        <div class="notes">
                            <p>Khách hàng cung cấp thông tin cơ bản để AhaRent tiếp tục xử lý đơn hàng.</p>
                        </div>
                    </div>
                </div>

                <div class="col single-step">
                    <div class="step-icon-group d-flex">
                        <img class="step-number" src="<?php echo get_template_directory_uri() . '/assets/img/step-3.png' ?>" />
                        <img class="step-icon" src="<?php echo get_template_directory_uri() . '/assets/img/step-3-icon.png' ?>" />
                    </div>
                    <div class="step-notes">
                        <div class="step-notes-title">
                            <h4>Nhận hàng</h4>
                        </div>
                        <div class="notes">
                            <p>Khách hàng có thể nhận hàng trực tiếp tại cửa hàng hoặc tại nhà.</p>
                        </div>
                    </div>
                </div>

                <div class="col single-step">
                    <div class="step-icon-group d-flex">
                        <img class="step-number" src="<?php echo get_template_directory_uri() . '/assets/img/step-4.png' ?>" />
                        <img class="step-icon" src="<?php echo get_template_directory_uri() . '/assets/img/step-4-icon.png' ?>" />
                    </div>
                    <div class="step-notes">
                        <div class="step-notes-title">
                            <h4>Thanh toán</h4>
                        </div>
                        <div class="notes">
                            <p>Thanh toán theo ngày và không phải dùng nhiều ngân sách mua trọn ngay từ đầu.</p>
                        </div>
                    </div>
                </div>

                <div class="col single-step">
                    <div class="step-icon-group d-flex">
                        <img class="step-number" src="<?php echo get_template_directory_uri() . '/assets/img/step-5.png' ?>" />
                        <img class="step-icon" src="<?php echo get_template_directory_uri() . '/assets/img/step-5-icon.png' ?>" />
                    </div>
                    <div class="step-notes">
                        <div class="step-notes-title">
                            <h4>Trả lại</h4>
                        </div>
                        <div class="notes">
                            <p>Sản phẩm được trả lại khi hết hạn thuê.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="categories">
            <div class="title-bar d-flex align-items-center">
                <h3>DANH MỤC</h3>
            </div>
            
            <div class="categories-list">
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




        <div class="popular-products">
            <div class="title-bar">
                <h3>SẢN PHẨM NỔI BẬT</h3>
            </div>

                <?php
                    woocommerce_product_loop_start();

                    $args = get_featured_products_query();

                    $loop = new WP_Query( $args );

                    while ( $loop->have_posts() ) :
                        $loop->the_post();
                        
                        wc_get_template_part( 'content', 'product' );
                    endwhile;

                    wp_reset_query();

                    woocommerce_product_loop_end();
                ?>

            <div class="button-more">
                <a href="<?php echo get_permalink( get_option( 'woocommerce_shop_page_id' )); ?>">
                    <button class="aha-button" type="button">Xem thêm</button>
                </a>
            </div>
            
        </div>
    </div>

</div>

<?php get_footer(); ?>