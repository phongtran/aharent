<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="wdp-column wdp-condition-subfield wdp-condition-field-method">
    <select name="rule[conditions][{c}][options][0]">
        <option value="<"><?php _e('&lt;', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value="<="><?php _e('&lt;=', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value=">="><?php _e('&gt;=', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value=">"><?php _e('&gt;', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
    </select>
</div>

<div class="wdp-column wdp-condition-subfield wdp-condition-field-value">
    <input name="rule[conditions][{c}][options][1]" type="number" placeholder="0.00" min="0">
</div>
