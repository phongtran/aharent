<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;
use WPDesk\FCF\Free\Settings\Tab\PricingTab;

/**
 * Supports option settings for field.
 */
class PricingAdvOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'pricing_adv';

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
		return PricingTab::TAB_NAME;
	}

	/**
	 * Returns type of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_INFO_ADV;
	}

	/**
	 * Returns label of option.
	 *
	 * @return string Option label.
	 */
	public function get_option_label(): string {
		$url = esc_url( apply_filters( 'flexible_checkout_fields/short_url', '#', 'fcf-settings-field-tab-pricing-upgrade' ) );
		return sprintf(
			/* translators: %1$s: break line, %2$s: anchor opening tag, %3$s: anchor closing tag */
			__( 'Add a fixed or percentage price to the field and set the tax on this price.%1$s %2$sUpgrade to PRO%3$s', 'flexible-checkout-fields' ),
			'<br>',
			'<a href="' . $url . '" target="_blank" class="fcfArrowLink">',
			'</a>'
		);
	}
}
