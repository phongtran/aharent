<?php 


function get_product_price() {
    
    $post_data = wp_unslash( $_POST );

    $product_id     = $post_data['id'];
    $quantity       = $post_data['quantity'];
    $duration       = $post_data['duration'];
    $date_from      = new DateTime( str_replace( '/', '-', $post_data['date_from'] ) );
    // $date_to        = new DateTime( str_replace( '/', '-', $post_data['date_to'] ) );

    $new_price = get_new_price( $product_id, $date_from, $duration, $quantity );

    wp_send_json_success( array( 'data' => $new_price ) );

    die();
    
}
add_action( 'wp_ajax_nopriv_get_product_price', 'get_product_price' );
add_action( 'wp_ajax_get_product_price', 'get_product_price' );


// Remove product in the cart using ajax
function warp_ajax_product_remove()
{
    // Get mini cart
    ob_start();

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item)
    {
        if ( $cart_item['product_id'] == $_POST['product_id'] && $cart_item_key == $_POST['cart_item_key'] )
        {
            WC()->cart->remove_cart_item( $cart_item_key );

            wp_send_json_success(array('message' => 'hi'));
            die;
        }
    }

    WC()->cart->maybe_set_cart_cookies();

    // Fragments and mini cart are returned
    $data = array(
        'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
            )
        ),
        'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
    );

    wp_send_json_success( $data );

    die();
}
add_action( 'wp_ajax_product_remove', 'warp_ajax_product_remove' );
add_action( 'wp_ajax_nopriv_product_remove', 'warp_ajax_product_remove' );


?>