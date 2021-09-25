<?php
    function get_price_from_ibookcar( $product_id, $date_from, $duration, $quantity )
    {
        $total_price = 0;
        // $duration = $date_to->diff( $date_from )->format("%a") + 1;

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


        $vendor = get_product_vendor ( $product->post );
        $vendor_percentage = get_vendor_percentage( $vendor );
        $deposit = $vendor_percentage * $total_price / 100;

        return array (
            "price"     => wc_price( $total_price * $quantity ),
            "deposit"   => wc_price( $deposit * $quantity ),
        );
    }

    function get_price_from_eventus( $product_id, $date_from, $duration, $quantity )
    {
        // $duration = $date_to->diff( $date_from )->format("%a") + 1;

        $product = new WC_Product_Variable( $product_id );
        $variations = $product->get_available_variations();

        $price = [];

        foreach ( $variations as $variation )
        {
            $price[$variation['attributes']['attribute_duration']] = $variation['display_price'];
        }

        $product_price = $price[ 'half' ];
         
        $vendor = get_product_vendor ( $product->post );
        $vendor_percentage = get_vendor_percentage( $vendor );
        $deposit = $vendor_percentage * $product_price / 100;

        return array (
            "price"     => wc_price( $product_price * $quantity ),
            "deposit"   => wc_price( $deposit * $quantity ),
        );
    }

    function get_price_from_kano( $product_id, $date_from, $duration, $quantity )
    {
        // $duration = $date_to->diff( $date_from )->format("%a") + 1;

        $product = new WC_Product_Variable( $product_id );
        $variations = $product->get_available_variations();

        $price = [];

        foreach ( $variations as $variation )
        {
            $price[$variation['attributes']['attribute_duration']] = $variation['display_price'];
        }

        $product_price = 0;

        $price_for_extra_day = get_post_meta( $product_id, '_extra_day' );

        if ( isset( $price_for_extra_day) && !empty( $price_for_extra_day[0]) )
            $price_for_extra_day = $price_for_extra_day[0];
        else
            $price_for_extra_day = 50000;

        if ( $duration <= 5 )
            $product_price = $duration * $price['5'];
        elseif ( $duration < 30 )
            $product_price = (5 * $price['5']) + (50000 * ($duration - 5));
         
        $vendor = get_product_vendor ( $product->post );
        $vendor_percentage = get_vendor_percentage( $vendor );
        $deposit = $vendor_percentage * $product_price / 100;

        return array (
            "price"     => wc_price( $product_price * $quantity ),
            "deposit"   => wc_price( $deposit * $quantity ),
        );
    }
    
    function get_price_from_simple_product( $product_id, $date_from, $duration, $quantity )
    {
        // $duration = $date_to->diff( $date_from )->format("%a") + 1;

        $product = get_product ( $product_id );
        $price = $product->get_price();
        $total_price = $price * $duration;

        $vendor = get_product_vendor ( $product->post );
        $vendor_percentage = get_vendor_percentage( $vendor );
        $deposit = $vendor_percentage * $total_price / 100;

        return array (
            "price"     => wc_price( $total_price * $quantity ),
            "deposit"   => wc_price( $deposit * $quantity ),
        );
    }

    function get_price_for_duration ( $product_id, $date_from, $duration, $quantity )
    {
        // $duration = $date_to->diff( $date_from )->format("%a") + 1;

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

        if ( $product_price == 0 && isset( $price_more ))
            $product_price = $price_more;
         
        $vendor = get_product_vendor ( $product->post );
        $vendor_percentage = get_vendor_percentage( $vendor );
        $deposit = $vendor_percentage * $product_price / 100;

        return array (
            "price"     => wc_price( $product_price * $quantity ),
            "deposit"   => wc_price( $deposit * $quantity ),
        );
    }

?>