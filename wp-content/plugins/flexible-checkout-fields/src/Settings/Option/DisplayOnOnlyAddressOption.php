<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\DisplayOnOption;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Option\DisplayOnAccountAddressOption;

/**
 * Supports option settings for field.
 */
class DisplayOnOnlyAddressOption extends DisplayOnOption implements OptionInterface {

	/**
	 * Returns subfields of option, if exists.
	 *
	 * @return OptionInterface[] List of option children.
	 */
	public function get_children(): array {
		return [
			DisplayOnAccountAddressOption::FIELD_NAME => new DisplayOnAccountAddressOption(),
		];
	}
}
