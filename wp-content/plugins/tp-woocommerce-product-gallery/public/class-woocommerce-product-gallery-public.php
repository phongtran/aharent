<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.tplugins.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/public
 * @author     TP Plugins <tp.sites.info@gmail.com>
 */
class Woocommerce_Product_Gallery_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Product_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Product_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-product-gallery-public.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name.'-tpslick', plugin_dir_url( __FILE__ ) . 'css/tpslick.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name.'-lity', plugin_dir_url( __FILE__ ) . 'css/lity.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name.'-tpslick-theme', plugin_dir_url( __FILE__ ) . 'css/tpslick-theme.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name.'-lightgallery.min', plugin_dir_url( __FILE__ ) . 'css/lightgallery.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name.'-lg-transitions.min', plugin_dir_url( __FILE__ ) . 'css/lg-transitions.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Product_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Product_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name.'-tpslick', plugin_dir_url( __FILE__ ) . 'js/tpslick.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name.'-lity', plugin_dir_url( __FILE__ ) . 'js/lity.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name.'-jquery.zoom', plugin_dir_url( __FILE__ ) . 'js/jquery.zoom.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name.'-jquery.mousewheel.min', plugin_dir_url( __FILE__ ) . 'js/jquery.mousewheel.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name.'-lightgallery-all.min', plugin_dir_url( __FILE__ ) . 'js/lightgallery-all.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-product-gallery-public.js', array( 'jquery' ), $this->version, false );

	}

	public function remove_gallery_thumbnail_images() {
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
	}

	public function add_gallery_thumbnail_images() {
		add_action( 'woocommerce_before_single_product_summary', array($this,'build_gallery_images'), 20 );
	}

	public function build_gallery_images() {
		global $product;
		$tpwpg_thumbnail = get_option('tpwpg_thumbnail');
		
		
		//---------------------------------------------------------
		$tpwpg_active_lightbox = get_option('tpwpg_active_lightbox');
		
		//$tpwpg_lightbox_mode = get_option('tpwpg_lightbox_mode');
		$tpwpg_active_lightbox_html = '';
		$lightbox_loop = '';
		//---------------------------------------------------------
		$tpwpg_lightbox_product_name = get_option('tpwpg_lightbox_product_name');
		//Zoom Options
		//tpwpg_grab
		$tpwpg_active_zoom = get_option('tpwpg_active_zoom');
		
		if($tpwpg_active_zoom){
			$tpwpg_zoom_class = 'tpwpg_zoom';
			$tpwpg_zoom_txt = 'Hover';
		}
		else{
			$tpwpg_zoom_class = '';
		}

		//wp_dbug($product);
		$product_id = $product->get_id();
		$product_name = $product->get_name();
		$gallery_image_ids = $product->get_gallery_image_ids();
		$image = $product->get_image();
		$image_id = $product->get_image_id();
		$gallery_attachment_ids = $product->get_gallery_image_ids();
		//array_push($gallery_image_ids, $image_id);
		array_unshift($gallery_image_ids, $image_id);
		if($product->is_type('variable')){ //product post has variations
			$available_variations = $product->get_available_variations();
		} //if($product->is_type('variable'))
		else{
			$available_variations = false;
		} //else

		if($gallery_image_ids){
			$img_num = count($gallery_image_ids);

			$html  = '<div class="tpwpg-main images tpwpg-regular tpcol-'.$img_num.'">';
				//$html .= '<section class="tpwpg-regular slider">';
				$sliderfor = '';
				$slidernav = '';
				$i = 1;
				foreach ($gallery_image_ids as $attachment_id) {
					//* *************************** */
					// woocommerce Image Sizes
					// woocommerce_thumbnail         – used in the product ‘grids’ in places such as the shop page.
					// woocommerce_single            – used on single product pages.
					// woocommerce_gallery_thumbnail – used below the main image on the single product page to switch the gallery.
					//* *************************** */
					//wp_dbug(wp_get_attachment_image( $gallery_image_id, 'thumbnail' ));

					$tpwpg_youtube_url = '';

					$attachment_title = get_the_title($attachment_id);

					if($available_variations){
						$data_att_variation = '';
						foreach ($available_variations as $available_variation) {
							if(isset($available_variation['image']['title']) && $attachment_title == $available_variation['image']['title']){
								$attributes = $available_variation['attributes'];
								foreach ($attributes as $key => $value) {
									$data_att_variation .= $value.',';
								}
							} //if(isset($available_variation['image']['title']) && $attachment_title == $available_variation['image']['title'])
						} //foreach ($available_variations as $available_variation)
						$data_att_variation = rtrim($data_att_variation, ",");
						$data_att_variations = 'data-variations="'.$data_att_variation.'"';
					} //if($available_variations)
					else{
						$data_att_variations = '';
					}

					if($tpwpg_active_zoom){
						$image_obj_big = wp_get_attachment_image_src( $attachment_id, 'full' );
					}
					else{
						$image_obj_big = wp_get_attachment_image_src( $attachment_id, 'medium_large' );
					}

					//wp_dbug($image_obj_big);
					$image_src_big = $image_obj_big[0];
					$image_w_big = $image_obj_big[1];
					$image_h_big = $image_obj_big[2];

					$image_obj_small = wp_get_attachment_image_src( $attachment_id, 'woocommerce_thumbnail' );
					//wp_dbug($image_obj);
					$image_src_small = $image_obj_small[0];
					$image_w_small = $image_obj_small[1];
					$image_h_small = $image_obj_small[2];

					$sliderfor .= '<div class="tpwpg-big">';
						$sliderfor .= '<span class="'.$tpwpg_zoom_class.'" id="ex'.$i.'">';
						
							$sliderfor .= '<img src="'.$image_src_big.'" data-imgid="'.$attachment_id.'" '.$data_att_variations.' alt="'.$attachment_title.'" title="'.$attachment_title.'">';

							if($tpwpg_active_zoom){
								$sliderfor .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="100px" height="100px" viewBox="0 0 880.000000 880.000000" preserveAspectRatio="xMidYMid meet">
									<metadata>
									zoom
									</metadata>
									<g transform="translate(0.000000,880.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
									<path d="M3325 8394 c-286 -30 -493 -73 -735 -154 -1375 -454 -2270 -1798 -2160 -3241 48 -619 262 -1183 643 -1689 122 -162 401 -446 557 -566 448 -345 918 -551 1465 -640 198 -32 588 -44 793 -24 760 73 1438 398 1972 946 428 439 712 973 833 1564 165 804 17 1627 -418 2330 -215 347 -566 709 -909 939 -407 273 -823 435 -1321 513 -102 16 -189 21 -405 23 -151 2 -293 1 -315 -1z m560 -758 c560 -82 1029 -323 1425 -732 346 -357 565 -794 651 -1299 26 -155 36 -449 20 -618 -51 -544 -291 -1065 -674 -1463 -621 -646 -1540 -898 -2409 -660 -477 131 -914 415 -1231 801 -361 439 -557 984 -557 1550 0 820 399 1568 1083 2029 335 226 717 362 1137 406 115 12 432 4 555 -14z"/>
									<path d="M6443 3411 c-128 -231 -332 -473 -582 -693 -130 -115 -339 -275 -449 -346 l-63 -41 833 -846 c546 -555 859 -866 908 -902 94 -69 234 -137 325 -159 85 -20 231 -23 305 -5 220 53 463 268 580 512 53 112 73 195 73 304 0 151 -52 300 -153 434 -27 36 -434 450 -904 920 l-855 855 -18 -33z"/>
									</g>
									</svg>';
									
								$sliderfor .= '<p>'.$tpwpg_zoom_txt.'</p>';
							} //if($tpwpg_active_zoom)
						$sliderfor .= '</span>';
						
					$sliderfor .= '</div>';

					$slidernav .= '<div class="tpwpg-thumbnail">';
						$slidernav .= '<img src="'.$image_src_small.'" data-imgid="'.$attachment_id.'" '.$data_att_variations.' alt="'.$attachment_title.'" title="'.$attachment_title.'">';
					$slidernav .= '</div>';
					
					$image_obj_full = wp_get_attachment_image_src( $attachment_id, 'full' );
					$image_src_full = $image_obj_full[0];
					
					if($tpwpg_active_lightbox){

						if($tpwpg_youtube_url){
							$image_src_full = $tpwpg_youtube_url;
						} //if($tpwpg_youtube_url)

						if($tpwpg_lightbox_product_name){
							$data_sub_html = 'data-sub-html="<h4>'.$attachment_title.'</h4>"';
						} //if($tpwpg_lightbox_product_name)
						else{
							$data_sub_html = '';
						} //else

						$lightbox_loop .= '<li id="lightgallery'.$i.'" class="" data-src="'.$image_src_full.'" '.$data_sub_html.'>';
							$lightbox_loop .= '<a href="">';
								$lightbox_loop .= '<img class="img-responsive" src="'.$image_src_small.'">';
							$lightbox_loop .= '</a>';
						$lightbox_loop .= '</li>';
					} //if($tpwpg_active_lightbox)

					$i++;
				}

				$html .= '<div class="slider slider-for">'.$sliderfor.'</div>';

				if($tpwpg_thumbnail){
					$html .= '<div class="slider-nav">'.$slidernav.'</div>';
				} //if($tpwpg_thumbnail)

				if($tpwpg_active_lightbox){
					//data-src="http://www.w3schools.com/"
					//$html .= '<span class="launchGallery" id="launchGallery">Gallery</span>';
					//$html .= '<span id="launchGallery" class="tpwpg_triangle_bottomright"></span>';

					//$html .= '<span id="launchGallery" class="launchGallery"><span class="tpwpg_magnifying_glass"></span></span>';
					$html .= '<span id="launchGallery" class="launchGallery">
								<svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="100pt" height="100pt" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
									<metadata>
									Full Screen
									</metadata>
									<g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
									<path d="M510 8190 l0 -1540 515 0 515 0 0 1025 0 1025 1025 0 1025 0 0 515 0 515 -1540 0 -1540 0 0 -1540z"/>
									<path d="M6650 9215 l0 -515 1025 0 1025 0 0 -1025 0 -1025 515 0 515 0 0 1540 0 1540 -1540 0 -1540 0 0 -515z"/>
									<path d="M510 2050 l0 -1540 1540 0 1540 0 0 515 0 515 -1025 0 -1025 0 0 1025 0 1025 -515 0 -515 0 0 -1540z"/>
									<path d="M8700 2565 l0 -1025 -1025 0 -1025 0 0 -515 0 -515 1540 0 1540 0 0 1540 0 1540 -515 0 -515 0 0 -1025z"/>
									</g>
								</svg>
							  </span>';

					$html .= '<ul id="lightgallery" style="display:none;">'.$lightbox_loop.'</ul>';
				} //if($tpwpg_active_lightbox)

				//$html .= '</section>';
			$html .= '</div>'; //tpwpg-main

			echo $html;
		}

	} //public function build_gallery_images()

	public function gallery_javascript() {
		$tpwpg_thumbnail = get_option('tpwpg_thumbnail');
		
		$tpwpg_dots = get_option('tpwpg_dots');
		$tpwpg_draggable = get_option('tpwpg_draggable');
		$tpwpg_accessibility = get_option('tpwpg_accessibility');

		$tpwpg_arrows = get_option('tpwpg_arrows');
		$tpwpg_centerMode = get_option('tpwpg_centerMode');
		$tpwpg_centerPadding = get_option('tpwpg_centerPadding');
		$tpwpg_infinite = get_option('tpwpg_infinite');
		$tpwpg_fade = get_option('tpwpg_fade');
		$tpwpg_speed = get_option('tpwpg_speed');
		$tpwpg_focusOnSelect = get_option('tpwpg_focusOnSelect');
		$tpwpg_adaptiveHeight = get_option('tpwpg_adaptiveHeight');

		//-----------------------------------------------------------
		$tpwpg_active_lightbox = get_option('tpwpg_active_lightbox');
		$tpwpg_lightbox_mode = get_option('tpwpg_lightbox_mode');
		
		$tpwpg_lightbox_speed = get_option('tpwpg_lightbox_speed');
		$tpwpg_lightbox_hideBarsDelay = get_option('tpwpg_lightbox_hideBarsDelay');
		$tpwpg_lightbox_closable = get_option('tpwpg_lightbox_closable');
		$tpwpg_lightbox_loop = get_option('tpwpg_lightbox_loop');
		$tpwpg_lightbox_mousewheel = get_option('tpwpg_lightbox_mousewheel');

		//-----------------------------------------------------------

		$tpwpg_active_zoom = get_option('tpwpg_active_zoom');

		$tpwpg_dots = ($tpwpg_dots) ? 'true' : 'false';
		$tpwpg_speed = ($tpwpg_speed) ? $tpwpg_speed : 0;

		

		$tpwpg_draggable = ($tpwpg_draggable) ? 'true' : 'false';

		$tpwpg_accessibility = ($tpwpg_accessibility) ? 'true' : 'false';
		$tpwpg_centerMode = ($tpwpg_centerMode) ? 'true' : 'false';
		$tpwpg_infinite = ($tpwpg_infinite) ? 'true' : 'false';
		$tpwpg_fade = ($tpwpg_fade) ? 'true' : 'false';
		$tpwpg_focusOnSelect = ($tpwpg_focusOnSelect) ? 'true' : 'false';

		$tpwpg_lightbox_closable = ($tpwpg_lightbox_closable) ? 'true' : 'false';
		
		$tpwpg_lightbox_loop = ($tpwpg_lightbox_loop) ? 'true' : 'false';
		$tpwpg_lightbox_mousewheel = ($tpwpg_lightbox_mousewheel) ? 'true' : 'false';
		
		?>
			<script>
			
			  jQuery(document).on('ready', function() {

				<?php if($tpwpg_active_lightbox): ?>

					//jQuery('#lightgallery').lightGallery();
					jQuery('#lightgallery').lightGallery({
						mode: '<?php echo $tpwpg_lightbox_mode; ?>',
						thumbnail: false,
						speed: <?php echo $tpwpg_lightbox_speed; ?>,
						hideBarsDelay: <?php echo $tpwpg_lightbox_hideBarsDelay; ?>,
						loop: <?php echo $tpwpg_lightbox_loop; ?>,
						closable: <?php echo $tpwpg_lightbox_closable; ?>,
						mousewheel: <?php echo $tpwpg_lightbox_mousewheel; ?>,
						download: false,
					}); 

					jQuery('#launchGallery').click(function(){
						jQuery('#lightgallery1').trigger('click');    
					})
				<?php endif; //if($tpwpg_active_lightbox) ?>

				<?php if($tpwpg_active_zoom): ?>

					jQuery('.tpwpg_zoom').zoom();

				<?php endif; //if($tpwpg_active_zoom) ?>

				jQuery('.slider-for').tpslick({
					// dots: true,
					speed: <?php echo $tpwpg_speed; ?>,
					slidesToShow: 1,
					slidesToScroll: 1,
					<?php if(is_rtl()): ?>
						rtl: true,
					<?php else: ?>
						rtl: false,
					<?php endif; ?>
					fade: <?php echo $tpwpg_fade; ?>,
					draggable: <?php echo $tpwpg_draggable; ?>,
					<?php if($tpwpg_adaptiveHeight): ?>
						adaptiveHeight: true,
					<?php endif; ?>
					<?php if($tpwpg_thumbnail): ?>
						asNavFor: '.slider-nav',
						arrows: false,
					<?php else: ?>
						arrows: true,
						dots: <?php echo $tpwpg_dots; ?>,
					<?php endif; ?>
				});

				<?php if($tpwpg_thumbnail): ?>
				jQuery('.slider-nav').tpslick({
					slidesToShow: 4,
					slidesToScroll: 1,
					infinite: <?php echo $tpwpg_infinite; ?>,
					<?php if(is_rtl()): ?>
						<?php if($tpwpg_vertical == 'true'): ?>
							rtl: false,
						<?php else: ?>
							rtl: true,
						<?php endif; ?>
					<?php else: ?>
						rtl: false,
					<?php endif; ?>
					asNavFor: '.slider-for',
					dots: <?php echo $tpwpg_dots; ?>,
					draggable: <?php echo $tpwpg_draggable; ?>,
					centerMode: <?php echo $tpwpg_centerMode; ?>,
					focusOnSelect: <?php echo $tpwpg_focusOnSelect; ?>,
				});
				<?php endif; //if($tpwpg_thumbnail) ?>


				if(jQuery(".tpslick-vertical").length){
					var vertical_width = jQuery(".tpslick-vertical").width();

					//console.log(vertical_width);

					<?php if(is_rtl()): ?>
						jQuery(".onsale").css({"right": vertical_width+"px","top": "5px", "margin": "0 5px"});
					<?php else: ?>
						jQuery(".onsale").css({"left": vertical_width+"px","top": "5px", "margin": "0 5px"});
					<?php endif; ?>
				} //if(jQuery(".tpslick-vertical").length)

			});
			</script>
		<?php
	}

	public function gallery_style() {
		$tpwpg_arrow_background = get_option('tpwpg_arrow_background');
		$tpwpg_arrow_color = get_option('tpwpg_arrow_color');
		$tpwpg_icons_background = get_option('tpwpg_icons_background');
		$tpwpg_icons_color = get_option('tpwpg_icons_color');
		$tpwpg_disable_thumbnail_less_than = get_option('tpwpg_disable_thumbnail_less_than');
		$tpwpg_active_zoom = get_option('tpwpg_active_zoom');

		if(is_rtl()){
			$float = 'right';
			$float_lang = 'left';
			$prev = 'next';
			$next = 'prev';
			$border_left = '2px solid transparent';
			$border_right = 'none';
		} //if(is_rtl())
		else{
			$float = 'left';
			$float_lang = 'right';
			$prev = 'prev';
			$next = 'next';
			$border_left = 'none';
			$border_right = '2px solid transparent';
		} //else

		$tpwpg_arrow_background = ($tpwpg_arrow_background) ? $tpwpg_arrow_background : 'none';
		$tpwpg_icons_background = ($tpwpg_icons_background) ? $tpwpg_icons_background : 'none';
		?>
			<style>
				@media (min-width: 768px){
					.tpwpg-main{
						float: <?php echo $float; ?>;
					}
				}
				.tpwpg-vertical .slider-nav{
					float: <?php echo $float; ?>;
				}
				.tpwpg-vertical .slider-for{
					float: <?php echo $float_lang; ?>;
				}
				.tpslick-vertical .tpslick-slide {
					border-right: <?php echo $border_right; ?>;
					border-left: <?php echo $border_left; ?>;
				}
				.launchGallery svg,
				.tpwpg_zoom svg{
					background:<?php echo $tpwpg_icons_background; ?> !important;
				}
				.tpslick-prev,
				.tpslick-next{
					background:<?php echo $tpwpg_arrow_background; ?> !important;
				}
				.tpslick-prev:hover, .tpslick-prev:focus,
				.tpslick-next:hover, .tpslick-next:focus{
					background:<?php echo $tpwpg_arrow_background; ?> !important;
					opacity: 0.5;
				}

				.tpwpg-vertical .tpslick-vertical .tpslick-prev svg{
					-webkit-transform: rotate(0deg);
					-moz-transform: rotate(0deg);
					-ms-transform: rotate(0deg);
					-o-transform: rotate(0deg);
				}

				.tpwpg-vertical .tpslick-vertical .tpslick-next svg{
					-webkit-transform: rotate(180deg);
					-moz-transform: rotate(180deg);
					-ms-transform: rotate(180deg);
					-o-transform: rotate(180deg);
				}

				.tpwpg-big p {
					<?php echo $float_lang; ?>: 38px;
					<?php echo $float; ?>: auto;
				}
				.tpwpg_zoom svg {
					<?php echo $float_lang; ?>: 5px;
					<?php echo $float; ?>: auto;
				}
				.launchGallery {
					<?php echo $float_lang; ?>: 4px;
					<?php echo $float; ?>: auto;
					<?php if(!$tpwpg_active_zoom): ?>
						top:5px;
					<?php endif; ?>
				}
				.tpslick-<?php echo $prev; ?> svg{
					-webkit-transform: rotate(90deg);
					-moz-transform: rotate(90deg);
					-ms-transform: rotate(90deg);
					-o-transform: rotate(90deg);
					/* filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=-2); */
				}

				.tpslick-<?php echo $next; ?> svg{
					-webkit-transform: rotate(-90deg);
					-moz-transform: rotate(-90deg);
					-ms-transform: rotate(-90deg);
					-o-transform: rotate(-90deg);
					/* filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=-2); */
				}

				.tpslick-prev svg g, .tpslick-next svg g{
					fill: <?php echo $tpwpg_arrow_color; ?>;
				}
				.launchGallery svg g,
				.tpwpg_zoom svg g{
					fill: <?php echo $tpwpg_icons_color; ?>;
				}
				.tpcol-<?php echo $tpwpg_disable_thumbnail_less_than; ?> .slider-nav{
					display: none;
				}
			</style>
		<?php
	}


	//------------------------------------------------------------------

	
}
