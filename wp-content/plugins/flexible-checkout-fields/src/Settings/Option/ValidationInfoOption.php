<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Tab\AdvancedTab;

/**
 * Supports option settings for field.
 */
class ValidationInfoOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'validation_info';

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
		return AdvancedTab::TAB_NAME;
	}

	/**
	 * Returns type of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_INFO;
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		$url = esc_url( apply_filters( 'flexible_checkout_fields/short_url', '#', 'fcf-settings-field-tab-validation-docs' ) );
		return sprintf(
			/* translators: %1$s: anchor opening tag, %2$s: anchor closing tag */
			__( 'You can to add custom validation in the functions.php file. %1$sRead more%2$s', 'flexible-checkout-fields' ),
			'<a href="' . $url . '" target="_blank" class="fcfArrowLink">',
			'</a>'
		);
	}
}
