<?php /* Template name: About */
    get_header();
?>

<div class="container">
    <div class="about-image">
        <img src="<?php echo get_template_directory_uri() ?>/assets/img/about.png" />
    </div>
    
    <div class="about-content">
        <?php the_content(); ?>
    </div>
</div>




<?php get_footer(); ?>