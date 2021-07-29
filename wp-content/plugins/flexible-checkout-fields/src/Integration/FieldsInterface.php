<?php
/**
 * .
 *
 * @package WPDesk\FCF\Free
 */

namespace WPDesk\FCF\Free\Integration;

use WPDesk\FCF\Free\Integration\FieldInterface;

/**
 * .
 */
interface FieldsInterface {

	/**
	 * Returns list of available fields.
	 *
	 * @param string $group_key Optionally key of field group.
	 *
	 * @return FieldInterface[] List of objects with field data.
	 */
	public function get_available_fields( string $group_key = '' ): array;
}
