<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Tab\AppearanceTab;

/**
 * Supports option settings for field.
 */
class CssOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'class';

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
		return AppearanceTab::TAB_NAME;
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
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return __( 'CSS class', 'flexible-checkout-fields' );
	}

	/**
	 * Returns content for label tooltip.
	 *
	 * @return string Tooltip content.
	 */
	public function get_label_tooltip(): string {
		return __( 'Enter CSS classes separated by a space.', 'flexible-checkout-fields' );
	}

	/**
	 * Returns default value of option.
	 *
	 * @return string|array Default value.
	 */
	public function get_default_value() {
		return 'form-row';
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
		$option_name = $this->get_option_name();

		$field_data[ $option_name ] = $this->sanitize_option_value(
			implode( ' ', (array) $field_settings[ $option_name ] )
		);
		return $field_data;
	}
}
