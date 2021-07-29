<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\RequiredOption;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;

/**
 * Supports option settings for field.
 */
class RequiredHiddenOption extends RequiredOption implements OptionInterface {

	/**
	 * Returns status if option are readonly.
	 *
	 * @return bool Readonly status of option.
	 */
	public function is_readonly(): bool {
		return true;
	}

	/**
	 * Returns content for label tooltip.
	 *
	 * @return string Tooltip content.
	 */
	public function get_label_tooltip(): string {
		return __( 'Requirement of this field is controlled by WooCommerce and cannot be changed.', 'flexible-checkout-fields' );
	}
}
