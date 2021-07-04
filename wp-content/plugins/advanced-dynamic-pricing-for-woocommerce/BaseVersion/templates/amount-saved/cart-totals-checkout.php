<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/**
 * @var $title string
 * @var $amount_saved float
 */
?>
<tr class="order-total">
    <th><?php echo $title; ?></th>
    <td data-title="<?php echo esc_attr($title); ?>"><?php echo wc_price($amount_saved); ?></td>
</tr>
