<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Option\OptionInterface;

/**
 * Supports option settings for field.
 */
class SettingSectionsAdvOption extends OptionAbstract implements OptionInterface {

	const FIELD_NAME = 'settings_sections_adv';

	/**
	 * Returns name of option.
	 *
	 * @return string Option name.
	 */
	public function get_option_name(): string {
		return self::FIELD_NAME;
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
		$url = esc_url( apply_filters( 'flexible_checkout_fields/short_url', '#', 'fcf-settings-section-custom-upgrade' ) );
		return '<p><strong>' . __( 'Get Flexible Checkout Fields PRO to use Custom Sections', 'flexible-checkout-fields' ) . '</strong></p>
			<ul>
				<li>' . __( 'Extend the form with additional fields. Insert Text inputs and Headings. Add Checkboxes and fields with options like DropDown or Radio.', 'flexible-checkout-fields' ) . '</li>
				<li>' . __( 'Add conditional logic based on products and categories as well as FCF fields and shipping methods.', 'flexible-checkout-fields' ) . '</li>
				<li>' . __( 'Add a fixed or percentage price to the field and set the tax on this price.', 'flexible-checkout-fields' ) . '</li>
			</ul>
			<p><a href="' . $url . '" target="_blank" class="fcfArrowLink">' . __( 'Upgrade to PRO', 'flexible-checkout-fields' ) . '</a></p>';
	}
}
