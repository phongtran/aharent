<?php

/**
 * Adds an 'Rental terms' tab to the Dokan settings navigation menu.
 *
 * @param array $menu_items
 *
 * @return array
 */
function prefix_add_rental_terms_tab( $menu_items ) {
    $menu_items['rental-terms'] = [
        'title'      => __( 'Rental terms' ),
        'icon'       => '<i class="fa fa-user-circle"></i>',
        'url'        => dokan_get_navigation_url( 'settings/rental-terms' ),
        'pos'        => 90,
        'permission' => 'dokan_view_store_settings_menu',
    ];
    return $menu_items;
}
add_filter( 'dokan_get_dashboard_settings_nav', 'prefix_add_rental_terms_tab' );



/**
 * Sets the title for the 'Rental terms' settings tab.
 *
 * @param string $title
 * @param string $tab
 *
 * @return string Title for tab with slug $tab
 */
function prefix_set_about_rental_terms_tab_title( $title, $tab ) {
    if ( 'rental-terms' === $tab ) {
        $title = __( 'Rental terms' );
    }
    return $title;
}
add_filter( 'dokan_dashboard_settings_heading_title', 'prefix_set_about_rental_terms_tab_title', 10, 2 );



/**
 * Sets the help text for the 'Rental terms' settings tab.
 *
 * @param string $help_text
 * @param string $tab
 *
 * @return string Help text for tab with slug $tab
 */
function prefix_set_rental_terms_tab_help_text( $help_text, $tab ) {
    if ( 'rental-terms' === $tab ) {
        $help_text = __( 'Edit your rental terms.' );
    }
    return $help_text;
}
add_filter( 'dokan_dashboard_settings_helper_text', 'prefix_set_rental_terms_tab_help_text', 10, 2 );


/**
 * Outputs the content for the 'Rental terms' settings tab.
 *
 * @param array $query_vars WP query vars
 */
function prefix_output_help_tab_content( $query_vars ) {    
    if ( isset( $query_vars['settings'] ) && 'rental-terms' === $query_vars['settings'] ) {
        if ( ! current_user_can( 'dokan_view_store_settings_menu' ) ) {
            dokan_get_template_part ('global/dokan-error', '', [
                'deleted' => false,
                'message' => __( 'You have no permission to view this page', 'dokan-lite' )
            ] );
        } else {
            $user_id        = get_current_user_id();
            $rental_terms   = get_user_meta( $user_id, 'vendor_rental_terms', true );
            $receive_return_terms = get_user_meta( $user_id, 'receive_return_terms', true );
            $delivery_terms = get_user_meta( $user_id, 'delivery_terms', true );

            ?>
            <form method="post" id="settings-form"  action="" class="dokan-form-horizontal">
                <?php wp_nonce_field( 'dokan_rental_terms_settings_nonce' ); ?>
                <div class="dokan-form-group">
                    <label class="dokan-w3 dokan-control-label" for="bio">
                        <?php esc_html_e( 'Security deposit terms' ); ?>
                    </label>
                    <div class="dokan-w5 text-editor">
                        <textarea id="rental-terms" name="rental_terms" class="tinymce-form dokan-form-control"><?php echo $rental_terms ?></textarea>
                    </div>
                </div>

                <div class="dokan-form-group">
                    <label class="dokan-w3 dokan-control-label" for="bio">
                        <?php esc_html_e( 'Receive and return terms' ); ?>
                    </label>
                    <div class="dokan-w5 text-editor">
                        <textarea id="receive-return" name="receive_return_terms" class="tinymce-form dokan-form-control"><?php echo $receive_return_terms ?></textarea>
                    </div>
                </div>

                <div class="dokan-form-group">
                    <label class="dokan-w3 dokan-control-label" for="bio">
                        <?php esc_html_e( 'Delivery terms' ); ?>
                    </label>
                    <div class="dokan-w5 text-editor">
                        <textarea id="delivery" name="delivery_terms" class="tinymce-form dokan-form-control"><?php echo $delivery_terms ?></textarea>
                    </div>
                </div>
                
                <div class="dokan-form-group">
                    <div class="dokan-w4 ajax_prev dokan-text-left" style="margin-left: 25%">
                        <input type="submit" name="dokan_update_about_settings" class="dokan-btn dokan-btn-danger dokan-btn-theme" value="<?php esc_attr_e( 'Update Settings' ); ?>">
                    </div>
                </div>
            </form>

            <style>
                #settings-form p.help-block {
                    margin-bottom: 0;
                }
            </style>
    
            <?php
        }
    }
}
add_action( 'dokan_render_settings_content', 'prefix_output_help_tab_content' );


/**
 * Saves the settings on the 'Rental terms' tab.
 *
 * Hooked with priority 5 to run before WeDevs\Dokan\Dashboard\Templates::ajax_settings()
 */
function prefix_save_rental_terms_settings() {
    $user_id   = dokan_get_current_user_id();
    $post_data = wp_unslash( $_POST );
    $nonce     = isset( $post_data['_wpnonce'] ) ? $post_data['_wpnonce'] : '';
    // Bail if another settings tab is being saved
    if ( ! wp_verify_nonce( $nonce, 'dokan_rental_terms_settings_nonce' ) ) {
        return;
    }
    $rental_terms               = $post_data['rental_terms'];
    $receive_return_terms       = $post_data['receive_return_terms'];
    $delivery_terms             = $post_data['delivery_terms'];
    
    update_user_meta( $user_id, 'vendor_rental_terms', $rental_terms );
    update_user_meta( $user_id, 'receive_return_terms', $receive_return_terms );
    update_user_meta( $user_id, 'delivery_terms', $delivery_terms );
    
    wp_send_json_success( array(
        'msg' => __( 'Your information has been saved successfully.' ),
    ) );
}
add_action( 'wp_ajax_dokan_settings', 'prefix_save_rental_terms_settings', 5 );

?>