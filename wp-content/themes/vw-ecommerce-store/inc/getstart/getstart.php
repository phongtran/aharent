<?php
//about theme info
add_action( 'admin_menu', 'vw_ecommerce_store_gettingstarted' );
function vw_ecommerce_store_gettingstarted() {    	
	add_theme_page( esc_html__('About VW Ecommerce Store', 'vw-ecommerce-store'), esc_html__('About VW Ecommerce Store', 'vw-ecommerce-store'), 'edit_theme_options', 'vw_ecommerce_store_guide', 'vw_ecommerce_store_mostrar_guide');   
}

// Add a Custom CSS file to WP Admin Area
function vw_ecommerce_store_admin_theme_style() {
   wp_enqueue_style('vw-ecommerce-store-custom-admin-style', esc_url(get_template_directory_uri()) . '/inc/getstart/getstart.css');
   wp_enqueue_script('vw-ecommerce-store-tabs', esc_url(get_template_directory_uri()) . '/inc/getstart/js/tab.js');
   wp_enqueue_style( 'font-awesome-css', esc_url(get_template_directory_uri()).'/assets/css/fontawesome-all.css' );

}
add_action('admin_enqueue_scripts', 'vw_ecommerce_store_admin_theme_style');

//guidline for about theme
function vw_ecommerce_store_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( 'vw-ecommerce-store' );
?>

