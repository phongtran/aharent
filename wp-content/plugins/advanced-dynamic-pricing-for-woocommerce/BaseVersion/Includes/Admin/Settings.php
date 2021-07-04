<?php

namespace ADP\BaseVersion\Includes\Admin;

use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\Common\Helpers;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\CacheHelper;
use WC_Coupon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Settings {
	static $activation_notice_option = 'advanced-dynamic-pricing-for-woocommerce-activation-notice-shown';
	public static $disabled_rules_option_name = 'wdp_rules_disabled_notify';

	/**
	 * @var Context
	 */
	protected $context;

	public function __construct( $context ) {
		$this->context = $context;

		add_filter( 'woocommerce_hidden_order_itemmeta', function ( $keys ) {
			$keys[] = '_wdp_initial_cost';
			$keys[] = '_wdp_initial_tax';
//			$keys[] = '_wdp_rules'; // duplicate
			$keys[] = '_wdp_free_shipping';

			return $keys;
		}, 10, 1 );

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wdp_settings' ) {
			if ( isset( $_GET['from_notify'] ) ) {
				update_option( self::$disabled_rules_option_name, array() );
			}
		}

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

		add_action( 'woocommerce_admin_order_preview_end', array( $this, 'printAppliedDiscountsOrderPreview') );
		add_filter( 'woocommerce_admin_order_preview_get_order_details', array( $this, 'addAppliedDiscountsData'),
			10, 2 );

		//do once
		if ( ! get_option( self::$activation_notice_option ) ) {
			add_action( 'admin_notices', array( $this, 'displayPluginActivatedMessage') );
        }

        if ( $this->context->getOption('hide_coupon_word_in_totals') ) {
            /**
             * Same hook is added in Frontend::__construct().
             * In this case hook is fired during ajax requests.
             */
			add_filter( 'woocommerce_cart_totals_coupon_label', function ( $html, $coupon ) {
				/**
				 * @var WC_Coupon $coupon
				 */
				if ( $coupon->get_virtual() && ( $adp_meta = $coupon->get_meta( 'adp', true ) ) ) {
                    if ( ! empty( $adp_meta['parts'] ) && count( $adp_meta['parts'] ) < 2  ) {
                        $adp_coupon = array_pop( $adp_meta['parts'] );
                        $html = $adp_coupon->getLabel();
                    } else {
					$html = $coupon->get_code();
                    }
				}

				return $html;
			}, 5, 2 );
		}

        add_filter( 'woocommerce_cart_totals_coupon_html', function ( $coupon_html, $coupon, $discount_amount_html ) {
            /**
             * Code is copied from Frontend to work with wc_ajax
             * @var WC_Coupon $coupon
             */
            if ( $coupon->get_virtual() && $coupon->get_meta( 'adp', true ) ) {
                $coupon_html = preg_replace('#<a(.*?)class="woocommerce-remove-coupon"(.*?)</a>#', '', $coupon_html);
            }
            return $coupon_html;
        }, 10, 3 );

		add_action( 'admin_notices', array( $this, 'notify_rule_disabled' ), 10 );

//		add_action( 'admin_notices', array ($this, 'notify_coupons_disabled'), 10 );

		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'edit_rules_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'editRulesPanel') );
	}

	public function edit_rules_tab() {
		?>
        <li class="edit_rules_tab"><a href="#edit_rules_data"><span><?php _e( 'Pricing rules', 'advanced-dynamic-pricing-for-woocommerce' ); ?></span></a></li><?php
	}

	public function editRulesPanel() {
		global $post, $thepostid, $product_object;

		/** Some boys like to purge global variables. We will not allow ourselves to be beaten. */
		if ( $product_object instanceof \WC_Product ) {
			$product = CacheHelper::getWcProduct( $product_object );
		} elseif ( is_numeric( $thepostid ) ) {
			$product = CacheHelper::getWcProduct( $thepostid );
		} elseif ( $post instanceof \WP_Post ) {
			$product = CacheHelper::getWcProduct( $post );
		} else {
			$product = null;
		}

		if( ! $product ) {
			?>
			<div id="edit_rules_data" class="panel woocommerce_options_panel">
				<h4><?php _e( 'Product wasn\'t returned', 'advanced-dynamic-pricing-for-woocommerce' ); ?></h4>
			</div>
			<?php
			return;
		}


		$listRulesUrlArgs = array(
			'product'      => $product->get_id(),
			'action_rules' => 'list',
		);

		if ( $product instanceof \WC_Product_Variable && !empty( $product->get_children() ) ) {
			$listRulesUrlArgs['product_childs'] = $product->get_children();
		}

		if ( !empty( $product->get_sku() ) ) {
			$listRulesUrlArgs['product_sku'] = $product->get_sku();
		}

		$categories = get_the_terms( $product->get_id(), 'product_cat' );
		if ( $categories !== false && ! ( $categories instanceof \WP_Error ) ) {
			if ( ( $termIds = array_column( $categories, 'term_id') ) && ! empty( $termIds ) ) {
				$listRulesUrlArgs['product_categories'] = $termIds;
			}
			if ( ( $slugs = array_column( $categories, 'slug') ) && ! empty( $slugs ) ) {
				$listRulesUrlArgs['product_category_slug'] = $slugs;
			}
		}

		if ( !empty( $product->get_attributes() ) ) {
			$productAttributes = $product->get_attributes();
			$productAttributeIds = array();
			foreach ( $productAttributes as $attr ) {
				$terms = get_the_terms( $product->get_id(), $attr->get_name() );
				if ( $terms !== false && ! ( $terms instanceof \WP_Error ) ) {
					$productAttributeIds = array_merge( $productAttributeIds,
						Helpers::getProductAttributes( array_column( $terms, 'term_id' ) ) );
				}
			}
			if ( ! empty( $productAttributeIds ) ) {
				$listRulesUrlArgs['product_attributes'] = array_column( $productAttributeIds, 'id');
			}
		}

		$tags = get_the_terms( $product->get_id(), 'product_tag' );
		if ( $tags !== false && ! ( $tags instanceof \WP_Error ) && !empty( $tags ) ) {
			$listRulesUrlArgs["product_tags"] = array_column( $tags, 'term_id');
		}

		$listRulesUrl = add_query_arg( $listRulesUrlArgs, menu_page_url( 'wdp_settings', false ) );

		$addRulesUrl = add_query_arg( array(
			'product'      => $product->get_id(),
			'action_rules' => 'add',
		), menu_page_url( 'wdp_settings', false ) );

		$rulesArgs    = array( 'product' => $product->get_id(), 'active_only' => true );

		if ( isset( $listRulesUrlArgs['product_childs']) ) {
			$rulesArgs['product_childs'] = $listRulesUrlArgs['product_childs'];
		}

		if ( isset( $listRulesUrlArgs['product_sku'] ) ) {
			$rulesArgs['product_sku'] = $listRulesUrlArgs['product_sku'];
		}

		if ( isset( $listRulesUrlArgs['product_categories'] ) ) {
			$rulesArgs['product_categories'] = $listRulesUrlArgs['product_categories'];
		}

		if ( isset( $listRulesUrlArgs['product_category_slug'] ) ) {
			$rulesArgs['product_category_slug'] = $listRulesUrlArgs['product_category_slug'];
		}

		if ( isset( $listRulesUrlArgs['product_attributes'] ) ) {
			$rulesArgs['product_attributes'] = $listRulesUrlArgs['product_attributes'];
		}

		if ( isset( $listRulesUrlArgs['product_tags'] ) ) {
			$rulesArgs['product_tags'] = $listRulesUrlArgs['product_tags'];
		}

		$rules         = Database::getRules( $rulesArgs );
		$countRules   = count( $rules ) != 0 ? count( $rules ) : '';
		?>
        <div id="edit_rules_data" class="panel woocommerce_options_panel">
			<?php if ( count( $rules ) != 0 ): ?>
                <button type="button" class="button" onclick="window.open('<?php echo $listRulesUrl ?>')"
                        style="margin: 5px;">
					<?php printf( __( 'View %s rules for the product', 'advanced-dynamic-pricing-for-woocommerce' ),
						$countRules ); ?></button>
			<?php endif; ?>
            <button type="button" class="button" onclick="window.open('<?php echo $addRulesUrl ?>')"
                    style="margin: 5px;">
				<?php _e( 'Add rule for the product', 'advanced-dynamic-pricing-for-woocommerce' ); ?></button>
        </div>
		<?php
	}

	public static function printAppliedDiscountsOrderPreview() {
		PreviewOrderAppliedDiscountRules::render();
	}

	public static function addAppliedDiscountsData( $exportData, $order ) {
		$exportData = PreviewOrderAppliedDiscountRules::addData( $exportData, $order );

		return $exportData;
	}

	public function displayPluginActivatedMessage() {
		?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Advanced Dynamic Pricing for WooCommerce is available <a href="admin.php?page=wdp_settings">on this page</a>.', 'advanced-dynamic-pricing-for-woocommerce' ); ?></p>
        </div>
		<?php
		update_option( self::$activation_notice_option, true );
	}

	public function add_meta_boxes() {
		MetaBoxOrderAppliedDiscountRules::init();
	}

	private function get_current_tab() {
		return isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : $this->get_default_tab();
	}

	private function get_default_tab() {
		return 'rules';
	}

	public static function get_ids_for_filter_titles( $rules ) {
		// make array of filters splitted by type
		$filters_by_type = array(
			'products'              => array(),
			'giftable_products'     => array(),
			'giftable_categories'   => array(),
			'product_tags'          => array(),
			'product_categories'    => array(),
			'product_category_slug' => array(),
			'product_attributes'    => array(),
			'product_sku'           => array(),
			'product_sellers'		=> array(),
			'product_custom_fields' => array(),
			'usermeta'              => array(),
			'users_list'            => array(),
			'coupons'               => array(),
			'subscriptions'         => array(),
			'rules_list'            => array(),
		);
		foreach ( array_keys( Helpers::getCustomProductTaxonomies() ) as $tax_name ) {
			$filters_by_type[ $tax_name ] = array();
		}
		$filters_by_type = apply_filters( 'wdp_ids_for_filter_titles', $filters_by_type, $rules );

		$conditionsQtyCustomTaxonomyTypes = array();
		$conditionsAmountCustomTaxonomyTypes = array();
        foreach ( array_keys( Helpers::getCustomProductTaxonomies() ) as $tax_name ) {
            $conditionsQtyCustomTaxonomyTypes["custom_taxonomy_$tax_name"] = $tax_name;
            $conditionsAmountCustomTaxonomyTypes["amount_custom_taxonomy_$tax_name"] = $tax_name;
        }

		foreach ( $rules as $rule ) {
			foreach ( $rule['filters'] as $filter ) {
				if ( ! empty( $filter['value'] ) ) {
					$type  = $filter['type'];
					$value = $filter['value'];

					if ( isset( $filters_by_type[ $type ] ) ) {
						$filters_by_type[ $type ] = array_merge( $filters_by_type[ $type ], (array) $value );
					}
				}

				if ( isset( $filter['product_exclude']['values'] ) ) {
					foreach ( $filter['product_exclude']['values'] as $product_id ) {
						$filters_by_type['products'][] = $product_id;
					}
				}
			}

			if ( isset( $rule['get_products']['value'] ) ) {
				foreach ( $rule['get_products']['value'] as $filter ) {
					if ( ! isset( $filter['value'] ) ) {
						continue;
					}
					$giftMode = isset( $filter['gift_mode'] ) ? $filter['gift_mode'] : "giftable_products";

					$type = "giftable_products";
					if ( $giftMode === "allow_to_choose_from_product_cat" ) {
						$type = "giftable_categories";
					}

					$value = $filter['value'];

					$filters_by_type[ $type ] = array_merge( $filters_by_type[ $type ], (array) $value );
				}
			}

			if ( isset( $rule['bulk_adjustments']['selected_categories'] ) ) {
				$filters_by_type['product_categories'] = array_merge( $filters_by_type['product_categories'],
					(array) $rule['bulk_adjustments']['selected_categories'] );
			}

			if ( isset( $rule['bulk_adjustments']['selected_products'] ) ) {
				$filters_by_type['products'] = array_merge( $filters_by_type['products'],
					(array) $rule['bulk_adjustments']['selected_products'] );
			}

			if ( isset( $rule['conditions'] ) ) {
				foreach ( $rule['conditions'] as $condition ) {
					if ( $condition['type'] === 'specific' && isset( $condition['options'][2] ) ) {
						$value                         = $condition['options'][2];
						$filters_by_type['users_list'] = array_merge( $filters_by_type['users_list'], (array) $value );
					} elseif ( $condition['type'] === 'product_attributes' && isset( $condition['options'][2] ) ) {
						$value                                 = $condition['options'][2];
						$filters_by_type['product_attributes'] = array_merge( $filters_by_type['product_attributes'],
							(array) $value );
					} elseif ( $condition['type'] === 'product_custom_fields' && isset( $condition['options'][2] ) ) {
						$value                                    = $condition['options'][2];
						$filters_by_type['product_custom_fields'] = array_merge( $filters_by_type['product_custom_fields'],
							(array) $value );
					} elseif ( $condition['type'] === 'usermeta' && isset( $condition['options'][2] ) ) {
						$value                                 = $condition['options'][2];
						$filters_by_type['usermeta'] = array_merge( $filters_by_type['usermeta'],
							(array) $value );
					} elseif ( $condition['type'] === 'product_categories' && isset( $condition['options'][2] ) ) {
						$value                                 = $condition['options'][2];
						$filters_by_type['product_categories'] = array_merge( $filters_by_type['product_categories'],
							(array) $value );
					} elseif ( $condition['type'] === 'product_category_slug' && isset( $condition['options'][2] ) ) {
						$value                                    = $condition['options'][2];
						$filters_by_type['product_category_slug'] = array_merge( $filters_by_type['product_category_slug'],
							(array) $value );
					} elseif ( $condition['type'] === 'product_tags' && isset( $condition['options'][2] ) ) {
						$value                           = $condition['options'][2];
						$filters_by_type['product_tags'] = array_merge( $filters_by_type['product_tags'],
							(array) $value );
					} elseif ( $condition['type'] === 'products' && isset( $condition['options'][2] ) ) {
						$value                       = $condition['options'][2];
						$filters_by_type['products'] = array_merge( $filters_by_type['products'], (array) $value );
					} elseif ( $condition['type'] === 'cart_was_rule_applied' && isset( $condition['options'][1] ) ) {
						$value                      = $condition['options'][1];
						$filters_by_type['rules_list'] = array_merge( $filters_by_type['rules_list'], (array) $value );
					} elseif ( $condition['type'] === 'cart_coupons' && isset( $condition['options'][1] ) ) {
						$value                      = $condition['options'][1];
						$filters_by_type['coupons'] = array_merge( $filters_by_type['coupons'], (array) $value );
					} elseif ( $condition['type'] === 'subscriptions' && isset( $condition['options'][1] ) ) {
						$value                            = $condition['options'][1];
						$filters_by_type['subscriptions'] = array_merge( $filters_by_type['subscriptions'],
							(array) $value );
					} elseif (isset($conditionsQtyCustomTaxonomyTypes[$condition['type']]) && isset( $condition['options'][2] ) ) {
					    $taxName = $conditionsQtyCustomTaxonomyTypes[$condition['type']];
						$value                                 = $condition['options'][2];
						$filters_by_type[ $taxName ] = array_merge( $filters_by_type[ $taxName ], (array) $value );
					} elseif (isset($conditionsAmountCustomTaxonomyTypes[$condition['type']]) && isset( $condition['options'][2] ) ) {
                        $taxName = $conditionsAmountCustomTaxonomyTypes[$condition['type']];
                        $value                                 = $condition['options'][1];
                        $filters_by_type[ $taxName ] = array_merge( $filters_by_type[ $taxName ], (array) $value );
                    }
				}

			}

		}
		return $filters_by_type;
	}


	/**
	 * Retrieve from get_ids_for_filter_titles function filters all products, tags, categories, attributes and return titles
	 *
	 * @param array $filters_by_type
	 *
	 * @return array
	 */
	public static function get_filter_titles( $filters_by_type ) {
		$result = array();

		// type 'products'
		$result['products'] = array();
		foreach ( $filters_by_type['products'] as $id ) {
			$result['products'][ $id ] = '#' . $id . ' ' . Helpers::getProductTitle( $id );
		}

		if ( isset( $_GET['product'] ) ) {
			$id                        = $_GET['product'];
			$result['products'][ $id ] = '#' . $id . ' ' . Helpers::getProductTitle( $id );
		}

		$result['rules_list'] = array();
		if ( is_array( $filters_by_type['rules_list'] ) && ! empty( $filters_by_type['rules_list'] ) ) {
			$rulesList = Database::getRules( $filters_by_type['rules_list'] );
			foreach ( $rulesList as $rule ) {
				$result['rules_list'][ $rule["id"] ] = $rule["title"];
			}
		}

		// type 'giftable_products'
		$result['giftable_products'] = array();
		foreach ( $filters_by_type['giftable_products'] as $id ) {
			$result['giftable_products'][ $id ] = '#' . $id . ' ' . Helpers::getProductTitle( $id );
		}

		$result['giftable_categories'] = array();
		foreach ( $filters_by_type['giftable_categories'] as $id ) {
			$result['giftable_categories'][ $id ] = Helpers::getCategoryTitle( $id );
		}

		$result['product_sku'] = array();
		foreach ( $filters_by_type['product_sku'] as $sku ) {
			$result['product_sku'][ $sku ] = 'SKU: ' . $sku;
		}

		$result['product_sellers'] = array();
		foreach( $filters_by_type['product_sellers'] as $id ) {
			$users = Helpers::getUsers( array ( $id ) );
			$result['product_sellers'][ $id ] = $users[0]['text'];
		}

		// type 'product_tags'
		$result['product_tags'] = array();
		foreach ( $filters_by_type['product_tags'] as $id ) {
			$result['product_tags'][ $id ] = Helpers::getTagTitle( $id );
		}

		// type 'product_categories'
		$result['product_categories'] = array();
		foreach ( $filters_by_type['product_categories'] as $id ) {
			$result['product_categories'][ $id ] = Helpers::getCategoryTitle( $id );
		}

		// type 'product_category_slug'
		$result['product_category_slug'] = array();
		foreach ( $filters_by_type['product_category_slug'] as $slug ) {
			$result['product_category_slug'][ $slug ] = __( 'Slug', 'advanced-dynamic-pricing-for-woocommerce' ) . ': ' . $slug;
		}

		// product_taxonomies
		foreach ( Helpers::getCustomProductTaxonomies() as $tax ) {
			$result[ $tax->name ] = array();
			foreach ( $filters_by_type[ $tax->name ] as $id ) {
				$result[ $tax->name ][ $id ] = Helpers::getProductTaxonomyTermTitle( $id, $tax->name );
			}
		}

		// type 'product_attributes'
		$attributes                   = Helpers::getProductAttributes( array_unique( $filters_by_type['product_attributes'] ) );
		$result['product_attributes'] = array();
		foreach ( $attributes as $attribute ) {
			$result['product_attributes'][ $attribute['id'] ] = $attribute['text'];
		}

		// type 'product_custom_fields'
		$customfields                    = array_unique( $filters_by_type['product_custom_fields'] ); // use as is!
		$result['product_custom_fields'] = array();
		foreach ( $customfields as $customfield ) {
			$result['product_custom_fields'][ $customfield ] = $customfield;
		}

		// type 'users_list'
		$attributes           = Helpers::getUsers( $filters_by_type['users_list'] );
		$result['users_list'] = array();
		foreach ( $attributes as $attribute ) {
			$result['users_list'][ $attribute['id'] ] = $attribute['text'];
		}

		// type 'cart_coupons'
		$result['coupons'] = array();
		foreach ( array_unique( $filters_by_type['coupons'] ) as $code ) {
			$result['coupons'][ $code ] = $code;
		}

		// type 'subscriptions'
		$result['subscriptions'] = array();
		foreach ( $filters_by_type['subscriptions'] as $id ) {
			$result['subscriptions'][ $id ] = '#' . $id . ' ' . Helpers::getProductTitle( $id );
		}

		$result['usermeta'] = array();
		foreach ( $filters_by_type['usermeta'] as $usermeta ) {
			$result['usermeta'][ $usermeta ] = $usermeta;
		}

		return apply_filters( 'wdp_filter_titles', $result, $filters_by_type );
	}

	public function notify_rule_disabled() {
		$disabled_rules = get_option( self::$disabled_rules_option_name, array() );

		if ( $disabled_rules ) {
			$disabled_count_common    = 0;
			$disabled_count_exclusive = 0;
			foreach ( $disabled_rules as $rule ) {
				$is_exclusive = $rule['is_exclusive'];

				if ( $is_exclusive ) {
					$disabled_count_exclusive ++;
				} else {
					$disabled_count_common ++;
				}
			}

			$rule_edit_url = add_query_arg( array(
				'page'        => 'wdp_settings',
				'from_notify' => '1'
			), admin_url( 'admin.php' ) );
			$rule_edit_url = add_query_arg( 'tab', 'rules', $rule_edit_url );

			$format = "<p>%s %s <a href='%s'>%s</a></p>";

			if ( $disabled_count_common ) {
				$notice_message = "";
				$notice_message .= '<div class="notice notice-success is-dismissible">';
				if ( 1 === $disabled_count_common ) {
					$notice_message .= sprintf( $format, "",
						__( "The common rule was turned off, it was running too slow.",
							'advanced-dynamic-pricing-for-woocommerce' ), $rule_edit_url,
						__( "Edit rule", 'advanced-dynamic-pricing-for-woocommerce' ) );
				} else {
					$notice_message .= sprintf( $format, $disabled_count_common,
						__( "common rules were turned off, it were running too slow.",
							'advanced-dynamic-pricing-for-woocommerce' ), $rule_edit_url,
						__( "Edit rule", 'advanced-dynamic-pricing-for-woocommerce' ) );
				}

				$notice_message .= '</div>';

				echo $notice_message;
			}

			if ( $disabled_count_exclusive ) {
				$notice_message = "";
				$notice_message .= '<div class="notice notice-success is-dismissible">';
				if ( 1 === $disabled_count_exclusive ) {
					$notice_message .= sprintf( $format, "",
						__( "The exclusive rule was turned off, it was running too slow.",
							'advanced-dynamic-pricing-for-woocommerce' ), $rule_edit_url,
						__( "Edit rule", 'advanced-dynamic-pricing-for-woocommerce' ) );
				} else {
					$notice_message .= sprintf( $format, $disabled_count_exclusive,
						__( "exclusive rules were turned off, it were running too slow.",
							'advanced-dynamic-pricing-for-woocommerce' ), $rule_edit_url,
						__( "Edit rule", 'advanced-dynamic-pricing-for-woocommerce' ) );
				}
				$notice_message .= '</div>';

				echo $notice_message;
			}
		}
	}

	public function notify_coupons_disabled() {
		if( !$this->context->isWoocommerceCouponsEnabled() ) {
			$notice_message = "";
			$notice_message .= '<div class="notice notice-warning is-dismissible"><p>';
			$notice_message .= __( "Please enable coupons (cart adjustments won't work)", 'advanced-dynamic-pricing-for-woocommerce' );
			$notice_message .= '</p></div>';
			echo $notice_message;
		}
	}
}
