<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Tab\DisplayTab;
use WPDesk\FCF\Free\Settings\Option\DisplayOnThankYouOption;
use WPDesk\FCF\Free\Settings\Option\DisplayOnAccountAddressOption;
use WPDesk\FCF\Free\Settings\Option\DisplayOnAccountOrderOption;
use WPDesk\FCF\Free\Settings\Option\DisplayOnEmailsOption;

/**
 * Supports option settings for field.
 */
class DisplayOnOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'display_on';

	/**
	 * Returns name of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	/**
	 * Returns name of option tab.
	 *
	 * @return string Tab name.
	 */
	public function get_option_tab(): string {
		return DisplayTab::TAB_NAME;
	}

	/**
	 * Returns type of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_CHECKBOX_LIST;
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return __( 'Pages/e-mails', 'flexible-checkout-fields' );
	}

	/**
	 * Returns subfields of option, if exists.
	 *
	 * @return OptionInterface[] List of option children.
	 */
	public function get_children(): array {
		return [
			DisplayOnThankYouOption::FIELD_NAME       => new DisplayOnThankYouOption(),
			DisplayOnAccountAddressOption::FIELD_NAME => new DisplayOnAccountAddressOption(),
			DisplayOnAccountOrderOption::FIELD_NAME   => new DisplayOnAccountOrderOption(),
			DisplayOnEmailsOption::FIELD_NAME         => new DisplayOnEmailsOption(),
		];
	}
}
