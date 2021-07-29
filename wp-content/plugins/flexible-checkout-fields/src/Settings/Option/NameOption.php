<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;

/**
 * Supports option settings for field.
 */
class NameOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'name';

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
		return GeneralTab::TAB_NAME;
	}

	/**
	 * Returns type of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_TEXT;
	}

	/**
	 * Returns status if option are readonly.
	 *
	 * @return bool Readonly status of option.
	 */
	public function is_readonly(): bool {
		return true;
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return __( 'Meta name', 'flexible-checkout-fields' );
	}

	/**
	 * Returns pattern to display value (%s will be replaced by option value).
	 * It works only for text or textarea fields.
	 *
	 * @return string Pattern to display value.
	 */
	public function get_print_pattern(): string {
		return '_%s';
	}
}
