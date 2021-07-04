<?php

namespace ADP\BaseVersion\Includes\External;

use ADP\BaseVersion\Includes\Common\Helpers;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SqlGenerator
{
    protected $appliedRules = array();

    /**
     * @var array
     */
    protected $join = array();

    /**
     * @var array
     */
    protected $where = array();

    /**
     * @var array
     */
    protected $excludeWhere = array();

    /**
     * @var array
     */
    protected $customTaxonomies = array();

    public function __construct()
    {
        $this->customTaxonomies = array_values(array_map(function ($tax) {
            return $tax->name;
        }, Helpers::getCustomProductTaxonomies()));
    }

    /**
     * @param Context $context
     * @param SingleItemRule $rule
     *
     * @return bool
     */
    public function applyRuleToQuery($context, SingleItemRule $rule)
    {
        $filters = $rule->getFilters();
        if ( ! $filters) {
            return false;
        }

        $filter = reset($filters);

        if ( ! $filter->isValid()) {
            return false;
        }

        $generated = $this->generateFilterSqlByType($filter->getType(), $filter->getValue());

        if ( ! empty($generated['where'])) {
            $this->where[] = $generated['where'];
        }

        if ($context->getOption('allow_to_exclude_products') && $filter->getExcludeProductIds()) {
            $ids                  = "( '" . implode("','",
                    array_map('esc_sql', $filter->getExcludeProductIds())) . "' )";
            $this->excludeWhere[] = "post.ID NOT IN {$ids} AND post.post_parent NOT IN {$ids}";
        }

        $this->appliedRules[] = $rule;

        return true;
    }

    public function getJoin()
    {
        return $this->join;
    }

    public function getWhere()
    {
        return $this->where;
    }

    public function getExcludeWhere()
    {
        return $this->excludeWhere;
    }

    protected function generateFilterSqlByType($type, $value)
    {
        if (in_array($type, $this->customTaxonomies)) {
            return $this->genSqlCustomTaxonomy($type, $value);
        }

        $method_name = "genSql" . ucfirst($type);

        return method_exists($this, $method_name) ? call_user_func(array($this, $method_name), $value) : false;
    }

    protected function genSqlProducts($productIds)
    {
        $where = array();

        $ids_sql_in = "( '" . implode("','", array_map('esc_sql', $productIds)) . "' )";

        $where[] = "post.ID IN {$ids_sql_in} OR post.post_parent IN {$ids_sql_in}";

        return array(
            'where' => implode(" ", $where),
        );
    }

    protected function addJoin($sqlJoin)
    {
        $hash = md5($sqlJoin);
        if ( ! isset($this->join[$hash])) {
            $this->join[$hash] = $sqlJoin;
        }
    }

    protected function genSqlProduct_sku($skus)
    {
        global $wpdb;

        $skus_sql_in = "( '" . implode("','", array_map('esc_sql', $skus)) . "' )";

        $this->addJoin("LEFT JOIN {$wpdb->postmeta} as postmeta_1 ON post.ID = postmeta_1.post_id");

        $where   = array();
        $where[] = "postmeta_1.meta_key = '_sku'";
        $where[] = "postmeta_1.meta_value IN {$skus_sql_in}";

        return array(
            'where' => "(" . implode(" AND ", $where) . ")",
        );
    }

    protected function genSqlProduct_sellers($sellers)
    {
        global $wpdb;

        $sellers_sql_in = "( '" . implode("','", array_map('esc_sql', $sellers)) . "' )";

        $where   = array();
        $where[] = "post.post_author IN {$sellers_sql_in}";

        return array(
            'where' => "(" . implode(" AND ", $where) . ")",
        );
    }

    protected function genSqlProduct_tags($tags)
    {
        return $this->genSqlByTermIds('product_tag', $tags);
    }

    protected function genSqlProduct_categories($categories)
    {
        return $this->genSqlByTermIds('product_cat', $categories);
    }

    protected function genSqlProduct_category_slug($categorySlugs)
    {
        global $wpdb;
        $where = array();

        $category_slugs_sql_in = "( '" . implode("','", array_map('esc_sql', $categorySlugs)) . "' )";

        $this->addJoin("LEFT JOIN {$wpdb->term_relationships} as term_rel_1 ON post.ID = term_rel_1.object_id");
        $this->addJoin("LEFT JOIN {$wpdb->term_taxonomy} as term_tax_1 ON term_rel_1.term_taxonomy_id = term_tax_1.term_taxonomy_id");
        $this->addJoin("LEFT JOIN {$wpdb->terms} as term_1 ON term_tax_1.term_id = term_1.term_id");

        $where[] = "term_1.slug IN {$category_slugs_sql_in}";

        return array(
            'where' => implode(" ", $where),
        );
    }

    protected function genSqlProduct_custom_fields($values)
    {
        global $wpdb;
        $where = array();

        $custom_fields = array();
        foreach ($values as $value) {
            $value = explode("=", $value);
            if (count($value) !== 2) {
                continue;
            }
            $custom_fields[] = array(
                'key'   => $value[0],
                'value' => $value[1],
            );
        }

        $this->addJoin("LEFT JOIN {$wpdb->postmeta} as postmeta_1 ON post.ID = postmeta_1.post_id");

        $tmp_where = [];
        foreach ($custom_fields as $custom_field) {
            $tmp_where[] = "postmeta_1.meta_key='{$custom_field['key']}' AND postmeta_1.meta_value='{$custom_field['value']}'";
        }

        $where[] = "( " . implode(" OR ", $tmp_where) . " )";


        return array(
            'where' => implode(" ", $where),
        );
    }

    protected function genSqlProduct_attributes($attributes)
    {
        return $this->genSqlByTermIds('product_attr', $attributes);
    }

    protected function genSqlCustomTaxonomy($taxName, $values)
    {
        return $this->genSqlByTermIds($taxName, $values);
    }

    protected function genSqlByTermIds($taxName, $termIds)
    {
        $term_ids_sql_in = "( '" . implode("','", array_map('esc_sql', $termIds)) . "' )";

        global $wpdb;
        $where = array();

        $relationshipTableName = "term_rel_$taxName";
        $taxTableName          = "term_tax_$taxName";

        $this->addJoin( "LEFT JOIN {$wpdb->term_relationships} as {$relationshipTableName} ON post.ID = {$relationshipTableName}.object_id" );
        $this->addJoin( "LEFT JOIN {$wpdb->term_taxonomy} as {$taxTableName} ON {$relationshipTableName}.term_taxonomy_id = {$taxTableName}.term_taxonomy_id" );

        $where[] = "{$taxTableName}.term_id IN {$term_ids_sql_in}";

        return array(
            'where' => implode(" ", $where),
        );
    }

    protected function genSqlAny()
    {
        return array(
            'where' => array(),
        );
    }

    public function isEmpty()
    {
        return count($this->appliedRules) === 0;
    }
}
