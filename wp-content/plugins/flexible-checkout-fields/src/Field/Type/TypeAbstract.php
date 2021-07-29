<?php
/**
 * .
 *
 * @package WPDesk\FCF\Free
 */

namespace WPDesk\FCF\Free\Field\Type;

use WPDesk\FCF\Free\Field\Type\TypeInterface;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Option\OptionIntegration;

/**
 * Abstract class of field type.
 */
abstract class TypeAbstract implements TypeInterface {

	/**
	 * Returns value of field type used in HTML.
	 *
	 * @return string Field type.
	 */
	public function get_raw_field_type(): string {
		return $this->get_field_type();
	}

	/**
	 * Returns reserved field names, overriding this field type for selected field names.
	 *
	 * @return array Field names.
	 */
	public function get_reserved_field_names(): array {
		return [];
	}

	/**
	 * Returns field icon as CSS Class supported by Icomoon.
	 *
	 * @return string Field icon.
	 */
	public function get_field_type_icon(): string {
		return '';
	}

	/**
	 * Returns whether field type is hidden.
	 *
	 * @return bool Status if field type is hidden.
	 */
	public function is_hidden(): bool {
		return false;
	}

	/**
	 * Returns whether field type is available for plugin version.
	 *
	 * @return bool Status if field type is available.
	 */
	public function is_available(): bool {
		return false;
	}

	/**
	 * Returns list of options objects for field settings.
	 *
	 * @return OptionInterface[] List of field options objects.
	 */
	public function get_options_objects(): array {
		return [];
	}

	/**
	 * Returns list of options for field settings.
	 *
	 * @return array List of field options.
	 */
	public function get_options(): array {
		$options = [];
		foreach ( $this->get_options_objects() as $option_objects ) {
			foreach ( $option_objects as $option_object ) {
				$options[] = ( new OptionIntegration( $option_object ) )->get_field_settings();
			}
		}
		return $options;
	}
}
