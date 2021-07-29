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
class LabelOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'label';

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
		return self::FIELD_TYPE_TEXTAREA;
	}

	/**
	 * Returns list of validation rules for field.
	 * Key is regular expression without delimiters, value is message of validation error.
	 *
	 * @return array Validation rules.
	 */
	public function get_validation_rules(): array {
		return [
			'^.{1,}$' => __( 'This field is required.', 'flexible-checkout-fields' ),
		];
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return __( 'Label', 'flexible-checkout-fields' );
	}

	/**
	 * Filters option value from all unsafe strings.
	 *
	 * @param string|array $field_value Original option value.
	 *
	 * @return string|array Updated value of option.
	 */
	public function sanitize_option_value( $field_value ) {
		return wp_kses_post( wp_unslash( $field_value ) );
	}
}
