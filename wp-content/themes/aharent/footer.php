    <footer>

        <div class="container">
            <div class="footer-links">
                
                <div class="footer-col">
               

                    <div class="col-items">
                        <div class="company-name">
                            <p>Công ty TNHH Tale</p>
                            <p>Technologies Việt Nam</p>
                            <p class="hotline">Hotline: (+84) 867 051 437</p>
                        </div>

                        <div class="copyright">
                            <p>© 2021 - Bản quyền của AhaRent</p>
                            <p>Website đang ở giai đoạn Beta</p>
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

                    <div class="col-items payment-links">
                        <div><img src="<?php echo get_template_directory_uri() . '/assets/img/visa-icon.png' ?>" /></div>
                        <div><img src="<?php echo get_template_directory_uri() . '/assets/img/mastercard-icon.png' ?>" /></div>
                        <div><img src="<?php echo get_template_directory_uri() . '/assets/img/jcb-icon.png' ?>" /></div>
                    </div>

                </div>
                
                <div class="footer-col">
                    <h4 class="text-center">KẾT NỐI VỚI CHÚNG TÔI</h4>
                    <div class="social-links">

                            <ul>
                                <li>
                                    <a href="https://www.facebook.com/aharentvn"><img src="<?php echo get_template_directory_uri() . '/assets/img/facebook.png' ?>" /></a>
                                </li>

                                <li>
                                    <a href="https://www.instagram.com/aharentvn/"><img src="<?php echo get_template_directory_uri() . '/assets/img/instagram.png' ?>" /></a>
                                </li>

                                <li>
                                    <a href="https://zalo.me/1959065384440068544"><img src="<?php echo get_template_directory_uri() . '/assets/img/zalo.png' ?>" /></a>
                                </li>
                                

                            </ul>

                        
                        <?php // dynamic_sidebar( 'footer-social-links' ); ?>

                    </div>
                </div>

            </div>

    
        </div>

    </footer>

    <?php wp_footer(); ?>
</body>
</html>