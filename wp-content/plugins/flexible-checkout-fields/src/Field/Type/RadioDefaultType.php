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
class RadioDefaultType extends DefaultType implements TypeInterface {

	const FIELD_TYPE = 'radio';

	/**
	 * Returns value of field type.
	 *
	 * @return string Field type.
	 */
	public function get_field_type(): string {
		return self::FIELD_TYPE;
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
