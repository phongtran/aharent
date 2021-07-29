<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Field\Type;

use WPDesk\FCF\Free\Field\Type\DefaultType;
use WPDesk\FCF\Free\Field\Type\TypeInterface;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Option\FieldTypeOption;

/**
 * Supports field type settings.
 */
class SelectType extends DefaultType implements TypeInterface {

	const FIELD_TYPE = 'select';

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
		return __( 'Select (Drop Down)', 'flexible-checkout-fields' );
	}

	/**
	 * Returns field icon as CSS Class supported by Icomoon.
	 *
	 * @return string Field icon.
	 */
	public function get_field_type_icon(): string {
		return 'icon-tasks-alt';
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
	 * Returns list of options for field settings.
	 *
	 * @return OptionInterface[] List of option fields.
	 */
	public function get_options_objects(): array {
		$options = parent::get_options_objects();
		$options[ GeneralTab::TAB_NAME ][ FieldTypeOption::FIELD_NAME ] = new FieldTypeOption();

		return $options;
	}
}
