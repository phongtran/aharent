<?php
/**
 * .
 *
 * @package WPDesk\FPF\Free
 */

namespace WPDesk\FCF\Free\Settings\Route;

use WPDesk\FCF\Free\Settings\Route\RouteAbstract;
use WPDesk\FCF\Free\Settings\Route\RouteInterface;
use WPDesk\FCF\Free\Settings\Form\SettingsPageForm;

/**
 * Supports settings for REST API route.
 */
class UpdateFormSettingsRoute extends RouteAbstract implements RouteInterface {

	const REST_API_ROUTE = 'settings';

	/**
	 * Returns route of REST API endpoint.
	 *
	 * @return string Route name.
	 */
	public function get_endpoint_route(): string {
		return self::REST_API_ROUTE;
	}

	/**
	 * Returns list of args for params using to register endpoint.
	 *
	 * @return array Args for endpoint params.
	 */
	public function get_route_params(): array {
		return [
			'form_fields' => [
				'description' => 'Form fields',
				'required'    => true,
				'default'     => [],
			],
		];
	}

	/**
	 * Returns data to be returned for endpoint.
	 *
	 * @param array $params Params for endpoint.
	 *
	 * @return mixed Response data.
	 *
	 * @throws \Exception .
	 */
	public function get_endpoint_response( array $params ) {
		try {
			$status = ( new SettingsPageForm() )->save_form_data( $params );
			if ( $status !== true ) {
				throw new \Exception();
			}

			return null;
		} catch ( \Exception $e ) {
			throw $e;
		}
	}
}
