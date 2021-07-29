<?php
/**
 * User profile hooks.
 *
 * @package Flexible Checkout Fields
 */

/**
 * User profile hooks.
 *
 * Class Flexible_Checkout_Fields_User_Profile
 */
class Flexible_Checkout_Fields_User_Profile {

	const FIELD_TYPE = 'type';
	const FIELD_TYPE_SELECT = 'select';

	const FIELD_TYPE_INSPIRECHECKBOX = 'inspirecheckbox';
	const FIELD_TYPE_INSPIRERADIO = 'inspireradio';

	const FIELD_COPY_BILLING = 'copy_billing';

	/**
	 * Plugin.
	 *
	 * @var Flexible_Checkout_Fields_Plugin
	 */
	protected $plugin;

	/**
	 * .
	 *
	 * @var Flexible_Checkout_Fields_User_Meta
	 */
	private $user_meta;

	/**
	 * Flexible_Checkout_Fields_User_Profile constructor.
	 *
	 * @param Flexible_Checkout_Fields_Plugin    $plugin Plugin.
	 * @param Flexible_Checkout_Fields_User_Meta $user_meta .
	 */
	public function __construct( Flexible_Checkout_Fields_Plugin $plugin, Flexible_Checkout_Fields_User_Meta $user_meta ) {
		$this->plugin    = $plugin;
		$this->user_meta = $user_meta;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'woocommerce_customer_meta_fields', array( $this, 'add_customer_meta_fields' ) );

		add_action( 'show_user_profile', array( $this, 'add_custom_user_fields_admin' ), 75 );
		add_action( 'edit_user_profile', array( $this, 'add_custom_user_fields_admin' ), 75 );

