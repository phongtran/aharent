<?php
    function get_price_from_ibookcar( $product_id, $date_from, $duration, $time_unit = 'day' )
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
            "price"     => $total_price,
            "deposit"   => $deposit,
        );
    }

    function get_price_from_eventus( $product_id, $date_from, $duration, $time_unit = 'day' )
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

        $product_price *= $duration;
         
        $vendor = get_product_vendor ( $product->post );
        $vendor_percentage = get_vendor_percentage( $vendor );
        $deposit = $vendor_percentage * $product_price / 100;

        return array (
            "price"     => $product_price,
            "deposit"   => $deposit,
        );
    }

    function get_price_from_kano( $product_id, $date_from, $duration, $time_unit = 'day' )
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
            "price"     => $product_price,
            "deposit"   => $deposit,
        );
    }
    
    function get_price_from_simple_product( $product_id, $date_from, $duration, $time_unit = 'day' )
    {
        // $duration = $date_to->diff( $date_from )->format("%a") + 1;

        $product = get_product ( $product_id );
        $price = $product->get_price();
        $total_price = $price * $duration;

        $vendor = get_product_vendor ( $product->post );
        $vendor_percentage = get_vendor_percentage( $vendor );
        $deposit = $vendor_percentage * $total_price / 100;

        return array (
            "price"     => $total_price,
            "deposit"   => $deposit,
        );
    }

    function get_price_for_variable_product ( $product_id, $date_from, $duration, $time_unit = 'day' )
    {
        // $duration = $date_to->diff( $date_from )->format("%a") + 1;

        $product = new WC_Product_Variable( $product_id );
        // $variations = $product->get_available_variations();

        // $price = [];

        // foreach ( $variations as $variation )
        //     if ( (!isset($variation['attributes']['attribute_time_unit'])) ||
        //         ((isset($variation['attributes']['attribute_time_unit'])) && ($time_unit == $variation['attributes']['attribute_time_unit'])))
        //     {
        //         if (isset($variation['attributes']['attribute_duration']))
        //             $price[$variation['attributes']['attribute_duration']] = $variation['display_price'];
        //         else
        //             $price['single'] = $variation['display_price'];
        //     }


        $prices = get_product_prices( $product );
        $product_price = 0;

        if ( isset($prices[$time_uni][1]) )
        {
            $product_price = $prices[1];
        }
        else
        {
            if ( isset( $prices[$time_unit]['more'] ) && !empty ($prices[$time_unit]['more']) )
            {
                $price_more = $prices[$time_unit]['more'];
                unset( $prices[$time_unit]['more'] );
            }

            $temp_prices = array();
            foreach ( $prices[$time_unit] as $key => $value )
                $temp_prices[$key] = $value['price'];

            ksort( $temp_prices );
            $block_price = false;

            foreach ( $temp_prices as $price_duration => $price_value )
            {
                if ( $duration <= $price_duration )
                {
                    $product_price = $price_value;
                    if ( isset($prices[$time_unit][$price_duration]['block_price']) && $prices[$time_unit][$price_duration]['block_price'] )
                        $block_price = true;
                    break;
                }       
            }

            if ( $product_price == 0)
            {
                if ( isset( $price_more ) )
                    $product_price = $price_more;
                else
                    $product_price = end( $temp_prices );
            }
        }

        if ( !$block_price )
            $product_price *= $duration;
        
        $vendor = get_product_vendor ( $product->post );
        $vendor_percentage = get_vendor_percentage( $vendor );
        $deposit = $vendor_percentage * $product_price / 100;

        return array (
            "price"     => $product_price,
            "deposit"   => $deposit,
        );
    }

?>