<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\LabelOption;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;

/**
 * Supports option settings for field.
 */
class LabelOptionallyOption extends LabelOption implements OptionInterface {

	/**
	 * Returns list of validation rules for field.
	 * Key is regular expression without delimiters, value is message of validation error.
	 *
	 * @return array Validation rules.
	 */
	public function get_validation_rules(): array {
		return [];
	}
}
