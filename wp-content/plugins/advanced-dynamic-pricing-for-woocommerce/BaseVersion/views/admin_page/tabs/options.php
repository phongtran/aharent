<?php
if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/**
 * @var array $options
 * @var string $product_bulk_table_customizer_url
 * @var string $category_bulk_table_customizer_url
 * @var array $sections
 * @var \ADP\BaseVersion\Includes\External\AdminPage\Tabs\Options $tabHandler
 * @var string $amount_saved_customer_url
 */

?>

<ul class="subsubsub">
    <?php
    $last_index = "order";
    foreach ($sections as $index => $section):
        if (empty($section['templates'])) {
            continue;
        }
        $section_title = $section['title'];
        ?>
        <li>
            <a class="section_choice"
               data-section="<?php echo $index; ?>" href="#section=<?php echo $index; ?>">
                <?php echo $section_title; ?>
            </a>
            <?php echo($last_index == $index ? '' : ' | '); ?>
        </li>
    <?php endforeach; ?>
</ul><br class="clear"/>


<form method="post">
    <input type="hidden" name="action" value="wdp">
    <input type="hidden" name="tab" value="<?php echo $tabHandler::getKey(); ?>"/>
    <?php foreach ($sections as $index => $section):
        $class = array('section', $index . '-settings-section');
        $id = $index . '_section';
        $label = $section['title'];
        ?>
        <div class="section settings-section" id="<?php echo $id; ?>">
            <h2><?php echo $label; ?></h2>

            <table class="section-settings">
                <?php
                if (isset($section['templates']) && is_array($section['templates'])) {
                    foreach ($section['templates'] as $template) {
                        $tabHandler->renderOptionsTemplate($template,
                            compact('options', 'product_bulk_table_customizer_url',
                                'category_bulk_table_customizer_url', 'amount_saved_customer_url'));
                    }
                }
                ?>
            </table>
        </div>
    <?php endforeach; ?>

    <a href="https://algolplus.com/plugins/downloads/advanced-dynamic-pricing-woocommerce-pro/"
       target=_blank><?php _e('Need more settings?', 'advanced-dynamic-pricing-for-woocommerce') ?></a>

    <p>
        <button type="submit" class="button button-primary" name="save-options"><?php _e('Save Changes',
                'advanced-dynamic-pricing-for-woocommerce') ?></button>
    </p>
</form>
