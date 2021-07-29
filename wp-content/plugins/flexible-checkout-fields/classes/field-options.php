<?php

/**
 * Field options.
 *
 * Class Flexible_Checkout_Fields_Field_Options
 */
class Flexible_Checkout_Fields_Field_Options {

	const ALLOWED_HTML_TAGS_IN_OPTION = '<img><a><strong><em><br>';

	/**
	 * Options in string.
	 *
	 * @var string
	 */
	private $options_string;

	/**
	 * Type of field.
	 *
	 * @var string
	 */
	private $field_type;

	/**
	 * Placeholder of field.
	 *
	 * @var string
	 */
	private $empty_option_label;

	/**
	 * Flexible_Checkout_Fields_Field_Options constructor.
	 *
	 * @param string $options_string Options in string.
	 * @param string $empty_option_label Placeholder of field.
	 * @param string $field_type Type of field.
	 */
	public function __construct( $options_string, $empty_option_label = '', $field_type = '' ) {
		$this->options_string     = $options_string;
		$this->empty_option_label = $empty_option_label;
		$this->field_type         = $field_type;
	}

	/**
	 * Get options as array.
	 *
	 * @param bool $placeholder_status Status whether to add placeholder as first option.
	 *
	 * @return array
	 */
	public function get_options_as_array( $placeholder_status = true ) {
		$options = array();
		if ( $placeholder_status && ( 'select' === $this->field_type ) ) {
			$options[''] = ( ! empty( $this->empty_option_label ) )
				? $this->empty_option_label
				: __( 'Select option', 'flexible-checkout-fields' )
			;
		}

		$tmp_options_array = explode( "\n", $this->options_string );
		foreach ( $tmp_options_array as $option_row ) {
			$option_array = explode( ':', $option_row, 2 );
			$option_value = trim( $option_array[0] );
			$option_label = $option_value;
			if ( isset( $option_array[1] ) ) {
				$option_label = trim( $option_array[1] );
			}
			$options[ $option_value ] = strip_tags( wp_unslash( wpdesk__( $option_label, 'flexible-checkout-fields' ) ) , self::ALLOWED_HTML_TAGS_IN_OPTION );
			unset( $option_array );
		}
		unset( $tmp_options_array );
		return $options;
	}

}
