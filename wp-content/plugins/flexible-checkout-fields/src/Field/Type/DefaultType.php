<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Field\Type;

use WPDesk\FCF\Free\Field\Type\TypeAbstract;
use WPDesk\FCF\Free\Field\Type\TypeInterface;
use WPDesk\FCF\Free\Settings\Tab\AdvancedTab;
use WPDesk\FCF\Free\Settings\Tab\AppearanceTab;
use WPDesk\FCF\Free\Settings\Tab\DisplayTab;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;
use WPDesk\FCF\Free\Settings\Tab\LogicTab;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Option\CssOption;
use WPDesk\FCF\Free\Settings\Option\CustomFieldDisabledOption;
use WPDesk\FCF\Free\Settings\Option\DisplayOnOption;
use WPDesk\FCF\Free\Settings\Option\EnabledOption;
use WPDesk\FCF\Free\Settings\Option\ExternalFieldOption;
use WPDesk\FCF\Free\Settings\Option\ExternalFieldInfoOption;
use WPDesk\FCF\Free\Settings\Option\FieldTypeDefaultOption;
use WPDesk\FCF\Free\Settings\Option\FormattingWcOption;
use WPDesk\FCF\Free\Settings\Option\LabelOption;
use WPDesk\FCF\Free\Settings\Option\LogicAdvOption;
use WPDesk\FCF\Free\Settings\Option\NameOption;
use WPDesk\FCF\Free\Settings\Option\PlaceholderOption;
use WPDesk\FCF\Free\Settings\Option\PriorityOption;
use WPDesk\FCF\Free\Settings\Option\RequiredOption;
use WPDesk\FCF\Free\Settings\Option\ValidationOption;
use WPDesk\FCF\Free\Settings\Option\ValidationInfoOption;

/**
 * Supports field type settings.
 */
class DefaultType extends TypeAbstract implements TypeInterface {

	const FIELD_TYPE = 'fcf_default';

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
		return __( 'Default Field', 'flexible-checkout-fields' );
	}

	/**
	 * Returns whether field type is hidden.
	 *
	 * @return bool Status if field type is hidden.
	 */
	public function is_hidden(): bool {
		return true;
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
		return [
			GeneralTab::TAB_NAME    => [
				ExternalFieldInfoOption::FIELD_NAME   => new ExternalFieldInfoOption(),
				PriorityOption::FIELD_NAME            => new PriorityOption(),
				FieldTypeDefaultOption::FIELD_NAME    => new FieldTypeDefaultOption(),
				EnabledOption::FIELD_NAME             => new EnabledOption(),
				CustomFieldDisabledOption::FIELD_NAME => new CustomFieldDisabledOption(),
				ExternalFieldOption::FIELD_NAME       => new ExternalFieldOption(),
				RequiredOption::FIELD_NAME            => new RequiredOption(),
				LabelOption::FIELD_NAME               => new LabelOption(),
				NameOption::FIELD_NAME                => new NameOption(),
			],
			AdvancedTab::TAB_NAME   => [
				ValidationOption::FIELD_NAME     => new ValidationOption(),
				ValidationInfoOption::FIELD_NAME => new ValidationInfoOption(),
			],
			AppearanceTab::TAB_NAME => [
				PlaceholderOption::FIELD_NAME => new PlaceholderOption(),
				CssOption::FIELD_NAME         => new CssOption(),
			],
			DisplayTab::TAB_NAME    => [
				DisplayOnOption::FIELD_NAME    => new DisplayOnOption(),
				FormattingWcOption::FIELD_NAME => new FormattingWcOption(),
			],
			LogicTab::TAB_NAME      => [
				LogicAdvOption::FIELD_NAME => new LogicAdvOption(),
			],
		];
	}
}
