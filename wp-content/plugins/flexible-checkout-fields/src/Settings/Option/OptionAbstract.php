<?php
/**
 * .
 *
 * @package WPDesk\FCF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionInterface;

/**
 * Abstract class for option of field.
 */
abstract class OptionAbstract implements OptionInterface {

	const FIELD_TYPE_CHECKBOX      = 'CheckboxField';
	const FIELD_TYPE_CHECKBOX_LIST = 'CheckboxListField';
	const FIELD_TYPE_GROUP         = 'GroupField';
	const FIELD_TYPE_HIDDEN        = 'HiddenField';
	const FIELD_TYPE_INFO          = 'InfoField';
	const FIELD_TYPE_INFO_ADV      = 'InfoAdvField';
	const FIELD_TYPE_INFO_NOTICE   = 'InfoNoticeField';
	const FIELD_TYPE_NUMBER        = 'NumberField';
	const FIELD_TYPE_RADIO         = 'RadioField';
	const FIELD_TYPE_RADIO_LIST    = 'RadioListField';
	const FIELD_TYPE_REPEATER      = 'RepeaterField';
	const FIELD_TYPE_SELECT        = 'SelectField';
	const FIELD_TYPE_SELECT_MULTI  = 'SelectMultiField';
	const FIELD_TYPE_TEXTAREA      = 'TextareaField';
	const FIELD_TYPE_TEXT          = 'TextField';

	/**
	 * Returns name of option tab.
	 *
	 * @return string Tab name.
	 */
	public function get_option_tab(): string {
		return '';
	}

	/**
	 * Returns type of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_type(): string {
		return '';
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return '';
	}

	/**
	 * Returns label of option row (for Repeater field).
	 *
	 * @return string Option row label.
	 */
	public function get_option_row_label(): string {
		return 'Row #%s';
	}

	/**
	 * Returns content for label tooltip.
	 *
	 * @return string Tooltip content.
	 */
	public function get_label_tooltip(): string {
		return '';
	}

	/**
	 * Returns URL for label tooltip.
	 *
	 * @return string Tooltip URL.
	 */
	public function get_label_tooltip_url(): string {
		return '';
	}

	/**
	 * Returns list of HTML attributes for field with their values.
	 *
	 * @return array Atts for field.
	 */
	public function get_input_atts(): array {
		return [];
	}

	/**
	 * Returns pattern to display value (%s will be replaced by option value).
	 * It works only for text or textarea fields.
	 *
	 * @return string Pattern to display value.
	 */
	public function get_print_pattern(): string {
		return '%s';
	}

	/**
	 * Returns status if option are readonly.
	 *
	 * @return bool Readonly status of option.
	 */
	public function is_readonly(): bool {
		return false;
	}

	/**
	 * Returns list of validation rules for field.
	 * Key is regular expression without delimiters, value is message of validation error.
	 *
	 * @return array Validation rules.
	 */
	public function get_validation_rules(): array {
		return [];
	}

	/**
	 * Returns name of option and regex for its value that must be true to display this field.
	 * Key is name of field, value is regular expression without delimiters.
	 *
	 * @return array Option names with regexes.
	 */
	public function get_options_regexes_to_display(): array {
		return [];
	}

	/**
	 * Returns name of option whose value will create list of rows for Repeater field.
	 *
	 * @return string Option name.
	 */
	public function get_option_name_to_rows(): string {
		return '';
	}

	/**
	 * Returns available values of option, if exists.
	 *
	 * @return array List of option values.
	 */
	public function get_values(): array {
		return [];
	}

	/**
	 * Returns default value of option.
	 *
	 * @return string|array Default value.
	 */
	public function get_default_value() {
		return '';
	}

	/**
	 * Returns endpoint route to retrieve values.
	 *
	 * @return string Route name of endpoint.
	 */
	public function get_endpoint_route(): string {
		return '';
	}

	/**
	 * Returns option names passed to REST API to retrieve values.
	 *
	 * @return array Option keys.
	 */
	public function get_endpoint_option_names(): array {
		return [];
	}

	/**
	 * Returns status if values from endpoint should be refreshed automatically (triggered by refresh event).
	 *
	 * @return bool Status of auto-refreshed values.
	 */
	public function is_endpoint_autorefreshed(): bool {
		return false;
	}

	/**
	 * Returns status whether change of option value initiates refresh event.
	 *
	 * @return bool Status of refresh event.
	 */
	public function is_refresh_trigger(): bool {
		return false;
	}

	/**
	 * Returns subfields of option, if exists.
	 *
	 * @return OptionInterface[] List of option children.
	 */
	public function get_children(): array {
		return [];
	}

	/**
	 * Filters option value from all unsafe strings.
	 *
	 * @param string|array $field_value Original option value.
	 *
	 * @return string|array Updated value of option.
	 */
	public function sanitize_option_value( $field_value ) {
		switch ( $this->get_option_type() ) {
			case self::FIELD_TYPE_CHECKBOX:
				return ( $field_value ) ? '1' : '0';
			case self::FIELD_TYPE_RADIO:
			case self::FIELD_TYPE_RADIO_LIST:
			case self::FIELD_TYPE_SELECT:
				if ( $values = $this->get_values() ) {
					return ( array_key_exists( $field_value, $values ) ) ? $field_value : $this->get_default_value();
				}
				break;
			case self::FIELD_TYPE_SELECT_MULTI:
				if ( ! is_array( $field_value ) ) {
					$field_value = [];
				}

				foreach ( $field_value as $value_index => $value ) {
					$field_value[ $value_index ] = sanitize_text_field( wp_unslash( $value ) );
				}
				return $field_value;
			case self::FIELD_TYPE_CHECKBOX_LIST:
			case self::FIELD_TYPE_GROUP:
			case self::FIELD_TYPE_REPEATER:
				return $field_value;
		}

		return sanitize_text_field( wp_unslash( $field_value ) );
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

		switch ( $this->get_option_type() ) {
			case self::FIELD_TYPE_CHECKBOX_LIST:
			case self::FIELD_TYPE_GROUP:
				foreach ( $this->get_children() as $option_children ) {
					$field_data = $option_children->update_field_data( $field_data, $field_settings );
				}
				break;
			case self::FIELD_TYPE_REPEATER:
				if ( ! ( $rows = $field_settings[ $option_name ] ?? [] ) ) {
					return $field_data;
				}

				foreach ( (array) $rows as $row_index => $row ) {
					if ( ! $row ) {
						continue;
					}

					foreach ( $this->get_children() as $option_children ) {
						$field_data[ $option_name ][ $row_index ] = $option_children->update_field_data(
							$field_data[ $option_name ][ $row_index ] ?? [],
							$row
						);
					}
				}
				$field_data[ $option_name ] = $this->sanitize_option_value(
					$field_data[ $option_name ] ?? $this->get_default_value()
				);
				break;
			default:
				$field_data[ $option_name ] = $this->sanitize_option_value(
					$field_settings[ $option_name ] ?? $this->get_default_value()
				);
				break;
		}

		return $field_data;
	}

	/**
	 * Returns updated settings of field contain submitted values.
	 *
	 * @param array $field_data Current settings of field.
	 * @param array $field_settings Settings of field.
	 *
	 * @return array Updated settings of field.
	 */
	public function save_field_data( array $field_data, array $field_settings ): array {
		return $this->update_field_data( $field_data, $field_settings );
	}
}
