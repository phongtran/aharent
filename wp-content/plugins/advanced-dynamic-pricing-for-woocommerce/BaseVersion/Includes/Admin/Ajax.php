<?php

namespace ADP\BaseVersion\Includes\Admin;

use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\External\WC\WcProductCustomAttributesCache;
use ADP\Factory;
use WC_Data_Store;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Ajax
{
    const ACTION_PREFIX = 'wdp_ajax';
    const SECURITY_QUERY_ARG = 'security';
    const SECURITY_ACTION = 'wdp-request';
    protected $limit = null;

    /**
     * @var Context
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;

        $this->limit = $this->context->getOption('limit_results_in_autocomplete');
        if (empty($this->limit)) {
            $this->limit = 25;
        }
    }

    public function register()
    {
        add_action('wp_ajax_' . self::ACTION_PREFIX, array($this, 'ajaxRequests'));
    }

    public function ajaxRequests()
    {
        $result = null;

        check_ajax_referer(self::SECURITY_ACTION, self::SECURITY_QUERY_ARG);

        $method     = filter_input(INPUT_POST, 'method', FILTER_SANITIZE_STRING);
        $methodName = 'ajax_' . $method;

        if (method_exists($this, $methodName)) {
            $result = $this->$methodName();
        }
        $result = apply_filters('wdp_ajax_' . $method, $result);

        wp_send_json_success($result);
    }

    public function ajax_products()
    {
        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);

        /** @var \WC_Product_Data_Store_CPT $dataStore */
        $dataStore = WC_Data_Store::load('product');
        $ids       = $dataStore->search_products($query, '', true, false, $this->limit);

        return array_values(array_map(function ($postId) {
            return array(
                'id'   => (string)$postId,
                'text' => '#' . $postId . ' ' . get_the_title($postId),
            );
        }, array_filter($ids)));
    }

    public function ajax_giftable_products()
    {
        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);

        /** @var \WC_Product_Data_Store_CPT $dataStore */
        $dataStore = WC_Data_Store::load('product');
        $ids       = $dataStore->search_products($query, '', true, false, $this->limit);

        return array_values(array_filter(array_map(function ($postId) {
            $product = CacheHelper::getWcProduct($postId);
            if ( ! $product) {
                return false;
            }

            $bundle = null;
            if ($product->is_type(array('variable', 'grouped'))) {
                $bundle = array_map(function ($postId) {
                    return array(
                        'id'   => (string)$postId,
                        'text' => '#' . $postId . ' ' . get_the_title($postId),
                    );
                }, $product->get_children());
            }

            $result = array(
                'id'   => (string)$postId,
                'text' => '#' . $postId . ' ' . get_the_title($postId),
            );

            if ($bundle) {
                $result['bundle'] = $bundle;
            }

            return $result;
        }, $ids)));
    }

    public function ajax_product_sku()
    {
        global $wpdb;
        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);

        $results = $wpdb->get_results("
SELECT DISTINCT meta_value, post_id
FROM $wpdb->postmeta
WHERE meta_key = '_sku' AND meta_value  like '%$query%' LIMIT $this->limit
");

        return apply_filters('wdp_product_sku_autocomplete_items', array_map(function ($result) {
            return array(
                'id'   => (string)$result->meta_value,
                'text' => 'SKU: ' . $result->meta_value,
            );
        }, $results), $results);
    }

    public function ajax_product_category_slug()
    {
        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);
        $terms = get_terms(array(
            'taxonomy'   => 'product_cat',
            'name__like' => $query,
            'hide_empty' => false,
            'number'     => $this->limit
        ));

        return array_map(function ($term) {
            return array(
                'id'   => $term->slug,
                'text' => __('Slug', 'advanced-dynamic-pricing-for-woocommerce') . ': ' . $term->slug,
            );
        }, $terms);
    }

    public function ajax_product_categories()
    {
        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);
        $terms = get_terms(array(
            'taxonomy'   => 'product_cat',
            'name__like' => $query,
            'hide_empty' => false,
            'number'     => $this->limit
        ));

        return array_map(function ($term) {
            $parent = $term;
            while ($parent->parent != '0') {
                $parent_id = $parent->parent;
                $parent    = get_term($parent_id, 'product_cat');
            }

            return array(
                'id'   => (string)$term->term_id,
                'text' => $parent == $term ? $term->name : $parent->name . '>' . $term->name,
            );
        }, $terms);
    }

    public function ajax_product_taxonomies()
    {
        $query         = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);
        $taxonomy_name = filter_input(INPUT_POST, 'taxonomy', FILTER_SANITIZE_STRING);

        $terms = get_terms(array(
            'taxonomy'   => $taxonomy_name,
            'name__like' => $query,
            'hide_empty' => false,
            'number'     => $this->limit,
        ));

        return array_map(function ($term) {
            return array(
                'id'   => (string)$term->term_id,
                'text' => $term->name,
            );
        }, $terms);
    }

    public function ajax_product_attributes()
    {
        global $wc_product_attributes, $wpdb;

        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);

        $taxonomies = array_map(function ($item) {
            return "'$item'";
        }, array_keys($wc_product_attributes));
        $taxonomies = implode(', ', $taxonomies);

        $items = $wpdb->get_results("
SELECT $wpdb->terms.term_id, $wpdb->terms.name, taxonomy
FROM $wpdb->term_taxonomy INNER JOIN $wpdb->terms USING (term_id)
WHERE taxonomy in ($taxonomies)
AND $wpdb->terms.name  like '%$query%' LIMIT $this->limit
");


        return array_map(function ($term) use ($wc_product_attributes) {
            $attribute = $wc_product_attributes[$term->taxonomy]->attribute_label;

            return array(
                'id'   => (string)$term->term_id,
                'text' => $attribute . ': ' . $term->name,
            );
        }, $items);
    }

    public function ajax_product_custom_attributes()
    {
        $query = strtolower(filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING));

        $results = array();
        /** @var WcProductCustomAttributesCache $productAttributesCache */
        $productAttributesCache  = Factory::get("External_WC_WcProductCustomAttributesCache");
        $attributes = $productAttributesCache->getAllCustomAttributes();

        foreach ( $attributes as $attributeName => $options ) {
            foreach ( $options as $option ) {
                if ( strpos(strtolower($option), $query) !== false ) {
                    $results[] = array(
                        'id'   => $attributeName . ': ' . $option,
                        'text' => $attributeName . ': ' . $option,
                    );
                }
            }
        }

        return $results;
    }

    public function ajax_product_tags()
    {
        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);
        $terms = get_terms(array(
            'taxonomy'   => 'product_tag',
            'name__like' => $query,
            'hide_empty' => false,
            'number'     => $this->limit
        ));

        return array_map(function ($term) {
            return array(
                'id'   => (string)$term->term_id,
                'text' => $term->name,
            );
        }, $terms);
    }

    public function ajax_product_custom_fields()
    {
        global $wpdb;

        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);
        $like  = $wpdb->esc_like($query);

        $wpFields = $wpdb->get_col("SELECT DISTINCT CONCAT(fields.meta_key,'=',fields.meta_value) FROM {$wpdb->postmeta} AS fields
JOIN {$wpdb->posts} AS products ON products.ID = fields.post_id
WHERE products.post_type IN ('product','product_variation') AND CONCAT(fields.meta_key,'=',fields.meta_value) LIKE '%{$like}%' ORDER BY meta_key LIMIT $this->limit");

        return array_map(function ($custom_field) {
            return array(
                'id'   => $custom_field,
                'text' => $custom_field,
            );
        }, $wpFields);
    }

    public function ajax_coupons()
    {
        $postsRaw = get_posts(array(
            's'              => filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING),
            'posts_per_page' => $this->limit,
            'post_type'      => 'shop_coupon',
            'post_status'    => array('publish'),
            'fields'         => 'ids',
        ));

        $items = array_map(function ($postId) {
            $code = get_the_title($postId);

            return array(
                'id'   => $code,
                'text' => $code
            );
        }, $postsRaw);

        return array_values($items);
    }

    public function ajax_rules_list()
    {
        $query     = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);
        $rulesList = Database::getRules();

        $rulesList = array_values(array_filter($rulesList, function ($rule) use ($query) {
            return (isset($_POST["current_rule"]) && $rule["id"] === $_POST["current_rule"]) ? false : stripos($rule["title"],
                    $query) !== false;
        }));

        return array_map(function ($el) {
            return array(
                "id"   => $el["id"],
                "text" => $el["title"]
            );
        }, $rulesList);
    }

    public function ajax_users_list()
    {
        $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);
        $query = "*$query*";
        $users = get_users(array(
            'fields'  => array('ID', 'user_nicename'),
            'search'  => $query,
            'orderby' => 'user_nicename',
            'number'  => $this->limit
        ));

        return array_map(function ($user) {
            return array(
                'id'   => (string)$user->ID,
                'text' => $user->user_nicename,
            );
        }, $users);
    }

    public function ajax_save_rule()
    {
        if ( ! isset($_POST['rule'])) {
            return;
        }
        $rule = $_POST['rule'];

        // prepare data to store each rule in db
        $data = array(
            'deleted'                  => 0,
            'enabled'                  => (isset($rule['enabled']) && $rule['enabled'] === 'on') ? 1 : 0,
            'exclusive'                => (isset($rule['exclusive']) && $rule['exclusive']) ? 1 : 0,
            'title'                    => filter_var(stripcslashes($rule['title']), FILTER_SANITIZE_STRING),
            'type'                     => sanitize_text_field($rule['type']),
            'priority'                 => isset($rule['priority']) ? (int)$rule['priority'] : 0,
            'options'                  => isset($rule['options']) ? $rule['options'] : array(),
            'conditions'               => array_values(isset($rule['conditions']) ? $rule['conditions'] : array()),
            'filters'                  => isset($rule['filters']) ? $rule['filters'] : array(),
            'limits'                   => array_values(isset($rule['limits']) ? $rule['limits'] : array()),
            'cart_adjustments'         => array_values(isset($rule['cart_adjustments']) ? $rule['cart_adjustments'] : array()),
            'product_adjustments'      => isset($rule['product_adjustments']) ? $rule['product_adjustments'] : array(),
            'sortable_blocks_priority' => isset($rule['sortable_blocks_priority']) ? $rule['sortable_blocks_priority'] : array(),
            'bulk_adjustments'         => isset($rule['bulk_adjustments']) ? $rule['bulk_adjustments'] : array(),
            'role_discounts'           => isset($rule['role_discounts']) ? $rule['role_discounts'] : array(),
            'get_products'             => isset($rule['get_products']) ? $rule['get_products'] : array(),
            'additional'               => isset($rule['additional']) ? $rule['additional'] : array(),
        );

        if (isset($data['additional']['disabled_by_plugin'])) {
            unset($data['additional']['disabled_by_plugin']);
        }

        foreach ($data['conditions'] as &$condition) {
            if ( ! isset($condition['options'])) {
                continue;
            }

            end($condition['options']);
            $lastIndex = key($condition['options']);

            for ($i = 0; $i < $lastIndex; $i++) {
                if ( ! isset($condition['options'][$i])) {
                    $condition['options'][$i] = null;
                }
            }
        }

        // arrays  saved as serialized values, must do "sanitize" recursive
        $arrays = array(
            'options',
            'conditions',
            'filters',
            'limits',
            'cart_adjustments',
            'product_adjustments',
            'sortable_blocks_priority',
            'role_discounts',
            'get_products',
            'additional',
        );
        foreach ($arrays as $name) {
            $data[$name] = serialize($this->sanitize_array_text_fields($data[$name]));
        }
        //allow to use HTML in bulk
        $data['bulk_adjustments'] = serialize($data['bulk_adjustments']);

        // insert or update
        $id = Database::storeRule($data, empty($rule['id']) ? null : (int)$rule['id']);

        return $id;
    }


    function sanitize_array_text_fields($array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->sanitize_array_text_fields($value);
            } else {
                $value = sanitize_text_field($value);
            }
        }

        return $array;
    }

    public function ajax_remove_rule()
    {
        $ruleId = (int)$_POST['rule_id'];
        if ($ruleId) {
            Database::markRuleAsDeleted($ruleId);
        }
        wp_send_json_success();
    }

    public function ajax_reorder_rules()
    {
        $items = $_POST['items'];

        foreach ($items as $item) {
            $id = (int)$item['id'];
            if ( ! empty($id)) {
                $data = array('priority' => (int)$item['priority']);
                Database::storeRule($data, $id);
            }
        }
    }

    public function ajax_subscriptions()
    {
        if (get_option('woocommerce_subscriptions_is_active', false)) {

            $query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING);

            $posts = wc_get_products(array(
                'type'  => array('subscription', 'subscription_variation', 'variable-subscription'),
                's'     => $query,
                'limit' => $this->limit
            ));

            $result = array();
            foreach ($posts as $post) {
                $result[] = array(
                    'id'   => $post->get_id(),
                    'text' => $post->get_name(),
                );
            }

            return $result;

        } else {
            return null;
        }
    }

    public function ajax_rebuild_onsale_list()
    {
        Factory::callStaticMethod("External_Shortcodes_OnSaleProducts", 'updateCachedProductsIds', $this->context);
    }

    public function ajax_rebuild_bogo_list()
    {
        Factory::callStaticMethod("External_Shortcodes_BogoProducts", 'updateCachedProductsIds', $this->context);
    }
}
