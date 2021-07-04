<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$samplesLink = '<a href="https://docs.algolplus.com/pricing-order-docs/" target=_blank>' . __('the user manual',
        'advanced-dynamic-pricing-for-woocommerce') . '</a>';
?>
<div class="clearfix"></div>
<div id="woe-admin" class="container-fluid wpcontent">
    <br>Check examples by types:
    <a href="https://algolplus.com/plugins/dynamic-pricing-examples-basic-usage/" target=_blank><?php _e('Basics',
            'advanced-dynamic-pricing-for-woocommerce') ?></a>, &nbsp;
    <a href="https://algolplus.com/plugins/dynamic-pricing-examples-bulk-tier-modes/"
       target=_blank><?php _e('Bulk discounts', 'advanced-dynamic-pricing-for-woocommerce') ?></a>,&nbsp;
    <a href="https://algolplus.com/plugins/customer-roles/" target=_blank><?php _e('Role discounts',
            'advanced-dynamic-pricing-for-woocommerce') ?></a>,&nbsp;
    <a href="https://algolplus.com/plugins/dynamic-pricing-examples-get-products/"
       target=_blank><?php _e('Buy & get free', 'advanced-dynamic-pricing-for-woocommerce') ?></a>,&nbsp;
    <a href="https://algolplus.com/plugins/dynamic-pricing-examples-package-pricing/" target=_blank><?php _e('Packages',
            'advanced-dynamic-pricing-for-woocommerce') ?></a>,&nbsp;
    <a href="https://algolplus.com/plugins/combine-variable-products/" target=_blank><?php _e('Variable products',
            'advanced-dynamic-pricing-for-woocommerce') ?></a>,&nbsp;
    <a href="https://algolplus.com/plugins/dynamic-pricing-examples-cart-rules/" target=_blank><?php _e('Cart rules',
            'advanced-dynamic-pricing-for-woocommerce') ?></a>

    <br>
    <p><?php echo sprintf(__('Or read %s to see all examples.', 'advanced-dynamic-pricing-for-woocommerce'),
            $samplesLink); ?></p>
    <br>
    <p><?php _e('Need help? Create ticket in', 'advanced-dynamic-pricing-for-woocommerce') ?> <a
            href="https://algolplus.freshdesk.com" target=_blank><?php _e('helpdesk system',
                'advanced-dynamic-pricing-for-woocommerce') ?></a>.
</div>
