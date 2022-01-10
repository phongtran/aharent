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
                        <img class="step-number"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-1.png' ?>" />
                        <img class="step-icon"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-1-icon.png' ?>" />
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
                        <img class="step-number"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-2.png' ?>" />
                        <img class="step-icon"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-2-icon.png' ?>" />
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
                        <img class="step-number"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-3.png' ?>" />
                        <img class="step-icon"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-3-icon.png' ?>" />
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
                        <img class="step-number"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-4.png' ?>" />
                        <img class="step-icon"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-4-icon.png' ?>" />
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
                        <img class="step-number"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-5.png' ?>" />
                        <img class="step-icon"
                            src="<?php echo get_template_directory_uri() . '/assets/img/step-5-icon.png' ?>" />
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
                        'parent'     => 0,
                    );
                    
                    $product_categories = get_terms( 'product_cat', $cat_args );

                    //random_total_sales();
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
                <h3>SẢN PHẨM ĐƯỢC THUÊ NHIỀU NHẤT</h3>
            </div>

            <?php
                    woocommerce_product_loop_start();

                    $args = get_most_rented_products_query();

                    $loop = new WP_Query( $args );
                    shuffle( $loop->posts );
                    

                    while ( $loop->have_posts() ) :
                        $loop->the_post();
                        
                        wc_get_template_part( 'content', 'product' );
                    endwhile;

                    wp_reset_query();

                    woocommerce_product_loop_end();
                ?>

            <div class="button-more">
                <a href="/san-pham/san-pham-thue-nhieu/">
                    <button class="aha-button" type="button">Xem thêm</button>
                </a>
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
                    shuffle( $loop->posts );
                    

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

    <?php
    $testimonials = array(
            array(
                'title'    =>  'Anh',
                'name'      =>  'Vũ Lê Dung',
                'image'     =>  get_template_directory_uri() . '/assets/img/testi-man-1.png',
                'content'   => 'Mình cần một số dụng cụ cho chuyến dã ngoại vào ngày hôm sau, mà bây giờ đi long nhông khắp thành phố Tp.HCM tìm đủ đồ thì chắc muộn mất. Tối hôm trước mình lên Aharent chọn thuê 1 đồ, vậy mà 9h sáng hôm sau đã có bạn giao hàng đem đồ tới rồi. Chưa kể bạn còn hướng dẫn tận tình cách sử dụng... như thế nào, setup sẵn đồ, mình chỉ việc sử dụng. Quá ưng luôn!'
            ),
            array(
                'title'    =>  'Chị',
                'name'      =>  'Nguyễn Minh',
                'image'     =>  get_template_directory_uri() . '/assets/img/testi-girl-big.png',
                'content'   => 'Ở nước ngoài dịch vụ cho thuê đồ trực tuyến rất phát triển, nhưng khi về Việt Nam mình tìm “mỏi mắt” không ra dịch vụ này. Ở Việt Nam chủ yếu cho thuê các mặt hàng như quần áo, xe cộ trực tiếp tại cửa hàng,... Điều này gây ra không ít bất tiện như mình cần tốn rất nhiều thời gian di chuyển từ cửa hàng này tới cửa hàng kia để lựa chọn món đồ ưng ý. May mắn được một người bạn giới thiệu tới Aharent, mình cảm thấy cuộc sống của mình nhẹ nhàng và thoải mái hơn rất nhiều.'
            ),
            array(
                'title'    =>  'Anh',
                'name'      =>  'Trần Hoàng',
                'image'     =>  get_template_directory_uri() . '/assets/img/testi-man-big.png',
                'content'   => 'Mình là một blogger chuyên review các sản phẩm về công nghệ như: laptop, máy chơi game, xe cộ,... Thông thường mỗi năm các hãng sẽ cho ra mắt ít nhất 1 dòng sản phẩm hoặc thậm chí là 3-4 dòng sản phẩm. Các dòng sản phẩm mới sẽ có giá rất đắt, mình không thể nào mua hết tất cả các sản phẩm để review cho độc giả được. Mà mượn bạn bè, các hãng tài trợ xung quanh thì số lượng cũng có hạn vì không phải ai cũng đủ tiềm lực “kinh tế”. Nhưng kể từ ngày nhờ biết đến Aharent, công việc của mình thuận lợi hơn rất nhiều, cần sản phẩm gì cứ lên Aharent tìm là có ngay. Hơn nữa phí thuê đồ phải chăng, rất nhiều sản phẩm công nghệ mới nhất đều có!'
            ),
        );
?>

    <div class="testimonies-wrapper">

        <?php foreach ( $testimonials as $testimonial ) : ?>
        
        <a href="#" class="modal-open-link">
            <div class="testi-item" data-content="<?php echo $testimonial['content'] ?>">
                <div class="testi-head">
                    <div class="person-image">
                        <img src="<?php echo $testimonial['image'] ?>" />
                    </div>
                    <div class="person-name">
                        <?php echo $testimonial['title'] ?> <span class="name"><?php echo $testimonial['name'] ?></span>
                    </div>
                    <div class="person-rating">
                        <i class="fa fa-star checked"></i>
                        <i class="fa fa-star checked"></i>
                        <i class="fa fa-star checked"></i>
                        <i class="fa fa-star checked"></i>
                        <i class="fa fa-star checked"></i>
                    </div>
                </div>
                <div class="testi-body">
                    <p><?php echo wp_trim_words( $testimonial['content'], 45 ) ?></p>
                </div>
                <div class="arrow"></div>
            </div>
        </a>

        <?php endforeach ?>

        <div class="testimonial-modal" style="display: none;">
            <div class="testi-item" data-content="<?php echo $testimonial['content'] ?>">
                <div class="testi-head">
                    <div class="person-image">
                        <img src="<?php echo $testimonial['image'] ?>" />
                    </div>
                    <div class="person-name">
                        <?php echo $testimonial['title'] ?> <span class="name"><?php echo $testimonial['name'] ?></span>
                    </div>
                    <div class="person-rating">
                        <i class="fa fa-star checked"></i>
                        <i class="fa fa-star checked"></i>
                        <i class="fa fa-star checked"></i>
                        <i class="fa fa-star checked"></i>
                        <i class="fa fa-star checked"></i>
                    </div>
                    <a href="#" class="modal-close-link">
                        <div class="modal-close">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/modal-close.png" />
                        </div>
                    </a>
                </div>
                <div class="testi-body">
                    <p><?php echo $testimonial['content'] ?></p>
                </div>
            </div>
        </div>

    </div>

</div>



<div class="pop-up-wrapper">
    <div class="pop-up">

        <?php echo do_shortcode( '[wpforms id="7103" title="false"]' ); ?>
        <?php //echo do_shortcode( '[wpforms id="6487" title="false"]' ); ?>

        <!-- <form class="sale-registration-form">
            <input placeholder="Nhập email của bạn" class="form-control">
            <button class="form-control submit-button" type="submit">Đăng ký</button>
        </form> -->
    
    
        <a href="#" class="pop-up-close-link">
            <div class="pop-up-close">
                <img src="<?php echo get_template_directory_uri() ?>/assets/img/modal-close.png" />
            </div>
        </a>

    </div>
    
</div>

<?php get_footer(); ?>