<?php
/**
 * User meta.
 *
 * @package Flexible Checkout Fields
 */

/**
 * Can update user meta.
 */
class Flexible_Checkout_Fields_User_Meta {

	/**
	 * Plugin.
	 *
	 * @var Flexible_Checkout_Fields_Plugin
	 */
	protected $plugin;

	/**
	 * @param Flexible_Checkout_Fields_Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Is flexible checkout fields section?
	 *
	 * @param string $settings_section .
	 *
	 * @return bool
	 */
	public function is_fcf_section( $settings_section ) {
		$sections = $this->plugin->sections;
		foreach ( $sections as $section ) {
			if ( isset( $section['section'] ) && $section['section'] === $settings_section ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Is allowed section to be stored in User Metadata?
	 *
	 * @param string $settings_section Key of fields section.
	 *
	 * @return bool Status of section, returns true if no data.
	 */
	private function is_section_allowed_for_usermeta( $settings_section ) {
		$sections = $this->plugin->sections;
		foreach ( $sections as $section ) {
			if ( isset( $section['section'] ) && $section['section'] === $settings_section ) {
				return ( ! isset( $section['user_meta'] ) || $section['user_meta'] );
			}
		}
		return true;
	}

	/**
	 * Update customer meta data.
	 *
	 * @param int   $customer_id Customer ID.
	 * @param array $data Posted checkout data.
	 */
	public function update_customer_meta_fields( $customer_id, $data ) {
		$settings = $this->plugin->get_settings();
		if ( ! empty( $settings ) ) {
			foreach ( $settings as $key => $type ) {
				if ( ! $this->is_fcf_section( $key ) || ! $this->is_section_allowed_for_usermeta( $key ) ) {
					continue;
				}
				foreach ( $type as $field ) {
					$field_name = $field['name'];
					$fcf_field  = new Flexible_Checkout_Fields_Field( $field, $this->plugin );
					if ( ! $fcf_field->is_field_excluded_for_user() ) {
						$value = '';
						if ( isset( $data[ $field_name ] ) ) {
							if ( $fcf_field->get_type() === Flexible_Checkout_Fields_Field_Type_Settings::FIELD_TYPE_TEXTAREA ) {
								$value = sanitize_textarea_field( wp_unslash( $data[ $field_name ] ) );
							} else {
								$value = sanitize_text_field( wp_unslash( $data[ $field_name ] ) );
							}
						}
						update_user_meta( $customer_id, $field_name, $value );
					}
				}
			}
		}
	}
}
