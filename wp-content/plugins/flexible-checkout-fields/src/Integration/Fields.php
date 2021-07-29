<?php
/**
 * .
 *
 * @package WPDesk\FCF\Free
 */

namespace WPDesk\FCF\Free\Integration;

use WPDesk\FCF\Free\Integration\FieldsInterface;
use WPDesk\FCF\Free\Integration\Field;
use WPDesk\FCF\Free\Integration\FieldInterface;

/**
 * .
 */
class Fields implements FieldsInterface {

	/**
	 * List of field groups.
	 *
	 * @var array
	 */
	private $field_groups;

	/**
	 * Class constructor.
	 *
	 * @param array $field_groups List of field groups.
	 */
	public function __construct( array $field_groups ) {
		$this->field_groups = $field_groups;
	}

	/**
	 * Returns list of available fields.
	 *
	 * @param string $group_key Optionally key of field group.
	 *
	 * @return FieldInterface[] List of objects with field data.
	 */
	public function get_available_fields( string $group_key = '' ): array {
		$items = [];
		foreach ( $this->field_groups as $field_group => $fields ) {
			if ( ( $group_key !== '' ) && ( $field_group !== $group_key ) ) {
				continue;
			}

			foreach ( $fields as $field ) {
				$items[] = new Field( $field, $field_group );
			}
		}

		return $items;
	}
}