<div class="wrapper-info">
    <div class="col-left">
    	<h2><?php esc_html_e( 'Welcome to VW Ecommerce Store Theme', 'vw-ecommerce-store' ); ?> <span class="version">Version: <?php echo esc_html($theme['Version']);?></span></h2>
    	<p><?php esc_html_e('All our WordPress themes are modern, minimalist, 100% responsive, seo-friendly,feature-rich, and multipurpose that best suit designers, bloggers and other professionals who are working in the creative fields.','vw-ecommerce-store'); ?></p>
    </div>
    <div class="col-right">
    	<div class="logo">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/final-logo.png" alt="" />
		</div>
		<div class="update-now">
			<h4><?php esc_html_e('Buy VW Ecommerce Store at 20% Discount','vw-ecommerce-store'); ?></h4>
			<h4><?php esc_html_e('Use Coupon','vw-ecommerce-store'); ?> ( <span><?php esc_html_e('vwpro20','vw-ecommerce-store'); ?></span> ) </h4> 
			<div class="info-link">
				<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_BUY_NOW ); ?>" target="_blank"> <?php esc_html_e( 'Upgrade to Pro', 'vw-ecommerce-store' ); ?></a>
			</div>
		</div>
    </div>

    <div class="tab-sec">
		<div class="tab">
			<button class="tablinks" onclick="vw_ecommerce_store_open_tab(event, 'lite_theme')"><?php esc_html_e( 'Setup With Customizer', 'vw-ecommerce-store' ); ?></button>
			<button class="tablinks" onclick="vw_ecommerce_store_open_tab(event, 'block_pattern')"><?php esc_html_e( 'Setup With Block Pattern', 'vw-ecommerce-store' ); ?></button>
			<button class="tablinks" onclick="vw_ecommerce_store_open_tab(event, 'gutenberg_editor')"><?php esc_html_e( 'Setup With Gutunberg Block', 'vw-ecommerce-store' ); ?></button>
			<button class="tablinks" onclick="vw_ecommerce_store_open_tab(event, 'product_addons_editor')"><?php esc_html_e( 'Woocommerce Product Addons', 'vw-ecommerce-store' ); ?></button>
			<button class="tablinks" onclick="vw_ecommerce_store_open_tab(event, 'ecommerce_pro')"><?php esc_html_e( 'Get Premium', 'vw-ecommerce-store' ); ?></button>
			<button class="tablinks" onclick="vw_ecommerce_store_open_tab(event, 'free_pro')"><?php esc_html_e( 'Support', 'vw-ecommerce-store' ); ?></button>
		</div>

		<?php
			$vw_ecommerce_store_plugin_custom_css = '';
			if(class_exists('Ibtana_Visual_Editor_Menu_Class')){
				$vw_ecommerce_store_plugin_custom_css ='display: block';
			}
		?>
		<div id="lite_theme" class="tabcontent open">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
				$plugin_ins = VW_Ecommerce_Store_Plugin_Activation_Settings::get_instance();
				$vw_ecommerce_store_actions = $plugin_ins->recommended_actions;
				?>
				<div class="vw-ecommerce-store-recommended-plugins">
				    <div class="vw-ecommerce-store-action-list">
				        <?php if ($vw_ecommerce_store_actions): foreach ($vw_ecommerce_store_actions as $key => $vw_ecommerce_store_actionValue): ?>
				                <div class="vw-ecommerce-store-action" id="<?php echo esc_attr($vw_ecommerce_store_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($vw_ecommerce_store_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($vw_ecommerce_store_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($vw_ecommerce_store_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" get-start-tab-id="lite-theme-tab" href="javascript:void(0);"><?php esc_html_e('Skip','vw-ecommerce-store'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="lite-theme-tab" style="<?php echo esc_attr($vw_ecommerce_store_plugin_custom_css); ?>">
				<h3><?php esc_html_e( 'Lite Theme Information', 'vw-ecommerce-store' ); ?></h3>
				<hr class="h3hr">
			  	<p><?php esc_html_e('VW ecommerce store is a multipurpose category store not only for the online apparel and fashion accessories but it can be applied for various other businesses as well like the sports equipment shop, cosmetic shop, jewellery shop or any mobile and gadget store. If you are planning to start a business of supermarket or grocery or an online food delivery website, VW ecommerce store is a very good option. It also can display the business information for the online product sale and can be used for multiple ecommerce stores right from fashion to technology. Known for its feature richness and some incredible features like multipurpose, CTA, Bootstrap framework, SEO friendly, optimised codes and much more, it is very good in case of opening the shop related to the home appliances or in case you are interested in the automobile shopping site, VW ecommerce store is a fine option. It is good for almost all types of stores under the sun like gadgets store, home appliances shop, automobile shopping site, interior store, books store, photo store, movies store, foods and restaurants online order sites and more. It comes with the customization options and secondly it is translation ready, mobile friendly and interactive.','vw-ecommerce-store'); ?></p>
			  	<div class="col-left-inner">
			  		<h4><?php esc_html_e( 'Theme Documentation', 'vw-ecommerce-store' ); ?></h4>
					<p><?php esc_html_e( 'If you need any assistance regarding setting up and configuring the Theme, our documentation is there.', 'vw-ecommerce-store' ); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_FREE_THEME_DOC ); ?>" target="_blank"> <?php esc_html_e( 'Documentation', 'vw-ecommerce-store' ); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Theme Customizer', 'vw-ecommerce-store'); ?></h4>
					<p> <?php esc_html_e('To begin customizing your website, start by clicking "Customize".', 'vw-ecommerce-store'); ?></p>
					<div class="info-link">
						<a target="_blank" href="<?php echo esc_url( admin_url('customize.php') ); ?>"><?php esc_html_e('Customizing', 'vw-ecommerce-store'); ?></a>
					</div>
					<hr>				
					<h4><?php esc_html_e('Having Trouble, Need Support?', 'vw-ecommerce-store'); ?></h4>
					<p> <?php esc_html_e('Our dedicated team is well prepared to help you out in case of queries and doubts regarding our theme.', 'vw-ecommerce-store'); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support Forum', 'vw-ecommerce-store'); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Reviews & Testimonials', 'vw-ecommerce-store'); ?></h4>
					<p> <?php esc_html_e('All the features and aspects of this WordPress Theme are phenomenal. I\'d recommend this theme to all.', 'vw-ecommerce-store'); ?>  </p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_REVIEW ); ?>" target="_blank"><?php esc_html_e('Reviews', 'vw-ecommerce-store'); ?></a>
					</div>
			  		<div class="link-customizer">
						<h3><?php esc_html_e( 'Link to customizer', 'vw-ecommerce-store' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','vw-ecommerce-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-slides"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_slidersettings') ); ?>" target="_blank"><?php esc_html_e('Slider Settings','vw-ecommerce-store'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-welcome-write-blog"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_topbar') ); ?>" target="_blank"><?php esc_html_e('Topbar Settings','vw-ecommerce-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-products"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_services_section') ); ?>" target="_blank"><?php esc_html_e('Our Best Seller','vw-ecommerce-store'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','vw-ecommerce-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','vw-ecommerce-store'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','vw-ecommerce-store'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','vw-ecommerce-store'); ?></a>
								</div> 
							</div>
							
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','vw-ecommerce-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','vw-ecommerce-store'); ?></a>
								</div>
							</div>
						</div>
					</div>
			  	</div>
				<div class="col-right-inner">
					<h3 class="page-template"><?php esc_html_e('How to set up Home Page Template','vw-ecommerce-store'); ?></h3>
				  	<hr class="h3hr">
					<p><?php esc_html_e('Follow these instructions to setup Home page.','vw-ecommerce-store'); ?></p>
	                <ul>
	                  	<p><span class="strong"><?php esc_html_e('1. Create a new page :','vw-ecommerce-store'); ?></span><?php esc_html_e(' Go to ','vw-ecommerce-store'); ?>
					  	<b><?php esc_html_e(' Dashboard >> Pages >> Add New Page','vw-ecommerce-store'); ?></b></p>

	                  	<p><?php esc_html_e('Name it as "Home" then select the template "Custom Home Page".','vw-ecommerce-store'); ?></p>
	                  	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/home-page-template.png" alt="" />
	                  	<p><span class="strong"><?php esc_html_e('2. Set the front page:','vw-ecommerce-store'); ?></span><?php esc_html_e(' Go to ','vw-ecommerce-store'); ?>
					  	<b><?php esc_html_e(' Settings >> Reading ','vw-ecommerce-store'); ?></b></p>
					  	<p><?php esc_html_e('Select the option of Static Page, now select the page you created to be the homepage, while another page to be your default page.','vw-ecommerce-store'); ?></p>
	                  	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/set-front-page.png" alt="" />
	                  	<p><?php esc_html_e(' Once you are done with this, then follow the','vw-ecommerce-store'); ?> <a class="doc-links" href="https://www.vwthemesdemo.com/docs/free-vw-ecommerce-store/" target="_blank"><?php esc_html_e('Documentation','vw-ecommerce-store'); ?></a></p>
	                </ul>
			  	</div>
			</div>
		</div>	

		<div id="block_pattern" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = VW_Ecommerce_Store_Plugin_Activation_Settings::get_instance();
			$vw_ecommerce_store_actions = $plugin_ins->recommended_actions;
			?>
				<div class="vw-ecommerce-store-recommended-plugins">
				    <div class="vw-ecommerce-store-action-list">
				        <?php if ($vw_ecommerce_store_actions): foreach ($vw_ecommerce_store_actions as $key => $vw_ecommerce_store_actionValue): ?>
				                <div class="vw-ecommerce-store-action" id="<?php echo esc_attr($vw_ecommerce_store_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($vw_ecommerce_store_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($vw_ecommerce_store_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($vw_ecommerce_store_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" href="javascript:void(0);" get-start-tab-id="gutenberg-editor-tab"><?php esc_html_e('Skip','vw-ecommerce-store'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="gutenberg-editor-tab" style="<?php echo esc_attr($vw_ecommerce_store_plugin_custom_css); ?>">
				<div class="block-pattern-img">
				  	<h3><?php esc_html_e( 'Block Patterns', 'vw-ecommerce-store' ); ?></h3>
					<hr class="h3hr">
					<p><?php esc_html_e('Follow the below instructions to setup Home page with Block Patterns.','vw-ecommerce-store'); ?></p>
	              	<p><b><?php esc_html_e('Click on Below Add new page button >> Click on "+" Icon >> Click Pattern Tab >> Click on homepage sections >> Publish.','vw-ecommerce-store'); ?></span></b></p>
	              	<div class="vw-ecommerce-store-pattern-page">
				    	<a href="javascript:void(0)" class="vw-pattern-page-btn button-primary button"><?php esc_html_e('Add New Page','vw-ecommerce-store'); ?></a>
				    </div>
	              	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/block-pattern.png" alt="" />
	            </div>

              	<div class="block-pattern-link-customizer">
						<h3><?php esc_html_e( 'Link to customizer', 'vw-ecommerce-store' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','vw-ecommerce-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-networking"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_social_icon_settings') ); ?>" target="_blank"><?php esc_html_e('Social Icons','vw-ecommerce-store'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','vw-ecommerce-store'); ?></a>
								</div>
								
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','vw-ecommerce-store'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','vw-ecommerce-store'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','vw-ecommerce-store'); ?></a>
								</div> 
							</div>
							
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','vw-ecommerce-store'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','vw-ecommerce-store'); ?></a>
								</div> 
							</div>
						</div>
				</div>					
	        </div>
		</div>

		<div id="gutenberg_editor" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = VW_Ecommerce_Store_Plugin_Activation_Settings::get_instance();
			$vw_ecommerce_store_actions = $plugin_ins->recommended_actions;
			?>
				<div class="vw-ecommerce-store-recommended-plugins">
				    <div class="vw-ecommerce-store-action-list">
				        <?php if ($vw_ecommerce_store_actions): foreach ($vw_ecommerce_store_actions as $key => $vw_ecommerce_store_actionValue): ?>
				                <div class="vw-ecommerce-store-action" id="<?php echo esc_attr($vw_ecommerce_store_actionValue['id']);?>">
			                        <div class="action-inner plugin-activation-redirect">
			                            <h3 class="action-title"><?php echo esc_html($vw_ecommerce_store_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($vw_ecommerce_store_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($vw_ecommerce_store_actionValue['link']); ?>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php }else{ ?>
				<h3><?php esc_html_e( 'Gutunberg Blocks', 'vw-ecommerce-store' ); ?></h3>
				<hr class="h3hr">
				<div class="vw-ecommerce-store-pattern-page">
			    	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-templates' ) ); ?>" class="vw-pattern-page-btn ibtana-dashboard-page-btn button-primary button"><?php esc_html_e('Ibtana Settings','vw-ecommerce-store'); ?></a>
			    </div>

			    <div class="link-customizer-with-guternberg-ibtana">
						<h3><?php esc_html_e( 'Link to customizer', 'vw-ecommerce-store' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','vw-ecommerce-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-networking"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_social_icon_settings') ); ?>" target="_blank"><?php esc_html_e('Social Icons','vw-ecommerce-store'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','vw-ecommerce-store'); ?></a>
								</div>
								
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','vw-ecommerce-store'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','vw-ecommerce-store'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','vw-ecommerce-store'); ?></a>
								</div> 
							</div>
							
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_ecommerce_store_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','vw-ecommerce-store'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','vw-ecommerce-store'); ?></a>
								</div> 
							</div>
						</div>
				</div>
			<?php } ?>
		</div>

		<div id="product_addons_editor" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = VW_Ecommerce_Store_Plugin_Activation_Settings::get_instance();
			$vw_ecommerce_store_actions = $plugin_ins->recommended_actions;
			?>
				<div class="vw-ecommerce-store-recommended-plugins">
				    <div class="vw-ecommerce-store-action-list">
				        <?php if ($vw_ecommerce_store_actions): foreach ($vw_ecommerce_store_actions as $key => $vw_ecommerce_store_actionValue): ?>
				                <div class="vw-ecommerce-store-action" id="<?php echo esc_attr($vw_ecommerce_store_actionValue['id']);?>">
			                        <div class="action-inner plugin-activation-redirect">
			                            <h3 class="action-title"><?php echo esc_html($vw_ecommerce_store_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($vw_ecommerce_store_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($vw_ecommerce_store_actionValue['link']); ?>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php }else{ ?>
				<h3><?php esc_html_e( 'Woocommerce Products Blocks', 'vw-ecommerce-store' ); ?></h3>
				<hr class="h3hr">
				<div class="vw-ecommerce-store-pattern-page">
					<p><?php esc_html_e('Follow the below instructions to setup Products Templates.','vw-ecommerce-store'); ?></p>
					<p><b><?php esc_html_e('1. First you need to activate these plugins','vw-ecommerce-store'); ?></b></p>
						<p><?php esc_html_e('1. Ibtana - WordPress Website Builder ','vw-ecommerce-store'); ?></p>
						<p><?php esc_html_e('2. Ibtana - Ecommerce Product Addons.','vw-ecommerce-store'); ?></p>
						<p><?php esc_html_e('3. Woocommerce','vw-ecommerce-store'); ?></p>

	              	<div class="vw-ecommerce-store-pattern-page">
			    			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-templates&ive_wizard_view=parent' ) ); ?>" class="vw-pattern-page-btn ibtana-dashboard-page-btn button-primary button"><?php esc_html_e('Woocommerce Templates','vw-ecommerce-store'); ?></a>
			    		</div>
			    </div>
			<?php } ?>
		</div>

		<div id="ecommerce_pro" class="tabcontent">
		  	<h3><?php esc_html_e( 'Premium Theme Information', 'vw-ecommerce-store' ); ?></h3>
			<hr class="h3hr">
		    <div class="col-left-pro">
		    	<p><?php esc_html_e('WordPress Ecommerce theme is a theme of premium order and is highly suitable for the businesses like online apparel shops as well as the fashion accessory stores. It is also good for sports equipment shop, cosmetics shop, mobile and gadgets store, jewellery shop, furniture shop, supermarket, grocery store or online food delivering website. It has some incredible features to support and some of these are retina ready, professional, personalization options, testimonial section, customization options, multipurpose, translation ready and much more. This WordPress theme is totally designed for the ecommerce sites and there is an option to display the business information and the option of the product sale online. It is also specially designed for shops and eCommerce sites. It could be used for all types of e-commerce online stores, including Fashion, Shoes, Jewellery, Watch, Sport, Technology, Mobile.','vw-ecommerce-store'); ?></p>
		    	<div class="pro-links">
			    	<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'vw-ecommerce-store'); ?></a>
					<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_BUY_NOW ); ?>"><?php esc_html_e('Buy Pro', 'vw-ecommerce-store'); ?></a>
					<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_PRO_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Documentation', 'vw-ecommerce-store'); ?></a>
				</div>
		    </div>
		    <div class="col-right-pro">
		    	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/responsive.png" alt="" />
		    </div>
		    <div class="featurebox">
			    <h3><?php esc_html_e( 'Theme Features', 'vw-ecommerce-store' ); ?></h3>
				<hr class="h3hr">
				<div class="table-image">
					<table class="tablebox">
						<thead>
							<tr>
								<th></th>
								<th><?php esc_html_e('Free Themes', 'vw-ecommerce-store'); ?></th>
								<th><?php esc_html_e('Premium Themes', 'vw-ecommerce-store'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e('Theme Customization', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Responsive Design', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Logo Upload', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Social Media Links', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Slider Settings', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Number of Slides', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><?php esc_html_e('4', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><?php esc_html_e('Unlimited', 'vw-ecommerce-store'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Template Pages', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><?php esc_html_e('3', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><?php esc_html_e('6', 'vw-ecommerce-store'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Home Page Template', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'vw-ecommerce-store'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Theme sections', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><?php esc_html_e('2', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><?php esc_html_e('16', 'vw-ecommerce-store'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Contact us Page Template', 'vw-ecommerce-store'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('1', 'vw-ecommerce-store'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Blog Templates & Layout', 'vw-ecommerce-store'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('3(Full width/Left/Right Sidebar)', 'vw-ecommerce-store'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Page Templates & Layout', 'vw-ecommerce-store'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('2(Left/Right Sidebar)', 'vw-ecommerce-store'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Color Pallete For Particular Sections', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Global Color Option', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Reordering', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Demo Importer', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Allow To Set Site Title, Tagline, Logo', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Enable Disable Options On All Sections, Logo', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Full Documentation', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Latest WordPress Compatibility', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Woo-Commerce Compatibility', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Support 3rd Party Plugins', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Secure and Optimized Code', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Exclusive Functionalities', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Enable / Disable', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Section Google Font Choices', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Gallery', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Simple & Mega Menu Option', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Support to add custom CSS / JS ', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Shortcodes', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Background, Colors, Header, Logo & Menu', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Premium Membership', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Budget Friendly Value', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Priority Error Fixing', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Feature Addition', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('All Access Theme Pass', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Seamless Customer Support', 'vw-ecommerce-store'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td></td>
								<td class="table-img"></td>
								<td class="update-link"><a href="<?php echo esc_url( VW_ECOMMERCE_STORE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Upgrade to Pro', 'vw-ecommerce-store'); ?></a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="free_pro" class="tabcontent">
		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-star-filled"></span><?php esc_html_e('Pro Version', 'vw-ecommerce-store'); ?></h4>
				<p> <?php esc_html_e('To gain access to extra theme options and more interesting features, upgrade to pro version.', 'vw-ecommerce-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Get Pro', 'vw-ecommerce-store'); ?></a>
				</div>
		  	</div>
		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-cart"></span><?php esc_html_e('Pre-purchase Queries', 'vw-ecommerce-store'); ?></h4>
				<p> <?php esc_html_e('If you have any pre-sale query, we are prepared to resolve it.', 'vw-ecommerce-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_CONTACT ); ?>" target="_blank"><?php esc_html_e('Question', 'vw-ecommerce-store'); ?></a>
				</div>
		  	</div>
		  	<div class="col-3">		  		
		  		<h4><span class="dashicons dashicons-admin-customizer"></span><?php esc_html_e('Child Theme', 'vw-ecommerce-store'); ?></h4>
				<p> <?php esc_html_e('For theme file customizations, make modifications in the child theme and not in the main theme file.', 'vw-ecommerce-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_CHILD_THEME ); ?>" target="_blank"><?php esc_html_e('About Child Theme', 'vw-ecommerce-store'); ?></a>
				</div>
		  	</div>

		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e('Frequently Asked Questions', 'vw-ecommerce-store'); ?></h4>
				<p> <?php esc_html_e('We have gathered top most, frequently asked questions and answered them for your easy understanding. We will list down more as we get new challenging queries. Check back often.', 'vw-ecommerce-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_FAQ ); ?>" target="_blank"><?php esc_html_e('View FAQ','vw-ecommerce-store'); ?></a>
				</div>
		  	</div>

		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-sos"></span><?php esc_html_e('Support Queries', 'vw-ecommerce-store'); ?></h4>
				<p> <?php esc_html_e('If you have any queries after purchase, you can contact us. We are eveready to help you out.', 'vw-ecommerce-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_ECOMMERCE_STORE_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Contact Us', 'vw-ecommerce-store'); ?></a>
				</div>
		  	</div>
		</div>
	</div>
</div>
<?php } ?>