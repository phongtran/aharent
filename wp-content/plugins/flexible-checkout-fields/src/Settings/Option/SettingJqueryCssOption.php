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
class SettingJqueryCssOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'inspire_checkout_fields_css_disable';

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
		return __( 'Disable jquery-ui.css on the frontend', 'flexible-checkout-fields' );
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_label_tooltip(): string {
		return __( 'Remember that some fields, i.e. datepicker use jQuery UI CSS. The plugin adds a default CSS but sometimes it can create some visual glitches.', 'flexible-checkout-fields' );
	}
}
