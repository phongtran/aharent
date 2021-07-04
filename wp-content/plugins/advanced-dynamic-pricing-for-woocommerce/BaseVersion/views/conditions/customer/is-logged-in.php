<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="wdp-column wdp-condition-subfield wdp-condition-field-value">
    <select name="rule[conditions][{c}][options][0]">
        <option value="yes" selected><?php _e('Yes', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value="no"><?php _e('No', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
    </select>
</div>
