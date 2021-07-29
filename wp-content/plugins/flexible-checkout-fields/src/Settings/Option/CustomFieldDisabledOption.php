<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\CustomFieldOption;

/**
 * Supports option settings for field.
 */
class CustomFieldDisabledOption extends CustomFieldOption {

	/**
	 * Returns default value of option.
	 *
	 * @return string|array Default value.
	 */
	public function get_default_value() {
		return '';
	}
}
