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
                                    <a target="_blank" href="https://www.facebook.com/aharentvn"><img src="<?php echo get_template_directory_uri() . '/assets/img/facebook.png' ?>" /></a>
                                </li>

                                <li>
                                    <a target="_blank" href="https://www.instagram.com/aharentvn/"><img src="<?php echo get_template_directory_uri() . '/assets/img/instagram.png' ?>" /></a>
                                </li>

                                <li>
                                    <a target="_blank" href="https://zalo.me/1959065384440068544"><img src="<?php echo get_template_directory_uri() . '/assets/img/zalo.png' ?>" /></a>
                                </li>
                                

                            </ul>

                        
                        <?php // dynamic_sidebar( 'footer-social-links' ); ?>

                    </div>
                </div>

            </div>

    
        </div>

    </footer>

    <?php wp_footer(); ?>


    <div class="chat-area">
        <div class="zalo">
            <div class="zalo-chat-widget" data-oaid="1959065384440068544" data-welcome-message="Rất vui khi được hỗ trợ bạn!" data-autopopup="0" data-width="" data-height=""></div>

            <script src="https://sp.zalo.me/plugins/sdk.js"></script>
        </div>

        <div class="fb">
            <!-- Messenger Chat Plugin Code -->
            <div id="fb-root"></div>

            <!-- Your Chat Plugin code -->
            <div id="fb-customer-chat" class="fb-customerchat">
            </div>

            <script>
            var chatbox = document.getElementById('fb-customer-chat');
            chatbox.setAttribute("page_id", "534088900838624");
            chatbox.setAttribute("attribution", "biz_inbox");

            window.fbAsyncInit = function() {
                FB.init({
                xfbml            : true,
                version          : 'v12.0'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
            </script>
        </div>
    </div>
</body>
</html>