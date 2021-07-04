<?php

/**
 * @var string $hash
 * @var string $cartUrl
 * @var float $qty
 *
 */

?>
    <li>
        <div>
            <span class="" style="vertical-align: middle">+</span>
            <span class="" style="vertical-align: middle">
				<?php
                echo sprintf(__('You have deleted %d free products from the cart.',
                    'advanced-dynamic-pricing-for-woocommerce'), $qty);
                ?>
			</span>
            <?php
            if ( ! isset($options["dont_show_restore_link"]) || ! $options["dont_show_restore_link"]) {
                ?>
                <div class="">
                    <a href="<?php echo $cartUrl; ?>">
                        <?php _e("Restore", 'advanced-dynamic-pricing-for-woocommerce'); ?>
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
    </li>
<?php
