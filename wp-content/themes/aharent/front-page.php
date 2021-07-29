<?php get_header(); ?>


<div class="mega-sale">
    <div class="container">
        <a href="#"><img src="<?php echo get_template_directory_uri() . '/assets/img/mega-sale.png' ?>" /></a>
    </div>
</div>

<div class="container">
    <div class="renting-instructions">
        <div class="title d-flex align-items-center">
            <h3>CÁCH THUÊ ĐƠN GIẢN</h3>
        </div>
        <div class="renting-steps d-flex">
            
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
                        <h4>Nhận hàng tại nhà</h4>
                    </div>
                    <div class="notes">
                        <p>Tiện lợi hơn với dịch vụ giao hàng đến tận nhà.</p>
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
                        <h4>Thanh toán mỗi tháng</h4>
                    </div>
                    <div class="notes">
                        <p>Thanh toán theo tháng và không phải dùng nhiều ngân sách mua trọn ngay từ đầu.</p>
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
                        <h4>Sở hữu hoặc trả lại</h4>
                    </div>
                    <div class="notes">
                        <p>Có thể mua, đổi, hoặc trả lại sản phẩm tùy thích.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="popular-products">
        <div class="title">
            <h3>SẢN PHẨM NỔI BẬT</h3>
        </div>

        


            <?php

            woocommerce_product_loop_start();

                $args = array(
                    'post_type'   => 'product',
                    'stock'       => 1,
                    'showposts'   => 12,
                    'orderby'     => 'date',
                    'order'       => 'DESC' ,
                );

                $loop = new WP_Query( $args );

                while ( $loop->have_posts() ) :
                    $loop->the_post();
                    
                    wc_get_template_part( 'content', 'product' );
                endwhile;

                wp_reset_query();

                woocommerce_product_loop_end();
            ?>
        
    </div>

</div>

<?php get_footer(); ?>