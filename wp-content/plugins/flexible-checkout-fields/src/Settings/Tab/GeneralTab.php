<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Tab;

use WPDesk\FCF\Free\Settings\Tab\TabAbstract;
use WPDesk\FCF\Free\Settings\Tab\TabInterface;

/**
 * Supports for settings tab of field.
 */
class GeneralTab extends TabAbstract implements TabInterface {

	const TAB_NAME = 'general';

	/**
	 * Returns name of tab.
	 *
	 * @return string Tab name.
	 */
	public function get_tab_name(): string {
		return self::TAB_NAME;
	}

	/**
	 * Returns label of tab.
	 *
	 * @return string Tab label.
	 */
	public function get_tab_label(): string {
		return __( 'General', 'flexible-checkout-fields' );
	}

	/**
	 * Returns tab icon as CSS Class supported by Icomoon.
	 *
	 * @return string Tab icon.
	 */
	public function get_tab_icon(): string {
		return 'icon-cog';
	}
}
