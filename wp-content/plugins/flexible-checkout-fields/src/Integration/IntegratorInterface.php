<?php
/**
 * .
 *
 * @package WPDesk\FCF\Free
 */

namespace WPDesk\FCF\Free\Integration;

use WPDesk\FCF\Free\Integration\SectionsInterface;
use WPDesk\FCF\Free\Integration\FieldsInterface;
use WPDesk\FCF\Free\Integration\ValueInterface;

/**
 * .
 */
interface IntegratorInterface extends SectionsInterface, FieldsInterface, ValueInterface {

	/**
	 * Returns version of integration script.
	 *
	 * @example Use method to integration with plugin.
	 *
	 * @return string Integration script version.
	 */
	public function get_version(): string;

	/**
	 * Returns version of plugin core (do not use this method for plugin integration).
	 *
	 * @example Use method to create plugin dependent on this plugin.
	 *
	 * @return string Plugin core version.
	 */
	public function get_version_dev(): string;
}
