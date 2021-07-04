<?php
if ( ! defined('ABSPATH')) {
    exit;
}

?>

<tr valign="top">
    <th scope="row" class="titledesc"><?php _e('External coupons', 'advanced-dynamic-pricing-for-woocommerce') ?></th>
    <td class="forminp">
        <label><input type="radio" name="external_coupons_behavior"
                      value="apply" <?php checked($options['external_coupons_behavior'], "apply"); ?>>
            <?php _e("Apply", 'advanced-dynamic-pricing-for-woocommerce') ?></label>

        <label><input type="radio" name="external_coupons_behavior"
                      value="disable_if_any_rule_applied" <?php checked($options['external_coupons_behavior'],
                "disable_if_any_rule_applied"); ?>>
            <?php _e("Disable all if any rule applied", 'advanced-dynamic-pricing-for-woocommerce') ?></label>

        <label><input type="radio" name="external_coupons_behavior"
                      value="disable_if_any_of_cart_items_updated" <?php checked($options['external_coupons_behavior'],
                "disable_if_any_of_cart_items_updated"); ?>>
            <?php _e("Disable all if any of cart items updated", 'advanced-dynamic-pricing-for-woocommerce') ?></label>
    </td>
</tr>
