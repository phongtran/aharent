<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Field\Type;

use WPDesk\FCF\Free\Field\Type\TypeAbstract;
use WPDesk\FCF\Free\Field\Type\TypeInterface;

/**
 * Supports field type settings.
 */
class TimeType extends TypeAbstract implements TypeInterface {

	const FIELD_TYPE = 'timepicker';

	/**
	 * Returns value of field type.
	 *
	 * @return string Field type.
	 */
	public function get_field_type(): string {
		return self::FIELD_TYPE;
	}

	/**
	 * Returns label of field type.
	 *
	 * @return string Field label.
	 */
	public function get_field_type_label(): string {
		return __( 'Time', 'flexible-checkout-fields' );
	}

	/**
	 * Returns field icon as CSS Class supported by Icomoon.
	 *
	 * @return string Field icon.
	 */
	public function get_field_type_icon(): string {
		return 'icon-clock';
	}

	/**
	 * Returns whether field type is available for plugin version.
	 *
	 * @return bool Status if field type is available.
	 */
	public function is_available(): bool {
		return false;
	}
}
