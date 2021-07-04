<?php

namespace ADP\BaseVersion\Includes\Common;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Database
{
    const TABLE_RULES = 'wdp_rules';
    const TABLE_ORDER_RULES = 'wdp_orders';
    const TABLE_ORDER_ITEMS_RULES = 'wdp_order_items';

    public static function create_database()
    {
        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $charsetCollate = $wpdb->get_charset_collate();

        // Table for Rulles (discounts)
        $rulesTableName = $wpdb->prefix . self::TABLE_RULES;

        $sql = /** @lang MySQL */
            "CREATE TABLE {$rulesTableName} (
            id INT NOT NULL AUTO_INCREMENT,
            deleted TINYINT(1) DEFAULT 0,
            enabled TINYINT(1) DEFAULT 1,
            exclusive TINYINT(1) DEFAULT 0,
            type VARCHAR(50),
            title VARCHAR(255),
            priority INT,
            options TEXT,
            additional TEXT,
            conditions TEXT,
            filters TEXT,
            limits TEXT,
            product_adjustments TEXT,
            sortable_blocks_priority TEXT,
            bulk_adjustments TEXT,
            role_discounts TEXT,
            cart_adjustments TEXT,
            get_products TEXT,
            PRIMARY KEY  (id),
            KEY deleted (deleted),
            KEY enabled (enabled)
        ) $charsetCollate;";
        dbDelta($sql);

        $orderRulesTableName = $wpdb->prefix . self::TABLE_ORDER_RULES;

