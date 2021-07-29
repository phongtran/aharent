<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.tplugins.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/admin
 * @author     TP Plugins <tp.sites.info@gmail.com>
 */
class Woocommerce_Product_Gallery_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-product-gallery-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-minicolors', plugin_dir_url( __FILE__ ) . 'css/jquery.minicolors.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name.'-minicolors', plugin_dir_url( __FILE__ ) . 'js/jquery.minicolors.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-product-gallery-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function tpwpg_register_options_page() {
		//add_options_page('Page Title', 'Woocommerce Product Gallery', 'manage_options', 'tpwpg_settings', array($this,'tpwpg_plugin_options_page'));
		add_menu_page( 'TP Woocommerce Product Gallery', 'TP Woocommerce Product Gallery', 'manage_options', 'tpwpg_settings', array( $this, 'tpwpg_plugin_options_page' ), 'dashicons-admin-tp', 110 );
	}
	//add_action('admin_menu', 'myplugin_register_options_page');

	
	public function tpwpg_plugin_options_page()	{

		//-------------------------------------- Settings --------------------------------------
		
		$tpwpg_thumbnail = get_option('tpwpg_thumbnail');
		
		$tpwpg_dots = get_option('tpwpg_dots');
		$tpwpg_speed = get_option('tpwpg_speed');
		$tpwpg_accessibility = get_option('tpwpg_accessibility');
		
		$tpwpg_autoplay = get_option('tpwpg_autoplay');
		$tpwpg_autoplaySpeed = get_option('tpwpg_autoplaySpeed');
		$tpwpg_arrows = get_option('tpwpg_arrows');
		$tpwpg_centerMode = get_option('tpwpg_centerMode');
		$tpwpg_centerPadding = get_option('tpwpg_centerPadding');
		$tpwpg_infinite = get_option('tpwpg_infinite');
		$tpwpg_draggable = get_option('tpwpg_draggable');
		$tpwpg_fade = get_option('tpwpg_fade');
		$tpwpg_focusOnSelect = get_option('tpwpg_focusOnSelect');

		$tpwpg_adaptiveHeight = get_option('tpwpg_adaptiveHeight');
		
		$tpwpg_thumbnail_check = ($tpwpg_thumbnail) ? 'checked="checked"' : '';
		$tpwpg_dots_check = ($tpwpg_dots) ? 'checked="checked"' : '';
		$tpwpg_accessibility_check = ($tpwpg_accessibility) ? 'checked="checked"' : '';
		
		$tpwpg_autoplay_check = ($tpwpg_autoplay) ? 'checked="checked"' : '';
		$tpwpg_arrows_check = ($tpwpg_arrows) ? 'checked="checked"' : '';
		$tpwpg_centerMode_check = ($tpwpg_centerMode) ? 'checked="checked"' : '';
		$tpwpg_infinite_check = ($tpwpg_infinite) ? 'checked="checked"' : '';
		$tpwpg_draggable_check = ($tpwpg_draggable) ? 'checked="checked"' : '';
		$tpwpg_fade_check = ($tpwpg_fade) ? 'checked="checked"' : '';
		$tpwpg_focusOnSelect_check = ($tpwpg_focusOnSelect) ? 'checked="checked"' : '';
		$tpwpg_adaptiveHeight_check = ($tpwpg_adaptiveHeight) ? 'checked="checked"' : '';

		//-------------------------------------- Style ---------------------------------
		$tpwpg_arrow_background = get_option('tpwpg_arrow_background');
		$tpwpg_arrow_color = get_option('tpwpg_arrow_color');

		$tpwpg_icons_background = get_option('tpwpg_icons_background');
		$tpwpg_icons_color = get_option('tpwpg_icons_color');

		$tpwpg_arrow_background = ($tpwpg_arrow_background) ? $tpwpg_arrow_background : 'none';
		$tpwpg_arrow_color = ($tpwpg_arrow_color) ? $tpwpg_arrow_color : '#ffffff';

		$tpwpg_icons_background = ($tpwpg_icons_background) ? $tpwpg_icons_background : 'none';

		// wp_dbug($tpwpg_arrow_background);
		//--------------------------------------  --------------------------------------

		//-------------------------------------- Video ---------------------------------
		$tpwpg_video_type = get_option('tpwpg_video_type');
		$tpwpg_video_play_background = get_option('tpwpg_video_play_background');
		//$tpwpg_arrow_color = get_option('tpwpg_arrow_color');

		$tpwpg_video_play_background = ($tpwpg_video_play_background) ? $tpwpg_video_play_background : '#DC4539';

		// wp_dbug($tpwpg_arrow_background);
		//--------------------------------------  --------------------------------------

		//-------------------------------------- Zoom --------------------------------------
		$tpwpg_active_zoom = get_option('tpwpg_active_zoom');
		$tpwpg_zoom_type = get_option('tpwpg_zoom_type');

		$tpwpg_active_zoom_check = ($tpwpg_active_zoom) ? 'checked="checked"' : '';

		//-------------------------------------- Lightbox --------------------------------------
		$tpwpg_active_lightbox = get_option('tpwpg_active_lightbox');
		$tpwpg_active_lightbox_check = ($tpwpg_active_lightbox) ? 'checked="checked"' : '';
		$tpwpg_lightbox_mode = get_option('tpwpg_lightbox_mode');
		$tpwpg_imageSize = '';
		$tpwpg_lightbox_speed = get_option('tpwpg_lightbox_speed');
		$tpwpg_lightbox_hideBarsDelay = get_option('tpwpg_lightbox_hideBarsDelay');
		$tpwpg_lightbox_closable = get_option('tpwpg_lightbox_closable');
		$tpwpg_lightbox_closable_check = ($tpwpg_lightbox_closable) ? 'checked="checked"' : '';
		$tpwpg_lightbox_loop = get_option('tpwpg_lightbox_loop');
		$tpwpg_lightbox_loop_check = ($tpwpg_lightbox_loop) ? 'checked="checked"' : '';
		$tpwpg_lightbox_mousewheel = get_option('tpwpg_lightbox_mousewheel');
		$tpwpg_lightbox_mousewheel_check = ($tpwpg_lightbox_mousewheel) ? 'checked="checked"' : '';
		$tpwpg_lightbox_product_name = get_option('tpwpg_lightbox_product_name');
		$tpwpg_lightbox_product_name_check = ($tpwpg_lightbox_product_name) ? 'checked="checked"' : '';
		$tpwpg_lightbox_loadYoutubeThumbnail = get_option('tpwpg_lightbox_loadYoutubeThumbnail');
		$tpwpg_lightbox_loadYoutubeThumbnail_check = ($tpwpg_lightbox_loadYoutubeThumbnail) ? 'checked="checked"' : '';
		
	?>
		<div class='wrap tpwpg-warp'>
			<?php screen_icon(); ?>
			<h2>TP Woocommerce Product Gallery Settings</h2>
			<form method="post" action="options.php">
				<?php //wp_nonce_field('update-options') ?>
				<?php wp_nonce_field('update-options') ?>

				<nav id="tpwpg-tab-nav">
					<span class="tabnav" data-sort="1">Settings</span>
					<span class="tabnav" data-sort="2">Style</span>
					<span class="tabnav" data-sort="3">Video (PRO)</span>
					<span class="tabnav" data-sort="4">Zoom</span>
					<span class="tabnav" data-sort="5">Lightbox</span>
					<span class="tabnav" data-sort="6">License (Get PRO)</span>
				</nav>

				<div id="tpwpg-tab-contents">
					
					<div class="tabtxt tps_admin_section" data-sort="1">

						<div class="tpwpg_admin_settings_left">

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">Display Thumbnail
									<input type="checkbox" name="tpwpg_thumbnail" value="1" <?php echo $tpwpg_thumbnail_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Show Thumbnail</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>
								<label class="tpwpg-container-text">Hide thumbnail if have 1 image</label>
								<select name="tpwpg_disabled_thumbnail_less_than">
									<option value="0" disabled>always display thumbnail</option>
									<option value="1" disabled>hide thumbnail if have 1 image</option>
								</select>

								<span class="tpwpg_admin_settings_desc">hide thumbnail if have 1 image</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>
								<label class="tpwpg-container-text">SlidesToShow</label>
								<select name="tpwpg_slidesToShow">
									<option value="3" disabled>3</option>
									<option value="4" disabled>4</option>
									<option value="5" disabled>5</option>
								</select>

								<span class="tpwpg_admin_settings_desc">Thumbnails number</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">Display Dots
									<input type="checkbox" name="tpwpg_dots" value="1" <?php echo $tpwpg_dots_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Show dot indicators</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">Draggable
									<input type="checkbox" name="tpwpg_draggable" value="1" <?php echo $tpwpg_draggable_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Enable mouse dragging</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">Fade
									<input type="checkbox" name="tpwpg_fade" value="1" <?php echo $tpwpg_fade_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Enable mouse dragging</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">FocusOnSelect
									<input type="checkbox" name="tpwpg_focusOnSelect" value="1" <?php echo $tpwpg_focusOnSelect_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Enable focus on selected element (click)</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">Accessibility
									<input type="checkbox" name="tpwpg_accessibility" value="1" <?php echo $tpwpg_accessibility_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Enables tabbing and arrow key navigation</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-text">Speed
									<input type="number" name="tpwpg_speed" value="<?php echo $tpwpg_speed; ?>" />
								</label>

								<span class="tpwpg_admin_settings_desc">Slide/Fade animation speed</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-checkbox">AdaptiveHeight
									<input type="checkbox" name="tpwpg_adaptiveHeight" value="1" <?php echo $tpwpg_adaptiveHeight_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Enables adaptive height for single slide horizontal carousels</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>

								<div class="tpwpg_admin_settings_row_2">
									<label class="tpwpg-container-checkbox">Autoplay
										<input type="checkbox" name="tpwpg_autoplay" value="1" disabled>
										<span class="checkmark"></span>
									</label>
								</div>

								<div class="tpwpg_admin_settings_row_2">
									<label class="tpwpg-container-text">AutoplaySpeed</label>
									<input type="number" name="tpwpg_autoplaySpeed" value="3000" disabled />
									<span class="tpwpg_admin_settings_desc">Autoplay Speed in milliseconds</span>
								</div>
								
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">Arrows
									<input type="checkbox" name="tpwpg_arrows" value="1" <?php echo $tpwpg_arrows_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Prev/Next Arrows</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<div class="tpwpg_admin_settings_row_2">
									<label class="tpwpg-container-checkbox">CenterMode
										<input type="checkbox" name="tpwpg_centerMode" value="1" <?php echo $tpwpg_centerMode_check; ?>>
										<span class="checkmark"></span>
									</label>
									<span class="tpwpg_admin_settings_desc">Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts</span>
								</div>

								<div class="tpwpg_admin_settings_row_2">
									<label class="tpwpg-container-text">CenterPadding</label>
									<input type="text" name="tpwpg_centerPadding" value="<?php echo $tpwpg_centerPadding; ?>" />
									<span class="tpwpg_admin_settings_desc">Side padding when in center mode (px or %)</span>
								</div>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">Infinite
									<input type="checkbox" name="tpwpg_infinite" value="1" <?php echo $tpwpg_infinite_check; ?>>
									<span class="checkmark"></span>
								</label>

								<span class="tpwpg_admin_settings_desc">Infinite loop sliding</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>

								<div class="tpwpg_admin_settings_row_2">
									<label class="tpwpg-container-checkbox">Vertical
										<input type="checkbox" name="tpwpg_vertical" value="1" disabled>
										<span class="checkmark"></span>
									</label>

									<span class="tpwpg_admin_settings_desc">Vertical slide mode</span>
								</div><!-- tpwpg_admin_settings_row_2 -->

								<div class="tpwpg_admin_settings_row_2">
									<label class="tpwpg-container-checkbox">VerticalSwiping
										<input type="checkbox" name="tpwpg_verticalSwiping" value="1" disabled>
										<span class="checkmark"></span>
									</label>

									<span class="tpwpg_admin_settings_desc">Vertical swipe mode</span>
								</div><!-- tpwpg_admin_settings_row_2 -->

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>
								<label class="tpwpg-container-text">Image size</label>
								<?php echo $this->tpwpg_add_image_size_field($tpwpg_imageSize); ?>
								<span class="tpwpg_admin_settings_desc">Select you image size</span>

							</div><!-- tpwpg_admin_settings_row -->

						</div><!-- tpwpg_admin_settings_left -->

						<div class="tpwpg_admin_settings_right">
						</div><!-- tpwpg_admin_settings_right -->

					</div><!-- tps_admin_section -->

					<div class="tabtxt tps_admin_section" data-sort="2">

						<div class="tpwpg_admin_settings_left">

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-text">Button arrow background</label>
								<input type="text" class="tp_colorpiker" name="tpwpg_arrow_background" value="<?php echo $tpwpg_arrow_background; ?>" autocomplete="off">

								<span class="tpwpg_admin_settings_desc">Next / Prev background</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-text">Button arrow color</label>
								<input type="text" class="tp_colorpiker" name="tpwpg_arrow_color" value="<?php echo $tpwpg_arrow_color; ?>" autocomplete="off">

								<span class="tpwpg_admin_settings_desc">Next / Prev color</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-text">Icons background</label>
								<input type="text" class="tp_colorpiker" name="tpwpg_icons_background" value="<?php echo $tpwpg_icons_background; ?>" autocomplete="off">

								<span class="tpwpg_admin_settings_desc">zoom and fullscreen</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-text">Icons color</label>
								<input type="text" class="tp_colorpiker" name="tpwpg_icons_color" value="<?php echo $tpwpg_icons_color; ?>" autocomplete="off">

								<span class="tpwpg_admin_settings_desc">zoom and fullscreen</span>

							</div><!-- tpwpg_admin_settings_row -->

						</div><!-- tpwpg_admin_settings_left -->

					</div><!-- tps_admin_section -->

					<div class="tabtxt tps_admin_section" data-sort="3">

						<div class="tpwpg_admin_settings_left">

							<div class="tpwpg_admin_settings_row">
								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>
								<label class="tpwpg-container-text">Video Open Type</label>
								<select name="tpwpg_video_type">
									<option value="1" disabled>Click on Play Button and the video will popup</option>
									<option value="2" disabled>Click on Play Button and the video will play directly</option>
								</select>
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>
								<label class="tpwpg-container-text">Button Play background</label>
								<input type="text" class="tp_colorpiker" name="tpwpg_video_play_background" value="#dc4539" autocomplete="off" disabled>

								<!-- <span class="tpwpg_admin_settings_desc">Next / Prev background</span> -->

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>
								<label class="tpwpg-container-checkbox">LoadYoutubeThumbnail (on Lightbox)
									<input type="checkbox" name="tpwpg_lightbox_loadYoutubeThumbnail" value="1" <?php echo $tpwpg_lightbox_loadYoutubeThumbnail_check; ?>>
									<span class="checkmark"></span>
								</label>
								<span class="tpwpg_admin_settings_desc">You can automatically load thumbnails for youtube videos from youtube by setting loadYoutubeThumbnail true</span>
							</div><!-- tpwpg_admin_settings_row -->

						</div><!-- tpwpg_admin_settings_left -->

					</div><!-- tps_admin_section -->

					<div class="tabtxt tps_admin_section" data-sort="4">

						<div class="tpwpg_admin_settings_left">

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-checkbox">Active Zoom on image over
									<input type="checkbox" name="tpwpg_active_zoom" value="1" <?php echo $tpwpg_active_zoom_check; ?>>
									<span class="checkmark"></span>
								</label>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-text">Zoom Type</label>
								<select name="tpwpg_zoom_type">
									<option value="1" selected="selected">Hover</option>
									<option value="6" disabled>Grab (PRO)</option>
									<option value="7" disabled>Click to activate (PRO)</option>
									<option value="8" disabled>Click to toggle (PRO)</option>
								</select>
							</div><!-- tpwpg_admin_settings_row -->

						</div><!-- tpwpg_admin_settings_left -->

					</div><!-- tps_admin_section -->

					<div class="tabtxt tps_admin_section" data-sort="5">

						<div class="tpwpg_admin_settings_left">

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-checkbox">Active Lightbox
									<input type="checkbox" name="tpwpg_active_lightbox" value="1" <?php echo $tpwpg_active_lightbox_check; ?>>
									<span class="checkmark"></span>
								</label>
								
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<div class="tpwpg_triangle_topright_box"><div class="tpwpg_triangle_topright"><span>PRO</span></div></div>
								<label class="tpwpg-container-checkbox">Display Thumbnials
									<input type="checkbox" name="tpwpg_lightbox_thumbnials" value="1" disabled>
									<span class="checkmark"></span>
								</label>
								<span class="tpwpg_admin_settings_desc">Enable thumbnails for the gallery</span>
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">

								<label class="tpwpg-container-text">Mode</label>
								<select id="select-trans" class="select-trans" name="tpwpg_lightbox_mode">
									<option selected="selected" value="lg-slide">lg-slide</option>
									<option value="lg-fade">lg-fade</option>
									<option value="lg-zoom-in">lg-zoom-in</option>
									
									<option value="tppg-lg-zoom-in-big-pro" disabled>lg-zoom-in-big (PRO)</option> 
									<option value="tppg-lg-zoom-out-pro" disabled>lg-zoom-out (PRO)</option> 
									<option value="tppg-lg-zoom-out-big-pro" disabled>lg-zoom-out-big (PRO)</option> 
									<option value="tppg-lg-zoom-out-in-pro" disabled>lg-zoom-out-in (PRO)</option> 
									<option value="tppg-lg-zoom-in-out-pro" disabled>lg-zoom-in-out (PRO)</option> 
									<option value="tppg-lg-soft-zoom-pro" disabled>lg-soft-zoom (PRO)</option> 
									<option value="tppg-lg-scale-up-pro" disabled>lg-scale-up (PRO)</option> 
									<option value="tppg-lg-slide-circular-pro" disabled>lg-slide-circular (PRO)</option> 
									<option value="tppg-lg-slide-circular-vertical-pro" disabled>lg-slide-circular-vertical (PRO)</option> 
									<option value="tppg-lg-slide-vertical-pro" disabled>lg-slide-vertical (PRO)</option> 
									<option value="tppg-lg-slide-vertical-growth-pro" disabled>lg-slide-vertical-growth (PRO)</option> 
									<option value="tppg-lg-slide-skew-only-pro" disabled>lg-slide-skew-only (PRO)</option> 
									<option value="tppg-lg-slide-skew-only-rev-pro" disabled>lg-slide-skew-only-rev (PRO)</option> 
									<option value="tppg-lg-slide-skew-only-y-pro" disabled>lg-slide-skew-only-y (PRO)</option> 
									<option value="tppg-lg-slide-skew-only-y-rev-pro" disabled>lg-slide-skew-only-y-rev (PRO)</option> 
									<option value="tppg-lg-slide-skew-pro" disabled>lg-slide-skew (PRO)</option> 
									<option value="tppg-lg-slide-skew-rev-pro" disabled>lg-slide-skew-rev (PRO)</option> 
									<option value="tppg-lg-slide-skew-cross-pro" disabled>lg-slide-skew-cross (PRO)</option> 
									<option value="tppg-lg-slide-skew-cross-rev-pro" disabled>lg-slide-skew-cross-rev (PRO)</option> 
									<option value="tppg-lg-slide-skew-ver-pro" disabled>lg-slide-skew-ver (PRO)</option> 
									<option value="tppg-lg-slide-skew-ver-rev-pro" disabled>lg-slide-skew-ver-rev (PRO)</option> 
									<option value="tppg-lg-slide-skew-ver-cross-pro" disabled>lg-slide-skew-ver-cross (PRO)</option> 
									<option value="tppg-lg-slide-skew-ver-cross-rev-pro" disabled>lg-slide-skew-ver-cross-rev (PRO)</option> 
									<option value="tppg-lg-lollipop-pro" disabled>lg-lollipop (PRO)</option> 
									<option value="tppg-lg-lollipop-rev-pro" disabled>lg-lollipop-rev (PRO)</option> 
									<option value="tppg-lg-rotate-pro" disabled>lg-rotate (PRO)</option> 
									<option value="tppg-lg-rotate-rev-pro" disabled>lg-rotate-rev (PRO)</option> 
									<option value="tppg-lg-tube-pro" disabled>lg-tube (PRO)</option>
								</select>
								
								<span class="tpwpg_admin_settings_desc">Type of transition between images</span>

							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-text">Speed</label>
								<input type="number" name="tpwpg_lightbox_speed" value="<?php echo $tpwpg_lightbox_speed; ?>">
								<span class="tpwpg_admin_settings_desc">Transition duration (in ms)</span>
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-text">HideBarsDelay</label>
								<input type="number" name="tpwpg_lightbox_hideBarsDelay" value="6000">
								<span class="tpwpg_admin_settings_desc">Delay for hiding gallery controls in ms</span>
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-checkbox">Closable
									<input type="checkbox" name="tpwpg_lightbox_closable" value="1" <?php echo $tpwpg_lightbox_closable_check; ?>>
									<span class="checkmark"></span>
								</label>
								<span class="tpwpg_admin_settings_desc">Allows clicks on dimmer to close gallery</span>
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-checkbox">Loop
									<input type="checkbox" name="tpwpg_lightbox_loop" value="1" <?php echo $tpwpg_lightbox_loop_check; ?>>
									<span class="checkmark"></span>
								</label>
								<span class="tpwpg_admin_settings_desc">If false, will disabled the ability to loop back to the beginning of the gallery when on the last element</span>
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-checkbox">Mousewheel
									<input type="checkbox" name="tpwpg_lightbox_mousewheel" value="1" <?php echo $tpwpg_lightbox_mousewheel_check; ?>>
									<span class="checkmark"></span>
								</label>
								<span class="tpwpg_admin_settings_desc">Change slide on mousewheel</span>
							</div><!-- tpwpg_admin_settings_row -->

							<div class="tpwpg_admin_settings_row">
								<label class="tpwpg-container-checkbox">Display image attachment title
									<input type="checkbox" name="tpwpg_lightbox_product_name" value="1" <?php echo $tpwpg_lightbox_product_name_check; ?>>
									<span class="checkmark"></span>
								</label>
								<span class="tpwpg_admin_settings_desc">If false, will disabled the image attachment title on image</span>
							</div><!-- tpwpg_admin_settings_row -->

						</div><!-- tpwpg_admin_settings_left -->

					</div><!-- tps_admin_section -->

					<div class="tabtxt tps_admin_section" data-sort="6">
						<h2>Free Version</h2>
						<a href="<?php echo TPWPG_PLUGIN_HOME.'product/'.TPWPG_PLUGIN_PRO_SLUG; ?>" target="_blank">Upgrade from the FREE version to the PRO version</a>
					</div><!-- tps_admin_section -->

				</div><!-- tpwpg-tab-contents -->

				<input type="submit" name="Submit" value="Update Options" class="tps-gcf-submit" />
				<input type="hidden" name="action" value="update" />
            	<input type="hidden" name="page_options" value="tpwpg_adaptiveHeight,tpwpg_thumbnail,tpwpg_dots,tpwpg_accessibility,tpwpg_draggable,tpwpg_speed,tpwpg_fade,tpwpg_focusOnSelect,tpwpg_arrows,tpwpg_centerMode,tpwpg_centerPadding,tpwpg_infinite,tpwpg_arrow_background,tpwpg_arrow_color,tpwpg_icons_background,tpwpg_icons_color,tpwpg_active_zoom,tpwpg_active_lightbox,tpwpg_lightbox_speed,tpwpg_lightbox_hideBarsDelay,tpwpg_lightbox_mode,tpwpg_lightbox_closable,tpwpg_lightbox_loop,tpwpg_lightbox_mousewheel,tpwpg_lightbox_product_name,tpwpg_lightbox_loadYoutubeThumbnail" />
			</form>

			<script>

            var tab = {
                nav : null, // holds all tabs
                txt : null, // holds all text containers
                init : function () {
                // tab.init() : init tab interface
            
                // Get all tabs + contents
                tab.nav = document.getElementById("tpwpg-tab-nav").getElementsByClassName("tabnav");
                tab.txt = document.getElementById("tpwpg-tab-contents").getElementsByClassName("tabtxt");
            
                // Error checking
                if (tab.nav.length==0 || tab.txt.length==0 || tab.nav.length!=tab.txt.length) {
                    console.log("ERROR STARTING TABS");
                } else {
                    // Attach onclick events to navigation tabs
                    for (var i=0; i<tab.nav.length; i++) {
                    tab.nav[i].dataset.pos = i;
                    tab.nav[i].addEventListener("click", tab.switch);
                    }
            
                    // Default - show first tab
                    tab.nav[0].classList.add("active");
                    tab.txt[0].classList.add("active");
                }
                },
            
                switch : function () {
                // tab.switch() : change to another tab
            
                // Hide all tabs & text
                for (var t of tab.nav) {
                    t.classList.remove("active");
                }
                for (var t of tab.txt) {
                    t.classList.remove("active");
                }
            
                // Set current tab
                tab.nav[this.dataset.pos].classList.add("active");
                tab.txt[this.dataset.pos].classList.add("active");
                }
            };
            
            window.addEventListener("load", tab.init);

        	</script>

		</div><!-- wrap tpwpg-warp -->
	<?php
	} //public function tpwpg_plugin_options_page()

	public function tpwpg_add_image_size_field($db_value){

		$field = array(
			'id'      => 'tpwpg_imageSize',
			'label'   => 'Image Size',
			'type'    => 'select',
			'options' => get_intermediate_image_sizes()
		);

		$input = sprintf(
			'<select id="%s" name="%s">',
			$field['id'],
			$field['id']
		);
		foreach ( $field['options'] as $key => $value ) {
			$field_value = !is_numeric( $key ) ? $key : $value;
			$input .= sprintf(
				'<option %s value="%s" disabled>%s</option>',
				$db_value === $field_value ? 'selected' : '',
				$field_value,
				$value
			);
		}
		$input .= '</select>';

		return $input;
	}

	public function tpwpg_settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=tpwpg_settings">Settings</a>';
		$pro_link = '<a href="'.TPWPG_PLUGIN_HOME.'product/'.TPWPG_PLUGIN_PRO_SLUG.'" class="tpc_get_pro" target="_blank">Go Premium!</a>';
		array_push( $links, $settings_link, $pro_link );
		return $links;
	} //function tpwpg_settings_link( $links )

	public function tpwpg_get_pro_link( $links, $file ) {

		if ( TPWPG_PLUGIN_BASENAME == $file ) {
	
			$row_meta = array(
				'docs' => '<a href="' . esc_url( 'https://www.tplugins.com/demos/product/v-neck-t-shirt/' ) . '" target="_blank" aria-label="' . esc_attr__( 'Live Demo', 'wtppcs' ) . '" class="tpc_live_demo">&#128073; ' . esc_html__( 'Live Demo', 'wtppcs' ) . '</a>'
			);
	
			return array_merge( $links, $row_meta );
		}
		
		return (array) $links;
	} //function tppc_get_pro_link( $links, $file )

}