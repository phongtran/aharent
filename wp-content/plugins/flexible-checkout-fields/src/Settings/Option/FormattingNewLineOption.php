<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;

/**
 * Supports option settings for field.
 */
class FormattingNewLineOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'display_on_option_new_line_before';

	/**
	 * Returns name of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	/**
	 * Returns type of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_CHECKBOX;
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return __( 'Display the field on a new line', 'flexible-checkout-fields' );
	}

	/**
	 * Returns default value of option.
	 *
	 * @return string|array Default value.
	 */
	public function get_default_value() {
		return '1';
	}
}
