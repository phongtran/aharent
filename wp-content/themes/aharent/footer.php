    <footer>

        <div class="container">
            <div class="footer-links">

                <div class="footer-col">


                    <div class="col-items">
                        <div class="company-name">
                            <p>CÔNG TY TNHH TALE TECHNOLOGIES VIỆT NAM</p>
                            <div class="company-info">
                                <p class="info-item">Địa chỉ trụ sở:</p>
                                <p>69A Đường 43, Phường Tân Quy, Quận 7, Thành phố Hồ Chí Minh, Việt Nam</p>
                                <p class="info-item">Giấy ĐKDN số:</p>
                                <p>0316362084 do Sở Kế hoạch và Đầu tư TP. Hồ Chí Minh cấp ngày 01/7/2020</p>
                                <p class="info-item">Số điện thoại/Zalo:</p>
                                <p>0867.051.437</p>
                                <p class="info-item">Email:</p>
                                <p> cskh@aharent.com</p>
                            </div>
                        </div>

                        <div class="copyright">
                            <p>© 2021 - Bản quyền của AhaRent</p>
                            <p>Website đang ở giai đoạn beta</p>
                        </div>

                        <div>
                            <p><a href='http://online.gov.vn/Home/WebDetails/94403'><img width="200px" alt='' title='' src="<?php echo get_template_directory_uri() ?>/assets/img/logoSaleNoti.png" /></a></p>
                        </div>



                    </div>

                </div>

                <div class="footer-col">
                    <div class="title">
                        <h4>TÌM HIỂU THÊM</h4>
                    </div>

                    <div class="col-items">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer-menu'
                            )
                        );


                        ?>
                    </div>

                </div>


                <div class="footer-col">
                    <div class="title">
                        <h4>PHƯƠNG THỨC THANH TOÁN</h4>
                    </div>

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


                        <?php // dynamic_sidebar( 'footer-social-links' ); 
                        ?>

                    </div>
                </div>

            </div>


        </div>

    </footer>

    <?php wp_footer(); ?>


    <div class="chat-area-1">
        <!-- <div class="zalo">
            <div class="zalo-chat-widget" data-oaid="1959065384440068544" data-welcome-message="Rất vui khi được hỗ trợ bạn!" data-autopopup="0" data-width="" data-height=""></div>

            <script src="https://sp.zalo.me/plugins/sdk.js"></script>
        </div> -->

        <div class="fb">
            <!-- Messenger Chat Plugin Code -->
            <div id="fb-root"></div>

            <!-- Your Chat Plugin code -->
            <div id="fb-customer-chat" class="fb-customerchat">
            </div>

            <?php if (!WP_DEBUG) : ?>
                <script>
                    var chatbox = document.getElementById('fb-customer-chat');
                    chatbox.setAttribute("page_id", "103026198160404");
                    // chatbox.setAttribute("page_id", "100081179862004");
                    chatbox.setAttribute("attribution", "biz_inbox");

                    window.fbAsyncInit = function() {
                        FB.init({
                            xfbml: true,
                            version: 'v12.0'
                        });
                    };

                    (function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s);
                        js.id = id;
                        js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                </script>
            <?php endif ?>
        </div>
    </div>
    </body>

    </html>