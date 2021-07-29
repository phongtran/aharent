<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Tab\DisplayTab;
use WPDesk\FCF\Free\Settings\Option\FormattingStateAbbrOption;
use WPDesk\FCF\Free\Settings\Option\FormattingStateCommaOption;

/**
 * Supports option settings for field.
 */
class FormattingStateOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'formatting_state_options';

	/**
	 * Returns name of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	/**
	 * Returns name of option tab.
	 *
	 * @return string Tab name.
	 */
	public function get_option_tab(): string {
		return DisplayTab::TAB_NAME;
	}

	/**
	 * Returns type of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_CHECKBOX_LIST;
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		return __( 'State/County formatting', 'flexible-checkout-fields' );
	}

	/**
	 * Returns subfields of option, if exists.
	 *
	 * @return OptionInterface[] List of option children.
	 */
	public function get_children(): array {
		return [
			FormattingStateAbbrOption::FIELD_NAME  => new FormattingStateAbbrOption(),
			FormattingStateCommaOption::FIELD_NAME => new FormattingStateCommaOption(),
		];
	}
}
