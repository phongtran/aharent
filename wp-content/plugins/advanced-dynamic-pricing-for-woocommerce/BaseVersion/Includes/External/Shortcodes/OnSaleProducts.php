<?php

namespace ADP\BaseVersion\Includes\External\Shortcodes;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\BaseVersion\Includes\External\SqlGenerator;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OnSaleProducts extends Products
{
    const NAME = 'adp_products_on_sale';
    const STORAGE_KEY = 'wdp_products_onsale';

    protected function set_adp_products_on_sale_query_args(&$queryArgs)
    {
        $queryArgs['post__in'] = array_merge(array(0), static::getCachedProductsIds($this->context));
    }

    /**
     * @param Context $context
     *
     * @return array
     */
    public static function getProductsIds($context)
    {
        global $wpdb;

        $rulesCollection = CacheHelper::loadActiveRules($context);
        $rulesArray      = $context->getOption('rules_apply_mode') !== "none" ? $rulesCollection->getRules() : array();

        /** @var $sqlGenerator SqlGenerator */
        $sqlGenerator = Factory::get("External_SqlGenerator");

        foreach ($rulesArray as $rule) {
            if (self::isSimpleRule($rule)) {
                $sqlGenerator->applyRuleToQuery($context, $rule);
            }
        }

        if ($sqlGenerator->isEmpty()) {
            return array();
        }

        $sql_joins    = $sqlGenerator->getJoin();
        $sql_where    = $sqlGenerator->getWhere();
        $excludeWhere = $sqlGenerator->getExcludeWhere();

        $sql = "SELECT post.ID as id, post.post_parent as parent_id FROM `$wpdb->posts` AS post
			" . implode(" ", $sql_joins) . "
			WHERE post.post_type IN ( 'product', 'product_variation' )
				AND post.post_status = 'publish'
			" . ($sql_where ? " AND " : "") . implode(" OR ", array_map(function ($v) {
                return "(" . $v . ")";
            }, $sql_where)) . ($excludeWhere ? " AND " : "") . implode(" AND ", array_map(function ($v) {
                return "(" . $v . ")";
            }, $excludeWhere)) . "
			GROUP BY post.ID";

        $bogoProducts = $wpdb->get_results($sql);

        $productIdsBogo = wp_parse_id_list(array_merge(wp_list_pluck($bogoProducts, 'id'),
            array_diff(wp_list_pluck($bogoProducts, 'parent_id'), array(0))));

        return $productIdsBogo;
    }

    /**
     * @param Rule $rule
     *
     * @return bool
     */
    protected static function isSimpleRule($rule)
    {
        return
            $rule instanceof SingleItemRule &&
            $rule->getProductAdjustmentHandler() &&
            ! $rule->getProductRangeAdjustmentHandler() &&
            ! $rule->getRoleDiscounts() &&
            count($rule->getGifts()) === 0 &&
            count($rule->getItemGiftsCollection()->asArray()) === 0 &&
            count($rule->getConditions()) === 0 &&
            count($rule->getLimits()) === 0;
    }
}
