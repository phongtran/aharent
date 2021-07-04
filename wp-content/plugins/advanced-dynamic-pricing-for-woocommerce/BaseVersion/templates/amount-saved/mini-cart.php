<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/**
 * @var $title string
 * @var $amount_saved float
 */
?>
<li class="woocommerce-mini-cart__total total" style="text-align: center">
    <strong><?php echo $title; ?>:</strong>
    <?php echo wc_price($amount_saved); ?>
</li>
