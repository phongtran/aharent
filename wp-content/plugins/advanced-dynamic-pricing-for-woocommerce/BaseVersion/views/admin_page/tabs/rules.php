<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/**
 * @var boolean $hide_inactive
 * @var string $pagination Pagination HTML
 * @var string $tab current tab key
 * @var string $page current page slug
 * @var string $tabHandler current tab handler
 * @var \ADP\Settings\OptionsManager $options
 */
?>

<div id="poststuff">

    <?php if ($options->getOption('support_shortcode_products_on_sale') || $options->getOption('support_shortcode_products_bogo')): ?>
        <div style="clear: both;">
            <p style="margin: 5px">
                <?php if ($options->getOption('support_shortcode_products_on_sale')): ?>
                    <button type="button" class="button wdp-btn-rebuild-onsale-list">
                        <?php _e('Rebuild Onsale List', 'advanced-dynamic-pricing-for-woocommerce'); ?>
                    </button>
                <?php endif; ?>
                <?php if ($options->getOption('support_shortcode_products_bogo')): ?>
                    <button type="button" class="button wdp-btn-rebuild-bogo-list">
                        <?php _e('Rebuild Bogo List', 'advanced-dynamic-pricing-for-woocommerce'); ?>
                    </button>
                <?php endif; ?>

            </p>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['product']) && isset($_GET['action_rules'])): ?>
        <div>
            <span class="tag-show-rules-for-product"><?php printf(__('Only rules for product "%s" are shown',
                    'advanced-dynamic-pricing-for-woocommerce'),
                    \ADP\BaseVersion\Includes\Common\Helpers::getProductTitle($_GET['product'])); ?></span>
        </div>
    <?php endif; ?>
    <div style="clear: both;">
        <p style="float: left; margin: 5px">
            <label>
                <input type="checkbox" class="hide-disabled-rules" <?php checked($hide_inactive); ?>>
                <?php _e('Hide inactive rules', 'advanced-dynamic-pricing-for-woocommerce') ?>
            </label>
        </p>

        <form id="rules-filter" method="get" style="float: right; margin: 5px">
            <input type="hidden" name="page" value="<?php echo $page; ?>"/>
            <input type="hidden" name="tab" value="<?php echo $tab; ?>"/>
            <?php echo $pagination; ?>
        </form>
    </div>

    <div id="post-body" class="metabox-holder">
        <div id="postbox-container-2" class="postbox-container">
            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                <div id="rules-container"
                     class="sortables-container group-container loading wdp-list-container"></div>
                <p id="no-rules"
                   class="wdp-no-list-items loading"><?php _e('No rules defined',
                        'advanced-dynamic-pricing-for-woocommerce') ?></p>
                <p>
                    <button class="button add-rule wdp-add-list-item loading">
                        <?php _e('Add rule', 'advanced-dynamic-pricing-for-woocommerce') ?></button>
                </p>
                <div id="progress_div" style="">
                    <div id="container"><span class="spinner is-active" style="float:none;"></span></div>
                </div>

            </div>
        </div>

        <div style="clear: both;"></div>
    </div>
</div>

<?php include 'rules/templates.php'; ?>