		add_action( 'personal_options_update', array( $this, 'save_custom_user_fields_admin' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_custom_user_fields_admin' ) );

	}

	/**
	 * Prepare fields.
	 *
	 * @param array $fields Fields.
	 * @param array $built_in_fields Fields.
	 *
	 * @return array
	 */
	private function prepare_fields( array $fields, array $built_in_fields ) {
		$field_types = $this->plugin->get_fields();
		foreach ( $fields as $key => $field_from_fcf_settings ) {
			if ( isset( $built_in_fields[ $key ] ) ) {
				$field = $built_in_fields[ $key ];
				if ( isset( $field_from_fcf_settings['label'] ) && '' !== $field_from_fcf_settings['label'] ) {
					$field['label'] = $field_from_fcf_settings['label'];
				}
			} else {
				$field = $field_from_fcf_settings;
			}
			$field_from_fcf_settings = new Flexible_Checkout_Fields_Field( $field, $this->plugin );
			if ( $field_from_fcf_settings->is_field_excluded_for_user() ) {
				unset( $fields[ $key ] );
			} else {
				if ( empty( $field['class'] ) ) {
					$field['class'] = '';
				} elseif ( is_array( $field['class'] ) ) {
					$field['class'] = implode( ' ', $field['class'] );
				}
				$field['description'] = '';
				if ( empty( $field[ self::FIELD_TYPE ] ) ) {
					$field[ self::FIELD_TYPE ] = 'text';
				}
				if ( self::FIELD_TYPE_INSPIRECHECKBOX === $field[ self::FIELD_TYPE ] && isset( $field_types[ self::FIELD_TYPE_INSPIRECHECKBOX ] ) ) {
					$field_type_settings = new Flexible_Checkout_Fields_Field_Type_Settings( $field_types[ self::FIELD_TYPE_INSPIRECHECKBOX ] );
					if ( ! $field_type_settings->is_pro() ) {
						$field['class']            = self::FIELD_TYPE_SELECT;
						$field[ self::FIELD_TYPE ] = self::FIELD_TYPE_SELECT;
						$field['options']          = array(
							wpdesk__( 'Yes', 'flexible-checkout-fields' ) => __( 'Yes', 'flexible-checkout-fields' ),
							wpdesk__( '', 'flexible-checkout-fields' )    => __( 'No', 'flexible-checkout-fields' ),
						);
					}
				}
				if ( self::FIELD_TYPE_INSPIRERADIO === $field[ self::FIELD_TYPE ] && isset( $field_types[ self::FIELD_TYPE_INSPIRERADIO ] ) ) {
					$field_type_settings = new Flexible_Checkout_Fields_Field_Type_Settings( $field_types[ self::FIELD_TYPE_INSPIRERADIO ] );
					if ( ! $field_type_settings->is_pro() ) {
						$field[ self::FIELD_TYPE ] = self::FIELD_TYPE_SELECT;
						$field['class']            = self::FIELD_TYPE_SELECT;
					}
				}
				$fields[ $key ] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Add customer billing and shipping fields.
	 *
	 * @param array $fields Fields.
	 *
	 * @return array mixed
	 */
	public function add_customer_meta_fields( $fields ) {
		$fields['billing']['fields'] = $this->prepare_fields(
			WC()->countries->get_address_fields( '', 'billing_' ),
			isset( $fields['billing']['fields'] ) ? $fields['billing']['fields'] : array()
		);
		$additional_shipping_fields  = array();
		if ( isset( $fields['shipping'], $fields['shipping']['fields'], $fields['shipping']['fields'][ self::FIELD_COPY_BILLING ] ) ) {
			$additional_shipping_fields = array( self::FIELD_COPY_BILLING => $fields['shipping']['fields'][ self::FIELD_COPY_BILLING ] );
		}
		$fields['shipping']['fields'] = array_merge(
			$additional_shipping_fields,
			$this->prepare_fields(
				WC()->countries->get_address_fields( '', 'shipping_' ),
				isset( $fields['shipping']['fields'] ) ? $fields['shipping']['fields'] : array()
			)
		);

		return $fields;
	}


	/**
	 * Add custom fields to edit user admin /wp-admin/profile.php.
	 *
	 * @param mixed $user .
	 *
	 * @return void
	 */
	public function add_custom_user_fields_admin( $user ) {
		$settings = $this->plugin->get_settings();
		$sections = $this->plugin->sections;
		if ( ! empty( $settings ) ) {
			foreach ( $settings as $key => $type ) {
				if ( in_array( $key, array( 'shipping', 'billing' ) ) ) {
					continue;
				}
				if ( ! $this->user_meta->is_fcf_section( $key ) ) {
					continue;
				}
				if ( is_array( $type ) ) {
					foreach ( $type as $field ) {
						if ( isset( $field['visible'] ) && 0 === intval( $field['visible'] ) && ( isset( $field['custom_field'] ) && 1 === intval( $field['custom_field'] ) ) ) {
							$return = false;

							$return = apply_filters( 'flexible_checkout_fields_user_fields', $return, $field, $user );

							if ( false === $return ) {

								switch ( $field[ self::FIELD_TYPE ] ) {
									case 'textarea':
										$fields[] = '
		                                        <tr>
		                                            <th><label for="' . esc_attr( $field['name'] ) . '">' . $field['label'] . '</label></th>
		                                            <td>
		                                                <textarea name="' . esc_attr( $field['name'] ) . '" id="' . esc_html( $field['name'] ) . '" class="regular-text" rows="5" cols="30">' . esc_textarea( get_the_author_meta( $field['name'], $user->ID ) ) . '</textarea><br /><span class="description"></span>
		                                            </td>
		                                        </tr>
		                                    ';
										break;

									default:
										$fields[] = '
		                                        <tr>
		                                            <th><label for="' . esc_attr( $field['name'] ) . '">' . $field['label'] . '</label></th>
		                                            <td>
		                                                <input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . $field['name'] . '" value="' . esc_attr( get_the_author_meta( $field['name'], $user->ID ) ) . '" class="regular-text" /><br /><span class="description"></span>
		                                            </td>
		                                        </tr>
		                                    ';
										break;
								}
							} else {
								if ( '' !== $return ) {
									$fields[] = $return;
								}
							}
						}
					}
				}
			}
			if ( isset( $fields ) ) {
				echo '<h3>' . __( 'Additional Information', 'flexible-checkout-fields' ) . '</h3>'; // phpcs: XSS ok.
				echo '<table class="form-table">';
				echo implode( '', $fields ); // phpcs: XSS ok.
				echo '</table>';
			}
		}
	}

	/**
	 * Save custom user fields in admin.
	 *
	 * @param int $user_id User ID.
	 */
	public function save_custom_user_fields_admin( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}
		if ( wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) { // phpcs:ignore
			$this->user_meta->update_customer_meta_fields( $user_id, $_POST );
		}
	}
}