        // Table for history of applied rules
        $sql = /** @lang MySQL */
            "CREATE TABLE {$orderRulesTableName} (
            id INT NOT NULL AUTO_INCREMENT,
            order_id INT NOT NULL,
            rule_id INT NOT NULL,
            amount DECIMAL(50,2) DEFAULT 0,
            qty INT DEFAULT 0,
            extra DECIMAL(50,2) DEFAULT 0,
            shipping DECIMAL(50,2) DEFAULT 0,
            is_free_shipping TINYINT(1) DEFAULT 0,
            gifted_amount DECIMAL(50,2) DEFAULT 0,
            gifted_qty INT DEFAULT 0,
            date DATETIME,
            PRIMARY KEY  (id),
            UNIQUE KEY order_id (order_id, rule_id),
            KEY rule_id (rule_id),
            KEY date (date)
        ) $charsetCollate;";
        dbDelta($sql);

        $orderItemsRulesTableName = $wpdb->prefix . self::TABLE_ORDER_ITEMS_RULES;

        // Table for history of applied rules
        $sql = /** @lang MySQL */
            "CREATE TABLE {$orderItemsRulesTableName} (
            id INT NOT NULL AUTO_INCREMENT,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            rule_id INT NOT NULL,
            amount DECIMAL(50,2) DEFAULT 0,
            qty INT DEFAULT 0,
            gifted_amount DECIMAL(50,2) DEFAULT 0,
            gifted_qty INT DEFAULT 0,
            date DATETIME,
            PRIMARY KEY  (id),
            UNIQUE KEY order_id (order_id, rule_id, product_id),
            KEY rule_id (rule_id),
            KEY product_id (product_id),
            KEY date (date)
        ) $charsetCollate;";
        dbDelta($sql);
    }

    public static function deleteDatabase()
    {
        global $wpdb;

        $rules_table_name = $wpdb->prefix . self::TABLE_RULES;
        $wpdb->query("DROP TABLE IF EXISTS $rules_table_name");

        $order_rules_table_name = $wpdb->prefix . self::TABLE_ORDER_RULES;
        $wpdb->query("DROP TABLE IF EXISTS $order_rules_table_name");

        $order_rules_items_table_name = $wpdb->prefix . self::TABLE_ORDER_ITEMS_RULES;
        $wpdb->query("DROP TABLE IF EXISTS $order_rules_items_table_name");
    }

    /**
     * get_rules
     *
     * @param array $args ( array|string types, bool active_only, bool include_deleted, bool exclusive, int|array id )
     *
     * @return array filtered rules
     */
    public static function getRules($args = array()): array
    {
//    	return self::get_test_rules();
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_RULES;

        $sql = "SELECT * FROM $table WHERE 1 ";

        if (isset($args['types'])) {
            $types        = (array)$args['types'];
            $placeholders = array_fill(0, count($types), '%s');
            $placeholders = implode(', ', $placeholders);
            $sql          = $wpdb->prepare("$sql AND type IN($placeholders)", $types);
        }

        $active_only = isset($args['active_only']) && $args['active_only'];
        if ($active_only) {
            $sql .= ' AND enabled = 1';
        }

        $include_deleted = isset($args['include_deleted']) && $args['include_deleted'];
        if ( ! $include_deleted) {
            $sql .= ' AND deleted = 0';
        }

        if (isset($args['exclusive'])) {
            $showExclusive = $args['exclusive'] ? 1 : 0;
            $sql           = "$sql AND exclusive = $showExclusive";
        }

        if (isset($args['id'])) {
            $ids          = (array)$args['id'];
            $placeholders = array_fill(0, count($ids), '%d');
            $placeholders = implode(', ', $placeholders);
            $sql          = $wpdb->prepare("$sql AND id IN($placeholders)", $ids);
        }

        $sql .= " ORDER BY exclusive DESC, priority";

        if (isset($args['limit'])) {
            $sql_limit = "";
            $limit     = $args['limit'];

            $count = null;
            $start = null;

            if (is_string($limit)) {
                $count = $limit;
            } elseif (is_array($limit)) {
                if (1 === count($limit)) {
                    $count = reset($limit);
                } elseif (2 === count($limit)) {
                    list($start, $count) = $limit;
                }
            }

            if ( ! is_null($count)) {
                $count = (integer)$count;
                if ( ! is_null($start)) {
                    $start     = (integer)$start;
                    $sql_limit = sprintf("LIMIT %d, %d", $start, $count);
                } else {
                    $sql_limit = sprintf("LIMIT %d", $count);
                }
            }

            $sql .= " " . $sql_limit;
        }

        $rows = $wpdb->get_results($sql);

        $rows = array_map(function ($item) {
            $result = array(
                'id'                       => $item->id,
                'title'                    => $item->title,
                'type'                     => $item->type,
                'exclusive'                => $item->exclusive,
                'priority'                 => $item->priority,
                'enabled'                  => $item->enabled ? 'on' : 'off',
                'options'                  => unserialize($item->options),
                'additional'               => unserialize($item->additional),
                'conditions'               => unserialize($item->conditions),
                'filters'                  => unserialize($item->filters),
                'limits'                   => unserialize($item->limits),
                'product_adjustments'      => unserialize($item->product_adjustments),
                'sortable_blocks_priority' => unserialize($item->sortable_blocks_priority),
                'bulk_adjustments'         => unserialize($item->bulk_adjustments),
                'role_discounts'           => unserialize($item->role_discounts),
                'cart_adjustments'         => unserialize($item->cart_adjustments),
                'get_products'             => unserialize($item->get_products),
            );
            $result = self::decodeArrayTextFields($result);

            return $result;
        }, $rows);

        if (isset($args['product'])) {
            $new_rows              = array();
            $filters_to_check      = array_column($rows, "filters");
            $sellerRulesExist      = false;
            $customFieldsRuleExist = false;
            array_map(function ($ruleFilters) use (&$sellerRulesExist, &$customFieldsRuleExist) {
                $rulesFiltersValues = array_values(array_column($ruleFilters, "type"));
                if ($sellerRulesExist === false && in_array("product_sellers", $rulesFiltersValues)) {
                    $sellerRulesExist = true;
                }
                if ($customFieldsRuleExist === false && in_array("product_custom_fields", $rulesFiltersValues)) {
                    $customFieldsRuleExist = true;
                }

                return $ruleFilters;
            }, $filters_to_check);
            if ($sellerRulesExist) {
                $productSellers = array_column(Helpers::getUsers(array()), 'id');
            }
            if ($customFieldsRuleExist) {
                $customFields = array_column(Helpers::getProductCustomFields($args['product']),
                    'id');
            }
            foreach ($rows as $row) {
                foreach ($row['filters'] as $filter) {
                    switch ($filter['type']) {
                        case 'products':
                            foreach ($filter['value'] as $value) {
                                if ((integer)$value == $args['product'] || (isset($args["product_childs"]) && in_array((integer)$value,
                                            $args["product_childs"]))) {
                                    $new_rows[] = $row;
                                    break 3;
                                }
                            }
                            break 2;
                        case 'product_sku':
                            foreach ($filter['value'] as $value) {
                                if (isset($args[$filter['type']]) && strcmp((string)$value,
                                        $args[$filter['type']]) === 0) {
                                    $new_rows[] = $row;
                                    break 3;
                                }
                            }
                            break 2;
                        case 'product_categories':
                        case 'product_attributes':
                        case 'product_tags':
                            foreach ($filter['value'] as $value) {
                                if (isset($args[$filter['type']]) && in_array((integer)$value,
                                        $args[$filter['type']])) {
                                    $new_rows[] = $row;
                                    break 3;
                                }
                            }
                            break 2;
                        case 'product_sellers':
                            foreach ($filter['value'] as $value) {
                                if ( ! empty($productSellers) && in_array((integer)$value, $productSellers)) {
                                    $new_rows[] = $row;
                                    break 3;
                                }
                            }
                            break 2;
                        case 'product_custom_fields':
                            foreach ($filter['value'] as $value) {
                                if ( ! empty($customFields) && in_array((string)$value, $customFields)) {
                                    $new_rows[] = $row;
                                    break 3;
                                }
                            }
                            break 2;
                        case 'product_category_slug':
                            foreach ($filter['value'] as $value) {
                                if (isset($args["product_category_slug"]) && in_array((string)$value,
                                        $args['product_category_slug'])) {
                                    $new_rows[] = $row;
                                    break 3;
                                }
                            }
                            break 2;
                        default:
                            break 1;
                    }
                }
            }
            $rows = $new_rows;
        }

        foreach ($rows as &$row) {
            $row = self::validateBulkAdjustments($row);
        }

        $rows = self::migrateTo_2_2_3($rows);

        $countryStates = WC()->countries->get_states();

        // fix collections in conditions
        foreach ($rows as &$row) {
            foreach ($row['conditions'] as &$condition) {
                $type    = $condition['type'];
                $options = &$condition['options'];

                if ($type === 'product_collections') {
                    $options = array($options[0], $options[2], $options[3]);
                } elseif ($type === 'amount_product_collections') {
                    $options = array($options[1], $options[2], $options[3]);
                } elseif ($type === 'shipping_state') {
                    $comparison_value = $condition['options'][1];

                    $newComparisonValue = array();
                    $changed              = false;

                    foreach ($comparison_value as $value) {
                        if (strpos($value, ':') === false) {
                            foreach ($countryStates as $country_code => $states) {
                                if (isset($states[$value])) {
                                    $newComparisonValue[] = $country_code . ":" . $value;
                                    $changed                = true;
                                }
                            }
                        }
                    }

                    if ($changed) {
                        $condition['options'][1] = $newComparisonValue;
                    }
                }
            }
        }

        $lastCustomTaxonomy = Helpers::getCustomProductTaxonomies();
        $lastCustomTaxonomy = end($lastCustomTaxonomy);

        foreach ($rows as &$row) {
            foreach ($row['conditions'] as &$condition) {
                $type    = $condition['type'];

                if ($type === 'custom_taxonomy' || $type === 'amount_custom_taxonomy') {
                    $condition['type'] = $condition['type'] . '_' . $lastCustomTaxonomy->name;
                }
            }
        }

        return $rows;
    }

    private static function validateBulkAdjustments($row)
    {
        if (empty($row['bulk_adjustments']['ranges'])) {
            return $row;
        }

        $ranges = $row['bulk_adjustments']['ranges'];
        $ranges = array_values(array_filter(array_map(function ($range) {
            return isset($range['to'], $range['from'], $range['value']) ? $range : false;
        }, $ranges)));


        usort($ranges, function ($a, $b) {
            if ($a["to"] === '' && $b["to"] === '') {
                return 0;
            } elseif ($a["to"] === '') {
                return 1;
            } elseif ($b["to"] === '') {
                return -1;
            }

            return (integer)$a["to"] - (integer)$b["to"];
        });

        $previousRange = null;
        foreach ($ranges as &$range) {
            $from = $range['from'];
            if ($from === '') {
                if (is_null($previousRange)) {
                    $from = 1;
                } else {
                    if ($previousRange['to'] !== '') {
                        $from = (integer)$previousRange['to'] + 1;
                    }
                }
            }
            $range['from']  = $from;
            $previousRange = $range;
        }

        $row['bulk_adjustments']['ranges'] = $ranges;

        return $row;
    }

    private static function migrateTo_2_2_3($rows)
    {
        // add selector "in_list/not_in_list" for amount conditions
        foreach ($rows as &$row) {
            foreach ($row['conditions'] as &$condition) {
                if ('amount_' === substr($condition['type'], 0,
                        strlen('amount_')) && 3 === count($condition['options'])) {
                    array_unshift($condition['options'], 'in_list');
                }
            }
        }

        return $rows;
    }

    /**
     * @param array $args
     *
     * @return int|null
     */
    public static function getRulesCount($args = array())
    {
        //    	return self::get_test_rules();
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_RULES;

        $sql = "SELECT COUNT(*) FROM $table WHERE 1 ";

        if (isset($args['types'])) {
            $types        = (array)$args['types'];
            $placeholders = array_fill(0, count($types), '%s');
            $placeholders = implode(', ', $placeholders);
            $sql          = $wpdb->prepare("$sql AND type IN($placeholders)", $types);
        }

        $active_only = isset($args['active_only']) && $args['active_only'];
        if ($active_only) {
            $sql .= ' AND enabled = 1';
        }

        $include_deleted = isset($args['include_deleted']) && $args['include_deleted'];
        if ( ! $include_deleted) {
            $sql .= ' AND deleted = 0';
        }

        if (isset($args['exclusive'])) {
            $showExclusive = $args['exclusive'] ? 1 : 0;
            $sql            = "$sql AND exclusive = $showExclusive";
        }

        if (isset($args['id'])) {
            $ids          = (array)$args['id'];
            $placeholders = array_fill(0, count($ids), '%d');
            $placeholders = implode(', ', $placeholders);
            $sql          = $wpdb->prepare("$sql AND id IN($placeholders)", $ids);
        }

        return (integer)$wpdb->get_var($sql);
    }

    private static function decodeArrayTextFields($array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = self::decodeArrayTextFields($value);
            } else {
                $value = trim(htmlspecialchars_decode($value));
            }
        }

        return $array;
    }

    public static function deleteAllRules()
    {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_RULES;
        $sql   = "DELETE FROM $table";
        $wpdb->query($sql);
    }

    public static function markRulesAsDeleted($type)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_RULES;

        $sql = "UPDATE $table SET deleted = 1 WHERE type ";
        if (is_array($type)) {
            $format = implode(', ', array_fill(0, count($type), '%s'));
            $sql    = $wpdb->prepare("$sql IN ($format)", $type);
        } else {
            $sql = $wpdb->prepare("$sql = %s", $type);
        }

        $wpdb->query($sql);
    }

    public static function markRuleAsDeleted($rule_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_RULES;

        $data  = array('deleted' => 1);
        $where = array('id' => $rule_id);
        $wpdb->update($table, $data, $where);
    }

    public static function storeRule($data, $id = null)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_RULES;

        if ( ! empty($id)) {
            $where  = array('id' => $id);
            $result = $wpdb->update($table, $data, $where);

            return $id;
        } else {
            $result = $wpdb->insert($table, $data);

            return $wpdb->insert_id;
        }
    }

    public static function addOrderStats($data)
    {
        global $wpdb;

        $table = $wpdb->prefix . self::TABLE_ORDER_RULES;

        $data = array_merge(array(
            'order_id'         => 0,
            'rule_id'          => 0,
            'amount'           => 0,
            'extra'            => 0,
            'shipping'         => 0,
            'is_free_shipping' => 0,
            'gifted_amount'    => 0,
            'gifted_qty'       => 0,
            'date'             => 0,
        ), $data);

        $wpdb->replace($table, $data);
    }

    public static function addProductStats($data)
    {
        global $wpdb;

        $table = $wpdb->prefix . self::TABLE_ORDER_ITEMS_RULES;

        $data = array_merge(array(
            'order_id'      => 0,
            'product_id'    => 0,
            'rule_id'       => 0,
            'amount'        => 0,
            'qty'           => 0,
            'gifted_amount' => 0,
            'gifted_qty'    => 0,
            'date'          => 0,
        ), $data);

        $wpdb->replace($table, $data);
    }

    /**
     * @param $orderId
     *
     * @return array
     */
    public static function getAppliedRulesForOrder($orderId)
    {
        global $wpdb;

        $table_order_rules = $wpdb->prefix . self::TABLE_ORDER_RULES;
        $table_rules       = $wpdb->prefix . self::TABLE_RULES;

        $sql = $wpdb->prepare("
            SELECT *
            FROM $table_order_rules LEFT JOIN $table_rules ON $table_order_rules.rule_id = $table_rules.id
            WHERE order_id = %d
            ORDER BY amount DESC
        ", $orderId);

        $rows = $wpdb->get_results($sql);

        return $rows;
    }

    public static function getCountOfRuleUsages($rule_id)
    {
        global $wpdb;

        $table_order_rules = $wpdb->prefix . self::TABLE_ORDER_RULES;

        $sql = $wpdb->prepare("
            SELECT COUNT(*)
            FROM {$table_order_rules}
            WHERE rule_id = %d
        ", $rule_id);

        $value = $wpdb->get_var($sql);

        return (integer)$value;
    }

    public static function getCountOfRuleUsagesPerCustomer($ruleId, $customerId)
    {
        global $wpdb;

        $tableOrderRules = $wpdb->prefix . self::TABLE_ORDER_RULES;

        $customerOrdersIds = get_posts(array(
            'fields'      => 'ids',
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $customerId,
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys(wc_get_order_statuses()),
        ));
        if (empty($customerOrdersIds)) {
            return 0;
        }

        $value = $wpdb->get_var("SELECT COUNT(*) FROM {$tableOrderRules}
		            WHERE rule_id = $ruleId  AND order_id IN (" . implode(',', $customerOrdersIds) . ")");

        return (integer)$value;
    }

    public static function markAsDisabledByPlugin($ruleId)
    {
        global $wpdb;

        $tableRules = $wpdb->prefix . self::TABLE_RULES;

        $sql = $wpdb->prepare("
            SELECT {$tableRules}.additional
            FROM {$tableRules}
            WHERE 'rule_id' = %d
        ", $ruleId);

        $additional                       = $wpdb->get_var($sql);
        $additional                       = unserialize($additional);
        $additional['disabled_by_plugin'] = 1;

        $data  = array('enabled' => 0, 'additional' => serialize($additional));
        $where = array('id' => $ruleId);
        $wpdb->update($tableRules, $data, $where);
    }

    public static function deleteConditionsFromDbByTypes($types)
    {

        $rules = array_merge(self::getRules(), self::getRules(array(
            'exclusive' => true,
        )));

        foreach ($rules as $keyRule => $rule) {
            if (isset($rule['conditions'])) {
                $conditions = $rule['conditions'];
            } else {
                continue;
            }
            foreach ($conditions as $keyCondition => $condition) {
                if (in_array($condition['type'], $types)) {
                    unset($conditions[$keyCondition]);
                }
            }
            $conditions = array_values($conditions);

            $data = array('conditions' => serialize($conditions));
            self::storeRule($data, $rule['id']);
        }
    }

    public static function isConditionTypeActive($types)
    {
        $rules = array_merge(self::getRules(array(
            'active_only' => true,
        )), self::getRules(array(
            'exclusive'   => true,
            'active_only' => true,
        )));

        foreach ($rules as $key_rule => $rule) {
            if (isset($rule['conditions'])) {
                $conditions = $rule['conditions'];
            } else {
                continue;
            }
            foreach ($conditions as $key_condition => $condition) {
                if (in_array($condition['type'], $types)) {
                    return true;
                }
            }

        }

        return false;
    }

    public static function disableRule($ruleId)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE_RULES;

        $data  = array('enabled' => 0);
        $where = array('id' => $ruleId);
        $wpdb->update($table, $data, $where);
    }

    public static function getOnlyRequiredChildPostMetaData($parentId)
    {
        global $wpdb;

        $requiredKeys = array(
            '_sale_price',
            '_regular_price',
            '_sale_price_dates_from',
            '_sale_price_dates_to',
            '_tax_status',
            '_tax_class',
            '_sku',
        );
        $requiredKeys = '"' . implode('","', $requiredKeys) . '"';

        $meta_list = $wpdb->get_results("
			SELECT post_id, meta_key, meta_value
			FROM $wpdb->postmeta
			WHERE
				post_id IN (SELECT ID FROM $wpdb->posts WHERE post_parent = $parentId )
				AND
				(meta_key IN ( $requiredKeys ) OR meta_key LIKE 'attribute_%')
			ORDER BY post_id ASC", ARRAY_A);

        $post_data = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = $parentId ", OBJECT_K);

        $required_data = array();

        foreach ($post_data as $post_datum) {
            $post_datum->meta = array();

            $required_data[$post_datum->ID] = $post_datum;
        }

        foreach ($meta_list as $row) {
            $value                                                  = maybe_unserialize($row['meta_value']);
            $required_data[$row['post_id']]->meta[$row['meta_key']] = $value;
        }

        return $required_data;
    }
}
