<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="wdp-column wdp-condition-subfield wdp-condition-field-method">
    <select name="rule[conditions][{c}][options][0]">
        <option value="from"><?php _e('from', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value="to"><?php _e('to', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value="specific_date"><?php _e('specific date', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
    </select>
</div>

<div class="wdp-column wdp-condition-subfield wdp-condition-field-value">
    <input type="text"
           name="rule[conditions][{c}][options][1]"
           class="wdp-condition_date" placeholder="select date" readonly="readonly" data-field="date">
</div>
