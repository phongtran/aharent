<?php
	
	/*---------------------------First highlight color-------------------*/

	$vw_ecommerce_store_first_color = get_theme_mod('vw_ecommerce_store_first_color');

	$vw_ecommerce_store_custom_css = '';

	if($vw_ecommerce_store_first_color != false){
		$vw_ecommerce_store_custom_css .='button.product-btn, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, span.cart-value, .search-box i, .serach_inner button, .products li:hover span.onsale, input[type="submit"], #footer .tagcloud a:hover, #sidebar .custom-social-icons i, #footer .custom-social-icons i, #footer-2, .scrollup i, #sidebar h3, .pagination .current, .pagination a:hover, #sidebar .tagcloud a:hover, #comments input[type="submit"], nav.woocommerce-MyAccount-navigation ul li, .woocommerce div.product span.onsale, .discount-btn a, #comments a.comment-reply-link, .toggle-nav i, #sidebar .widget_price_filter .ui-slider .ui-slider-range, #sidebar .widget_price_filter .ui-slider .ui-slider-handle, #sidebar .woocommerce-product-search button, #footer .widget_price_filter .ui-slider .ui-slider-range, #footer .widget_price_filter .ui-slider .ui-slider-handle, #footer .woocommerce-product-search button, .nav-previous a:hover, .nav-next a:hover, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current{';
			$vw_ecommerce_store_custom_css .='background-color: '.esc_attr($vw_ecommerce_store_first_color).';';
		$vw_ecommerce_store_custom_css .='}';
	}
	if($vw_ecommerce_store_first_color != false){
		$vw_ecommerce_store_custom_css .='a, #topbar .custom-social-icons i:hover, .carousel-caption1 a:hover, .carousel-caption a:hover, .products li:hover h2, #footer .custom-social-icons i:hover, #footer li a:hover, .post-main-box:hover h2, .more-btn a:hover, #sidebar ul li a:hover, .post-navigation a:hover .post-title, .post-navigation a:focus .post-title, .main-navigation a:hover, .main-navigation ul.sub-menu a:hover, .entry-content a, .sidebar .textwidget p a, .textwidget p a, #comments p a, .slider .inner_carousel p a, .post-navigation a:hover, .post-navigation a:focus, #sidebar a.custom_read_more:hover, #footer a.custom_read_more:hover, .post-main-box:hover h2 a, .post-main-box:hover .entry-date a, .post-main-box:hover .entry-author a, .post-main-box:hover .more-btn a, .single-post .post-info:hover .entry-date a, .single-post .post-info:hover .entry-author a, .logo .site-title a:hover{';
			$vw_ecommerce_store_custom_css .='color: '.esc_attr($vw_ecommerce_store_first_color).';';
		$vw_ecommerce_store_custom_css .='}';
	}
	if($vw_ecommerce_store_first_color != false){
		$vw_ecommerce_store_custom_css .='.wp-block-button .wp-block-button__link:hover{';
			$vw_ecommerce_store_custom_css .='color: '.esc_attr($vw_ecommerce_store_first_color).'!important;';
		$vw_ecommerce_store_custom_css .='}';
	}
	if($vw_ecommerce_store_first_color != false){
		$vw_ecommerce_store_custom_css .='.carousel-caption1 a:hover, .carousel-caption a:hover, .more-btn a:hover, #sidebar a.custom_read_more:hover, #footer a.custom_read_more:hover, .wp-block-button .wp-block-button__link:hover, .post-main-box:hover .more-btn a, .loader-line{';
			$vw_ecommerce_store_custom_css .='border-color: '.esc_attr($vw_ecommerce_store_first_color).';';
		$vw_ecommerce_store_custom_css .='}';
	}
	if($vw_ecommerce_store_first_color != false){
		$vw_ecommerce_store_custom_css .='#serv-section hr, .main-navigation ul ul{';
			$vw_ecommerce_store_custom_css .='border-top-color: '.esc_attr($vw_ecommerce_store_first_color).';';
		$vw_ecommerce_store_custom_css .='}';
	}
	if($vw_ecommerce_store_first_color != false){
		$vw_ecommerce_store_custom_css .='#footer h3:after, .main-navigation ul ul, .header-fixed{';
			$vw_ecommerce_store_custom_css .='border-bottom-color: '.esc_attr($vw_ecommerce_store_first_color).';';
		$vw_ecommerce_store_custom_css .='}';
	}
	if($vw_ecommerce_store_first_color != false){
		$vw_ecommerce_store_custom_css .='span.cart-value:before{';
			$vw_ecommerce_store_custom_css .='border-right-color: '.esc_attr($vw_ecommerce_store_first_color).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*---------------------------Width Layout -------------------*/

	$vw_ecommerce_store_theme_lay = get_theme_mod( 'vw_ecommerce_store_width_option','Full Width');
    if($vw_ecommerce_store_theme_lay == 'Boxed'){
		$vw_ecommerce_store_custom_css .='body{';
			$vw_ecommerce_store_custom_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
		$vw_ecommerce_store_custom_css .='}';
		$vw_ecommerce_store_custom_css .='.search-box i{';
			$vw_ecommerce_store_custom_css .='padding: 18px 24px;';
		$vw_ecommerce_store_custom_css .='}';
	}else if($vw_ecommerce_store_theme_lay == 'Wide Width'){
		$vw_ecommerce_store_custom_css .='body{';
			$vw_ecommerce_store_custom_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
		$vw_ecommerce_store_custom_css .='}';
	}else if($vw_ecommerce_store_theme_lay == 'Full Width'){
		$vw_ecommerce_store_custom_css .='body{';
			$vw_ecommerce_store_custom_css .='max-width: 100%;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*--------------------------- Slider Opacity -------------------*/

	$vw_ecommerce_store_theme_lay = get_theme_mod( 'vw_ecommerce_store_slider_opacity_color','0.5');
	if($vw_ecommerce_store_theme_lay == '0'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.1'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.1';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.2'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.2';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.3'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.3';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.4'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.4';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.5'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.5';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.6'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.6';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.7'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.7';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.8'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.8';
		$vw_ecommerce_store_custom_css .='}';
		}else if($vw_ecommerce_store_theme_lay == '0.9'){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='opacity:0.9';
		$vw_ecommerce_store_custom_css .='}';
		}

	/*---------------------------Slider Content Layout -------------------*/

	$vw_ecommerce_store_theme_lay = get_theme_mod( 'vw_ecommerce_store_slider_content_option','Left');
    if($vw_ecommerce_store_theme_lay == 'Left'){
		$vw_ecommerce_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_ecommerce_store_custom_css .='text-align:left; left:15%; right:30%; top:40%;';
		$vw_ecommerce_store_custom_css .='}';
	}else if($vw_ecommerce_store_theme_lay == 'Center'){
		$vw_ecommerce_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_ecommerce_store_custom_css .='text-align:center; left:20%; right:20%; top:50%;';
		$vw_ecommerce_store_custom_css .='}';
	}else if($vw_ecommerce_store_theme_lay == 'Right'){
		$vw_ecommerce_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_ecommerce_store_custom_css .='text-align:right; left:30%; right:10%; top:50%;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*---------------------------Slider Height ------------*/

	$vw_ecommerce_store_slider_height = get_theme_mod('vw_ecommerce_store_slider_height');
	if($vw_ecommerce_store_slider_height != false){
		$vw_ecommerce_store_custom_css .='#slider img{';
			$vw_ecommerce_store_custom_css .='height: '.esc_attr($vw_ecommerce_store_slider_height).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*---------------------------Blog Layout -------------------*/

	$vw_ecommerce_store_theme_lay = get_theme_mod( 'vw_ecommerce_store_blog_layout_option','Default');
    if($vw_ecommerce_store_theme_lay == 'Default'){
		$vw_ecommerce_store_custom_css .='.post-main-box{';
			$vw_ecommerce_store_custom_css .='';
		$vw_ecommerce_store_custom_css .='}';
	}else if($vw_ecommerce_store_theme_lay == 'Center'){
		$vw_ecommerce_store_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn{';
			$vw_ecommerce_store_custom_css .='text-align:center;';
		$vw_ecommerce_store_custom_css .='}';
		$vw_ecommerce_store_custom_css .='.post-info{';
			$vw_ecommerce_store_custom_css .='margin-top:10px;';
		$vw_ecommerce_store_custom_css .='}';
		$vw_ecommerce_store_custom_css .='.post-info hr{';
			$vw_ecommerce_store_custom_css .='margin:10px auto;';
		$vw_ecommerce_store_custom_css .='}';
	}else if($vw_ecommerce_store_theme_lay == 'Left'){
		$vw_ecommerce_store_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn, #our-services p{';
			$vw_ecommerce_store_custom_css .='text-align:Left;';
		$vw_ecommerce_store_custom_css .='}';
		$vw_ecommerce_store_custom_css .='.post-info hr{';
			$vw_ecommerce_store_custom_css .='margin-bottom:10px;';
		$vw_ecommerce_store_custom_css .='}';
		$vw_ecommerce_store_custom_css .='.post-main-box h2{';
			$vw_ecommerce_store_custom_css .='margin-top:10px;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*------------------------------Responsive Media -----------------------*/

	$vw_ecommerce_store_resp_topbar = get_theme_mod( 'vw_ecommerce_store_resp_topbar_hide_show',false);
	if($vw_ecommerce_store_resp_topbar == true && get_theme_mod( 'vw_ecommerce_store_topbar_hide_show', false) == false){
    	$vw_ecommerce_store_custom_css .='.lower-header{';
			$vw_ecommerce_store_custom_css .='display:none;';
		$vw_ecommerce_store_custom_css .='} ';
	}
    if($vw_ecommerce_store_resp_topbar == true){
    	$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_ecommerce_store_custom_css .='.lower-header{';
			$vw_ecommerce_store_custom_css .='display:block;';
		$vw_ecommerce_store_custom_css .='} }';
	}else if($vw_ecommerce_store_resp_topbar == false){
		$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_ecommerce_store_custom_css .='.lower-header{';
			$vw_ecommerce_store_custom_css .='display:none;';
		$vw_ecommerce_store_custom_css .='} }';
	}

	$vw_ecommerce_store_resp_stickyheader = get_theme_mod( 'vw_ecommerce_store_stickyheader_hide_show',false);
	if($vw_ecommerce_store_resp_stickyheader == true && get_theme_mod( 'vw_ecommerce_store_sticky_header',false) != true){
    	$vw_ecommerce_store_custom_css .='.header-fixed{';
			$vw_ecommerce_store_custom_css .='position:static;';
		$vw_ecommerce_store_custom_css .='} ';
	}
    if($vw_ecommerce_store_resp_stickyheader == true){
    	$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_ecommerce_store_custom_css .='.header-fixed{';
			$vw_ecommerce_store_custom_css .='position:fixed;';
		$vw_ecommerce_store_custom_css .='} }';
	}else if($vw_ecommerce_store_resp_stickyheader == false){
		$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px){';
		$vw_ecommerce_store_custom_css .='.header-fixed{';
			$vw_ecommerce_store_custom_css .='position:static;';
		$vw_ecommerce_store_custom_css .='} }';
	}

	$vw_ecommerce_store_resp_slider = get_theme_mod( 'vw_ecommerce_store_resp_slider_hide_show',false);
	if($vw_ecommerce_store_resp_slider == true && get_theme_mod( 'vw_ecommerce_store_slider_hide_show', false) == false){
    	$vw_ecommerce_store_custom_css .='#slider{';
			$vw_ecommerce_store_custom_css .='display:none;';
		$vw_ecommerce_store_custom_css .='} ';
	}
    if($vw_ecommerce_store_resp_slider == true){
    	$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_ecommerce_store_custom_css .='#slider{';
			$vw_ecommerce_store_custom_css .='display:block;';
		$vw_ecommerce_store_custom_css .='} }';
	}else if($vw_ecommerce_store_resp_slider == false){
		$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_ecommerce_store_custom_css .='#slider{';
			$vw_ecommerce_store_custom_css .='display:none;';
		$vw_ecommerce_store_custom_css .='} }';
	}

	$vw_ecommerce_store_sidebar = get_theme_mod( 'vw_ecommerce_store_sidebar_hide_show',true);
    if($vw_ecommerce_store_sidebar == true){
    	$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_ecommerce_store_custom_css .='#sidebar{';
			$vw_ecommerce_store_custom_css .='display:block;';
		$vw_ecommerce_store_custom_css .='} }';
	}else if($vw_ecommerce_store_sidebar == false){
		$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_ecommerce_store_custom_css .='#sidebar{';
			$vw_ecommerce_store_custom_css .='display:none;';
		$vw_ecommerce_store_custom_css .='} }';
	}

	$vw_ecommerce_store_resp_scroll_top = get_theme_mod( 'vw_ecommerce_store_resp_scroll_top_hide_show',true);
	if($vw_ecommerce_store_resp_scroll_top == true && get_theme_mod( 'vw_ecommerce_store_hide_show_scroll',true) != true){
    	$vw_ecommerce_store_custom_css .='.scrollup i{';
			$vw_ecommerce_store_custom_css .='visibility:hidden !important;';
		$vw_ecommerce_store_custom_css .='} ';
	}
    if($vw_ecommerce_store_resp_scroll_top == true){
    	$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_ecommerce_store_custom_css .='.scrollup i{';
			$vw_ecommerce_store_custom_css .='visibility:visible !important;';
		$vw_ecommerce_store_custom_css .='} }';
	}else if($vw_ecommerce_store_resp_scroll_top == false){
		$vw_ecommerce_store_custom_css .='@media screen and (max-width:575px){';
		$vw_ecommerce_store_custom_css .='.scrollup i{';
			$vw_ecommerce_store_custom_css .='visibility:hidden !important;';
		$vw_ecommerce_store_custom_css .='} }';
	}

	/*------------- Top Bar Settings ------------------*/

	$vw_ecommerce_store_topbar_padding_top_bottom = get_theme_mod('vw_ecommerce_store_topbar_padding_top_bottom');
	if($vw_ecommerce_store_topbar_padding_top_bottom != false){
		$vw_ecommerce_store_custom_css .='.lower-header{';
			$vw_ecommerce_store_custom_css .='padding-top: '.esc_attr($vw_ecommerce_store_topbar_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_ecommerce_store_topbar_padding_top_bottom).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*-------------- Sticky Header Padding ----------------*/

	$vw_ecommerce_store_sticky_header_padding = get_theme_mod('vw_ecommerce_store_sticky_header_padding');
	if($vw_ecommerce_store_sticky_header_padding != false){
		$vw_ecommerce_store_custom_css .='.header-fixed{';
			$vw_ecommerce_store_custom_css .='padding: '.esc_attr($vw_ecommerce_store_sticky_header_padding).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*------------------ Search Settings -----------------*/
	
	$vw_ecommerce_store_search_padding_top_bottom = get_theme_mod('vw_ecommerce_store_search_padding_top_bottom');
	$vw_ecommerce_store_search_padding_left_right = get_theme_mod('vw_ecommerce_store_search_padding_left_right');
	$vw_ecommerce_store_search_font_size = get_theme_mod('vw_ecommerce_store_search_font_size');
	$vw_ecommerce_store_search_border_radius = get_theme_mod('vw_ecommerce_store_search_border_radius');
	if($vw_ecommerce_store_search_padding_top_bottom != false || $vw_ecommerce_store_search_padding_left_right != false || $vw_ecommerce_store_search_font_size != false || $vw_ecommerce_store_search_border_radius != false){
		$vw_ecommerce_store_custom_css .='.search-box i{';
			$vw_ecommerce_store_custom_css .='padding-top: '.esc_attr($vw_ecommerce_store_search_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_ecommerce_store_search_padding_top_bottom).';padding-left: '.esc_attr($vw_ecommerce_store_search_padding_left_right).';padding-right: '.esc_attr($vw_ecommerce_store_search_padding_left_right).';font-size: '.esc_attr($vw_ecommerce_store_search_font_size).';border-radius: '.esc_attr($vw_ecommerce_store_search_border_radius).'px;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*---------------- Button Settings ------------------*/

	$vw_ecommerce_store_show_button = get_theme_mod( 'vw_ecommerce_store_blog_button_border', false); 
	if($vw_ecommerce_store_show_button == true){ 
		$vw_ecommerce_store_custom_css .='.post-main-box .more-btn a{'; 
		$vw_ecommerce_store_custom_css .='border: 2px solid #222222;margin:10px 0;'; 
		$vw_ecommerce_store_custom_css .='}'; 
		$vw_ecommerce_store_custom_css .='.post-main-box .more-btn{'; 
		$vw_ecommerce_store_custom_css .='margin:0px 0; display:inline-block;'; 
		$vw_ecommerce_store_custom_css .='}'; 
	}

	$vw_ecommerce_store_button_padding_top_bottom = get_theme_mod('vw_ecommerce_store_button_padding_top_bottom');
	$vw_ecommerce_store_button_padding_left_right = get_theme_mod('vw_ecommerce_store_button_padding_left_right');
	if($vw_ecommerce_store_button_padding_top_bottom != false || $vw_ecommerce_store_button_padding_left_right != false){
		$vw_ecommerce_store_custom_css .='.post-main-box .more-btn a{';
			$vw_ecommerce_store_custom_css .='padding-top: '.esc_attr($vw_ecommerce_store_button_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_ecommerce_store_button_padding_top_bottom).';padding-left: '.esc_attr($vw_ecommerce_store_button_padding_left_right).';padding-right: '.esc_attr($vw_ecommerce_store_button_padding_left_right).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_button_border_radius = get_theme_mod('vw_ecommerce_store_button_border_radius');
	if($vw_ecommerce_store_button_border_radius != false){
		$vw_ecommerce_store_custom_css .='.post-main-box .more-btn a{';
			$vw_ecommerce_store_custom_css .='border-radius: '.esc_attr($vw_ecommerce_store_button_border_radius).'px;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*------------- Single Blog Page------------------*/

	$vw_ecommerce_store_single_blog_post_navigation_show_hide = get_theme_mod('vw_ecommerce_store_single_blog_post_navigation_show_hide',true);
	if($vw_ecommerce_store_single_blog_post_navigation_show_hide != true){
		$vw_ecommerce_store_custom_css .='.post-navigation{';
			$vw_ecommerce_store_custom_css .='display: none;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*-------------- Copyright Alignment ----------------*/

	$vw_ecommerce_store_copyright_alingment = get_theme_mod('vw_ecommerce_store_copyright_alingment');
	if($vw_ecommerce_store_copyright_alingment != false){
		$vw_ecommerce_store_custom_css .='.copyright p{';
			$vw_ecommerce_store_custom_css .='text-align: '.esc_attr($vw_ecommerce_store_copyright_alingment).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_copyright_padding_top_bottom = get_theme_mod('vw_ecommerce_store_copyright_padding_top_bottom');
	if($vw_ecommerce_store_copyright_padding_top_bottom != false){
		$vw_ecommerce_store_custom_css .='#footer-2{';
			$vw_ecommerce_store_custom_css .='padding-top: '.esc_attr($vw_ecommerce_store_copyright_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_ecommerce_store_copyright_padding_top_bottom).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*----------------Sroll to top Settings ------------------*/

	$vw_ecommerce_store_scroll_to_top_font_size = get_theme_mod('vw_ecommerce_store_scroll_to_top_font_size');
	if($vw_ecommerce_store_scroll_to_top_font_size != false){
		$vw_ecommerce_store_custom_css .='.scrollup i{';
			$vw_ecommerce_store_custom_css .='font-size: '.esc_attr($vw_ecommerce_store_scroll_to_top_font_size).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_scroll_to_top_padding = get_theme_mod('vw_ecommerce_store_scroll_to_top_padding');
	$vw_ecommerce_store_scroll_to_top_padding = get_theme_mod('vw_ecommerce_store_scroll_to_top_padding');
	if($vw_ecommerce_store_scroll_to_top_padding != false){
		$vw_ecommerce_store_custom_css .='.scrollup i{';
			$vw_ecommerce_store_custom_css .='padding-top: '.esc_attr($vw_ecommerce_store_scroll_to_top_padding).';padding-bottom: '.esc_attr($vw_ecommerce_store_scroll_to_top_padding).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_scroll_to_top_width = get_theme_mod('vw_ecommerce_store_scroll_to_top_width');
	if($vw_ecommerce_store_scroll_to_top_width != false){
		$vw_ecommerce_store_custom_css .='.scrollup i{';
			$vw_ecommerce_store_custom_css .='width: '.esc_attr($vw_ecommerce_store_scroll_to_top_width).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_scroll_to_top_height = get_theme_mod('vw_ecommerce_store_scroll_to_top_height');
	if($vw_ecommerce_store_scroll_to_top_height != false){
		$vw_ecommerce_store_custom_css .='.scrollup i{';
			$vw_ecommerce_store_custom_css .='height: '.esc_attr($vw_ecommerce_store_scroll_to_top_height).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_scroll_to_top_border_radius = get_theme_mod('vw_ecommerce_store_scroll_to_top_border_radius');
	if($vw_ecommerce_store_scroll_to_top_border_radius != false){
		$vw_ecommerce_store_custom_css .='.scrollup i{';
			$vw_ecommerce_store_custom_css .='border-radius: '.esc_attr($vw_ecommerce_store_scroll_to_top_border_radius).'px;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*----------------Social Icons Settings ------------------*/
	$vw_ecommerce_store_social_icon_font_size = get_theme_mod('vw_ecommerce_store_social_icon_font_size');
	if($vw_ecommerce_store_social_icon_font_size != false){
		$vw_ecommerce_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_ecommerce_store_custom_css .='font-size: '.esc_attr($vw_ecommerce_store_social_icon_font_size).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_social_icon_padding = get_theme_mod('vw_ecommerce_store_social_icon_padding');
	if($vw_ecommerce_store_social_icon_padding != false){
		$vw_ecommerce_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_ecommerce_store_custom_css .='padding: '.esc_attr($vw_ecommerce_store_social_icon_padding).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_social_icon_width = get_theme_mod('vw_ecommerce_store_social_icon_width');
	if($vw_ecommerce_store_social_icon_width != false){
		$vw_ecommerce_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_ecommerce_store_custom_css .='width: '.esc_attr($vw_ecommerce_store_social_icon_width).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_social_icon_height = get_theme_mod('vw_ecommerce_store_social_icon_height');
	if($vw_ecommerce_store_social_icon_height != false){
		$vw_ecommerce_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_ecommerce_store_custom_css .='height: '.esc_attr($vw_ecommerce_store_social_icon_height).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_social_icon_border_radius = get_theme_mod('vw_ecommerce_store_social_icon_border_radius');
	if($vw_ecommerce_store_social_icon_border_radius != false){
		$vw_ecommerce_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_ecommerce_store_custom_css .='border-radius: '.esc_attr($vw_ecommerce_store_social_icon_border_radius).'px;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*----------------Woocommerce Products Settings ------------------*/

	$vw_ecommerce_store_products_padding_top_bottom = get_theme_mod('vw_ecommerce_store_products_padding_top_bottom');
	if($vw_ecommerce_store_products_padding_top_bottom != false){
		$vw_ecommerce_store_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_ecommerce_store_custom_css .='padding-top: '.esc_attr($vw_ecommerce_store_products_padding_top_bottom).'!important; padding-bottom: '.esc_attr($vw_ecommerce_store_products_padding_top_bottom).'!important;';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_products_padding_left_right = get_theme_mod('vw_ecommerce_store_products_padding_left_right');
	if($vw_ecommerce_store_products_padding_left_right != false){
		$vw_ecommerce_store_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_ecommerce_store_custom_css .='padding-left: '.esc_attr($vw_ecommerce_store_products_padding_left_right).'!important; padding-right: '.esc_attr($vw_ecommerce_store_products_padding_left_right).'!important;';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_products_box_shadow = get_theme_mod('vw_ecommerce_store_products_box_shadow');
	if($vw_ecommerce_store_products_box_shadow != false){
		$vw_ecommerce_store_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
				$vw_ecommerce_store_custom_css .='box-shadow: '.esc_attr($vw_ecommerce_store_products_box_shadow).'px '.esc_attr($vw_ecommerce_store_products_box_shadow).'px '.esc_attr($vw_ecommerce_store_products_box_shadow).'px #ddd;';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_products_border_radius = get_theme_mod('vw_ecommerce_store_products_border_radius');
	if($vw_ecommerce_store_products_border_radius != false){
		$vw_ecommerce_store_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_ecommerce_store_custom_css .='border-radius: '.esc_attr($vw_ecommerce_store_products_border_radius).'px;';
		$vw_ecommerce_store_custom_css .='}';
	}

	/*------------------ Preloader Background Color  -------------------*/

	$vw_ecommerce_store_preloader_bg_color = get_theme_mod('vw_ecommerce_store_preloader_bg_color');
	if($vw_ecommerce_store_preloader_bg_color != false){
		$vw_ecommerce_store_custom_css .='#preloader{';
			$vw_ecommerce_store_custom_css .='background-color: '.esc_attr($vw_ecommerce_store_preloader_bg_color).';';
		$vw_ecommerce_store_custom_css .='}';
	}

	$vw_ecommerce_store_preloader_border_color = get_theme_mod('vw_ecommerce_store_preloader_border_color');
	if($vw_ecommerce_store_preloader_border_color != false){
		$vw_ecommerce_store_custom_css .='.loader-line{';
			$vw_ecommerce_store_custom_css .='border-color: '.esc_attr($vw_ecommerce_store_preloader_border_color).'!important;';
		$vw_ecommerce_store_custom_css .='}';
	}