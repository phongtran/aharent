<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Tab\AdvancedTab;

/**
 * Supports option settings for field.
 */
class ValidationPostcodeOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'validation';

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
		return AdvancedTab::TAB_NAME;
	}

	/**
	 * Returns type of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_RADIO;
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return __( 'Validation', 'flexible-checkout-fields' );
	}

	/**
	 * Returns available values of option, if exists.
	 *
	 * @return array List of option values.
	 */
	public function get_values(): array {
		$rules = [
			''         => __( 'Default', 'flexible-checkout-fields' ),
			'none'     => __( 'None', 'flexible-checkout-fields' ),
			'email'    => __( 'E-mail', 'flexible-checkout-fields' ),
			'phone'    => __( 'Phone', 'flexible-checkout-fields' ),
			'postcode' => __( 'Postcode', 'flexible-checkout-fields' ),
		];

		$custom_rules = apply_filters( 'flexible_checkout_fields_custom_validation', [] );
		foreach ( $custom_rules as $rule_key => $rule_data ) {
			$rules[ $rule_key ] = $rule_data['label'];
		}
		return $rules;
	}

	/**
	 * Returns default value of option.
	 *
	 * @return string|array Default value.
	 */
	public function get_default_value() {
		return '';
	}
}
