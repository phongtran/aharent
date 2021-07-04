<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/**
 * @var array $tabs
 */

?>

<div id="wdp-report-window">
    <div id="wdp-report-control-bar">
        <div id="wdp-report-resizer"></div>

        <div id="wdp-report-main-tab-selector" class="tab-links-list">

            <div class="tab-link selected" data-tab-id="cart"><?php echo __('Cart',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></div>
            <div class="tab-link" data-tab-id="products"><?php echo __('Products',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></div>
            <div class="tab-link" data-tab-id="rules"><?php echo __('Rules',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></div>
            <div class="tab-link" data-tab-id="reports"><?php echo __('Get system report',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></div>

            <div id="wdp-report-resizer"></div>
        </div>

        <div id="progress_div" style="margin-right: 10px;">
            <img class="spinner_img" alt="spinner">
        </div>

        <div id="wdp-report-window-refresh">
            <button>
                <?php echo __('Refresh', 'advanced-dynamic-pricing-for-woocommerce'); ?>
            </button>
        </div>

        <div id="wdp-report-window-close">
            <span class="dashicons dashicons-no-alt"></span>
        </div>
    </div>


    <div id="wdp-report-tab-window"></div>

</div>
