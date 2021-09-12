    <footer>

        <div class="container">
            <div class="footer-links">
                
                <div class="footer-col">
                    <a class="logo-link" href="/">
                        <div class="logo"></div>
                    </a>

                    <div class="col-items">
                        <div class="company-name">
                            <p>Công ty TNHH Tale</p>
                            <p>Technologies Việt Nam</p>
                        </div>

                  
                        
                    </div>

                </div>

                <div class="footer-col">
                    <div class="title"><h4>TÌM HIỂU THÊM</h4></div>

                    <div class="col-items">
                        <?php
                            wp_nav_menu(   
                                array ( 
                                    'theme_location' => 'footer-menu' 
                                ) 
                            );

                            
                        ?>
                    </div>

                </div>
                
                
                <div class="footer-col">
                    <div class="title"><h4>PHƯƠNG THỨC THANH TOÁN</h4></div>

                    <div class="col-items d-flex row-cols-3">
                        <a href="#"><img src="<?php echo get_template_directory_uri() . '/assets/img/visa-icon.png' ?>" /></a>
                        <a href="#"><img src="<?php echo get_template_directory_uri() . '/assets/img/mastercard-icon.png' ?>" /></a>
                        <a href="#"><img src="<?php echo get_template_directory_uri() . '/assets/img/jcb-icon.png' ?>" /></a>
                    </div>

                </div>
                
                <div class="footer-col">
                    <h4 class="text-center">KẾT NỐI VỚI CHÚNG TÔI</h4>
                    <div class="social-links">
                        
                        <?php dynamic_sidebar( 'footer-social-links' ); ?>

                    </div>
                </div>

            </div>

            <div class="copyright">
                <p>© 2021 - Bản quyền của AhaRent</p>
            </div>
        </div>

    </footer>

    <?php wp_footer(); ?>
</body>
</html>