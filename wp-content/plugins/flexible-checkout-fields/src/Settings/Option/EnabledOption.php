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
class EnabledOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'visible';

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
		return self::FIELD_TYPE_CHECKBOX;
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return __( 'Enable field', 'flexible-checkout-fields' );
	}

	/**
	 * Returns default value of option.
	 *
	 * @return string|array Default value.
	 */
	public function get_default_value() {
		return '1';
	}

	/**
	 * Returns updated settings of field contain values for this option.
	 *
	 * @param array $field_data Original settings of field.
	 * @param array $field_settings Settings of field.
	 *
	 * @return array Updated settings of field.
	 */
	public function update_field_data( array $field_data, array $field_settings ): array {
		$option_name  = $this->get_option_name();
		$option_value = $field_settings[ $option_name ] ?? 0;

		$field_data[ $option_name ] = $this->sanitize_option_value( ( $option_value ) ? '0' : '1' );
		return $field_data;
	}
}
