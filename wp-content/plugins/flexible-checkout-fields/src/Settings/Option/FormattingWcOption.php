<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\FormattingOption;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Option\FormattingNewLineOption;

/**
 * Supports option settings for field.
 */
class FormattingWcOption extends FormattingOption implements OptionInterface {

	/**
	 * Returns subfields of option, if exists.
	 *
	 * @return OptionInterface[] List of option children.
	 */
	public function get_children(): array {
		return [
			FormattingNewLineOption::FIELD_NAME => new FormattingNewLineOption(),
		];
	}
}
