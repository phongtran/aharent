<?php
    function get_price_from_ibookcar( $product_id, $date_from, $date_to, $quantity )
    {
        $total_price = 0;
        $duration = $date_to->diff( $date_from )->format("%a") + 1;

        $product = new WC_Product_Variable( $product_id );
        $variations = $product->get_available_variations();

        $price = [];

        foreach ( $variations as $variation )
        {
            $price[$variation['attributes']['attribute_day']] = $variation['display_price'];
        }
            

        $step = 7;
        if ($duration < 7)
            $step = $duration;
        
        $day = $date_from;

        for ( $i = 0; $i < $step; $i++ )
        {
            $day_name = $day->format('l');
            $count = floor( ($duration - $i) / 7 ) + 1;

            if ( ($duration - $i) > 7 )
                $count += 1;
            
            $total_price += $count * $price[$day_name];

            $day->add(new DateInterval('P1D'));
        }

        $vendor_percentage = get_vendor_percentage( $product->post->post_author );
        $deposit = $vendor_percentage * $total_price / 100;

        return array (
            "price"     => wc_price( $total_price * $quantity ),
            "deposit"   => wc_price( $deposit * $quantity ),
        );
    }
    
    function get_price_from_simple_product( $product_id, $date_from, $date_to, $quantity )
    {
        $duration = $date_to->diff( $date_from )->format("%a") + 1;

        $product = get_product ( $product_id );
        $price = $product->get_price();
        $total_price = $price * $duration;

        $vendor_percentage = get_vendor_percentage( $product->post->post_author );
        $deposit = $vendor_percentage * $total_price / 100;

        return array (
            "price"     => wc_price( $total_price * $quantity ),
            "deposit"   => wc_price( $deposit * $quantity ),
        );
    }

    function get_price_for_duration ( $product_id, $date_from, $date_to, $quantity )
    {
        $duration = $date_to->diff( $date_from )->format("%a") + 1;

        $product = new WC_Product_Variable( $product_id );
        $variations = $product->get_available_variations();

        $price = [];

        foreach ( $variations as $variation )
        {
            $price[$variation['attributes']['attribute_duration']] = $variation['display_price'];
        }

        if ( isset( $price['more'] ) && !empty ($price ['more']) )
        {
            $price_more = $price['more'];
            unset( $price['more'] );
        }

        ksort( $price );

        $product_price = 0;

        foreach ( $price as $price_duration => $price_value )
        {
            if ( $duration < $price_duration )
            {
                $product_price = $price_value;
                break;
            }
                
        }
            
        $vendor_percentage = get_vendor_percentage( $product->post->post_author );
        $deposit = $vendor_percentage * $product_price / 100;

        return array (
            "price"     => wc_price( $product_price * $quantity ),
            "deposit"   => wc_price( $deposit * $quantity ),
        );
    }

?>