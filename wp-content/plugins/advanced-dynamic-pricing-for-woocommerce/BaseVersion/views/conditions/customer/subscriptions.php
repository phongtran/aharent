<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="wdp-column wdp-condition-subfield wdp-condition-field-method">
    <select name="rule[conditions][{c}][options][0]">
        <option value="at_least_one"><?php _e('at least one of selected',
                'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value="all"><?php _e('all of selected', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value="only"><?php _e('only selected', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
        <option value="none"><?php _e('none of selected', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
    </select>
</div>

<div class="wdp-column wdp-condition-subfield wdp-condition-field-value">
    <select multiple
            data-list="subscriptions"
            data-field="autocomplete"
            data-placeholder="<?php _e("Select values", "advanced-dynamic-pricing-for-woocommerce") ?>"
            name="rule[conditions][{c}][options][1][]">
    </select>
</div>
