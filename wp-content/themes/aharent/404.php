<?php get_header(); ?>

<div class="container">
    <div class="link-404">
        <img src="<?php echo get_template_directory_uri()?>/assets/img/404-image.png" />
        <span><h3><?php esc_html_e( 'KHÔNG TÌM THẤY TRANG!', 'woocommerce'); ?></h3></span>
        <span><?php esc_html_e( 'Trang bạn đang tìm kiếm không tồn tại.', 'woocommerce'); ?></span>
    </div>
</div>


<?php get_footer(); ?>