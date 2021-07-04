<?php

namespace ADP\BaseVersion\Includes\External\AdminPage\Tabs;

use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\External\Customizer\Customizer;
use ADP\BaseVersion\Includes\Common\Helpers;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\AdminPage\Interfaces\AdminTabInterface;
use ADP\BaseVersion\Includes\Rule\Processors\SingleItemRuleProcessor;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\Settings\Varieties\Option\BooleanOption;

if ( ! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Options implements AdminTabInterface
{
	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var Context
	 */
	protected $context;

	public function __construct($context)
	{
		$this->context = $context;
		$this->title   = self::getTitle();
	}

	public function handleSubmitAction()
	{
		if (isset($_POST['save-options'])) {
			$settings = $this->context->getSettings();
			$options  = $_POST;
			unset($options['save-options']);
			unset($options['tab']);
			unset($options['action']);

			foreach (array_keys($settings->getOptions()) as $key) {
				$option = $settings->tryGetOption($key);

				if ($option) {
					if (isset($options[$key])) {
						$option->set($options[$key]);
					} elseif ($key === 'disable_all_rules_coupon_applied') {
						/** This boolean option should be ignored */
						continue;
					} elseif ($option instanceof BooleanOption) {
						$option->set(false);
						/**
						 * Because of the 'short text' field is controlled by the 'checkbox',
						 * so we have to set default value if the option does not come.
						 */
					} elseif ($key === 'initial_price_context') {
						$option->set('nofilter');
					}
				}
			}

			$settings->save();

			wp_redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function getViewVariables()
	{
		$options = $this->context->getSettings()->getOptions();

		$data = compact('options');

		list($product, $category) = $this->calculateCustomizerUrls();
		$data['product_bulk_table_customizer_url']  = $product;
		$data['category_bulk_table_customizer_url'] = $category;
		$data['amount_saved_customer_url']          = $this->makeCustomerUrl('discount_message');

		$data['sections'] = $this->getSections();

		return $data;
	}

	public static function getRelativeViewPath()
	{
		return 'admin_page/tabs/options.php';
	}

	public static function getHeaderDisplayPriority()
	{
		return 50;
	}

	public static function getKey()
	{
		return 'options';
	}

	public static function getTitle()
	{
		return __('Settings', 'advanced-dynamic-pricing-for-woocommerce');
	}

	public function enqueueScripts()
	{
		$baseVersionUrl = WC_ADP_PLUGIN_URL . "/BaseVersion/";
		wp_enqueue_script('wdp_options-scripts', $baseVersionUrl . 'assets/js/options.js', array('jquery'),
			WC_ADP_VERSION);
		wp_enqueue_style('wdp_options-styles', $baseVersionUrl . 'assets/css/options.css', array(), WC_ADP_VERSION);
	}

	protected function getSections()
	{
		$sections = array(
			"rules"           => array(
				'title'     => __("Rules", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					"rules_per_page",
					"rule_max_exec_time",
					"limit_results_in_autocomplete",
					"allow_to_exclude_products",
					"support_shortcode_products_on_sale",
					"support_shortcode_products_bogo",
				),
			),
			"category_page"   => array(
				'title'     => __("Category page", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(),
			),
			"product_page"    => array(
				'title'     => __("Product page", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					"do_not_modify_price_at_product_page",
					"show_onsale_badge",
					"use_first_range_as_min_qty",
				),
			),
			"price_templates" => array(
				'title'     => __("Product price", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					1  => "replace_price_with_min_bulk_price",
					10 => "product_price_html",
				),
			),
			"bulk_table"      => array(
				'title'     => __("Bulk table", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					"show_category_bulk_table",
					"show_matched_bulk_table",
					"discount_table_ignores_conditions",
					"bulk_table_calculation_mode",
				),
			),
			"cart"            => array(
				'title'     => __("Cart", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					"show_striked_prices",
					"show_cross_out_subtotal_in_cart_totals",
					"amount_saved_url_to_customizer",
				),
			),
			"free_products"   => array(
				'title'     => __("Free products", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					"message_after_add_free_product",
				),
			),
			"coupons"         => array(
				'title'     => __("Coupons", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					5  => "external_coupons_behavior",
					10 => "hide_coupon_word_in_totals",
				),
			),
			"calculation"     => array(
				'title'     => __("Calculation", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					"apply_discount_for_onsale_products",
					"initial_price_context",
					"combine_discounts",
					"default_discount_name",
					"combine_fees",
					"default_fee_name",
					"default_fee_tax_class",
					"override_cents",
					"is_calculate_based_on_wc_precision",
				),
			),
			"system"          => array(
				'title'     => __("System", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					"suppress_other_pricing_plugins",
					"allow_to_edit_prices_in_po",
					"update_prices_while_doing_cron",
					"update_prices_while_doing_rest_api",
					"uninstall_remove_data",
				),
			),
			"debug"           => array(
				'title'     => __("Debug", 'advanced-dynamic-pricing-for-woocommerce'),
				'templates' => array(
					"show_debug_bar",
				),
			),
		);

		return $sections;
	}

	public function renderOptionsTemplate($template, $data)
	{
		extract($data);
		include WC_ADP_PLUGIN_VIEWS_PATH . "admin_page/tabs/options/{$template}.php";
	}

	/**
	 * Making urls for simple redirect to customizer page with expanded panel and opened url with bulk table
	 *
	 */
	private function calculateCustomizerUrls()
	{
		/** @var SingleItemRuleProcessor[] $ruleProcessors */
		$rules = array();
		foreach (CacheHelper::loadActiveRules($this->context)->getRules() as $rule) {
			if ($rule instanceof SingleItemRule && $rule->getProductRangeAdjustmentHandler()) { // discount table only for 'SingleItem' rule
				$rules[] = $rule;
			}
		}

		$categoryId = 0;
		$productId  = 0;

		foreach ($rules as $rule) {
			foreach ($rule->getFilters() as $filter) {
				if ($filter->getType() === $filter::TYPE_CATEGORY && ! $categoryId) {
					$value      = $filter->getValue();
					$categoryId = $value ? reset($value) : 0;
				}

				if ($filter->getType() === $filter::TYPE_PRODUCT && ! $productId) {
					$value     = $filter->getValue();
					$productId = $value ? reset($value) : 0;
				}

				if ($filter->getType() === $filter::TYPE_SKU && ! $productId) {
					$value = $filter->getValue();
					$sku   = $value ? reset($value) : null;
					if ($sku) {
						$productId = wc_get_product_id_by_sku($sku);
					}
				}

				if ($categoryId && $productId) {
					break;
				}
			}

			if ($categoryId && $productId) {
				break;
			}
		}

		return array($this->makeUrl($productId, 'product'), $this->makeUrl($categoryId, 'category'));
	}

	private function makeUrl($id, $type)
	{
		$customizer_url = $this->makeCustomerUrl($type);

		if ( ! in_array($type, array('product', 'category'))) {
			return $customizer_url;
		}

		$query_args = array(
			'autofocus[panel]' => "wdp_{$type}_bulk_table",
		);

		if ($id) {
			if ('product' == $type) {
				$query_args['url'] = get_permalink((int)$id);
			} elseif ('category' == $type) {
				$query_args['url'] = get_term_link((int)$id, 'product_cat');
			}
		}

		return add_query_arg($query_args, $customizer_url);
	}

	private function makeCustomerUrl($type)
	{
		/**
		 * @see Customizer::init()
		 */
		if ($type === 'product' || $type === 'category') {
			$panel = "wdp_{$type}_bulk_table";
		} elseif ($type === 'discount_message') {
			$panel = "wdp_discount_message";
		} else {
			return '';
		}

		$query_args = array(
			'return'           => admin_url('themes.php'),
			'autofocus[panel]' => $panel,
		);

		return add_query_arg($query_args, admin_url('customize.php'));
	}

	public function registerAjax()
	{

	}
}
