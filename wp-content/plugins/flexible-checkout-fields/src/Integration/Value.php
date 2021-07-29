<?php
/**
 * .
 *
 * @package WPDesk\FCF\Free
 */

namespace WPDesk\FCF\Free\Integration;

use WPDesk\FCF\Free\Integration\ValueInterface;

/**
 * .
 */
class Value implements ValueInterface {

	/**
	 * Returns value of order field.
	 *
	 * @param string $field_key Field key.
	 * @param int    $order_id  ID of WC_Order.
	 *
	 * @return mixed Value of field, or null if not exists.
	 */
	public function get_field_value( string $field_key, int $order_id ) {
		if ( ! ( $order = wc_get_order( $order_id ) ) ) {
			return null;
		}

		$value = wpdesk_get_order_meta( $order, '_' . $field_key, true );
		if ( $json = json_decode( $value, true ) ) {
			return $json;
		} else {
			return $value;
		}
	}
}
