<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\DisplayOnOption;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Option\DisplayOnThankYouOption;
use WPDesk\FCF\Free\Settings\Option\DisplayOnAccountOrderOption;
use WPDesk\FCF\Free\Settings\Option\DisplayOnEmailsOption;

/**
 * Supports option settings for field.
 */
class DisplayOnWithoutAddressOption extends DisplayOnOption implements OptionInterface {

	/**
	 * Returns subfields of option, if exists.
	 *
	 * @return OptionInterface[] List of option children.
	 */
	public function get_children(): array {
		return [
			DisplayOnThankYouOption::FIELD_NAME     => new DisplayOnThankYouOption(),
			DisplayOnAccountOrderOption::FIELD_NAME => new DisplayOnAccountOrderOption(),
			DisplayOnEmailsOption::FIELD_NAME       => new DisplayOnEmailsOption(),
		];
	}
}
