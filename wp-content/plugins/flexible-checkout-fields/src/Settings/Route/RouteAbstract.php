<?php
/**
 * .
 *
 * @package WPDesk\FCF\Free
 */

namespace WPDesk\FCF\Free\Settings\Route;

use WPDesk\FCF\Free\Settings\Route\RouteInterface;

/**
 * Abstract class for REST API route.
 */
abstract class RouteAbstract implements RouteInterface {

	/**
	 * Returns list of HTTP methods for endpoint.
	 *
	 * @return string[] List of methods.
	 */
	public function get_route_methods(): array {
		return [ 'POST' ];
	}

	/**
	 * Returns list of args for params using to register endpoint.
	 *
	 * @return array Args for endpoint params.
	 */
	public function get_route_params(): array {
		return [
			'form_values'     => [
				'description' => 'Form values',
				'required'    => true,
				'default'     => [],
			],
			'form_field_name' => [
				'description' => 'Key of field',
				'required'    => true,
				'default'     => '',
			],
			'form_section'    => [
				'description' => 'Section name',
				'required'    => true,
				'default'     => '',
			],
			'form_fields'     => [
				'description' => 'Form fields',
				'required'    => true,
				'default'     => [],
			],
		];
	}
}
