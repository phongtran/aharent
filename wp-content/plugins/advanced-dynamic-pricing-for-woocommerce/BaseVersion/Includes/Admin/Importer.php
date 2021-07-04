<?php

namespace ADP\BaseVersion\Includes\Admin;

use \ADP\BaseVersion\Includes\Common\Database;
use \ADP\BaseVersion\Includes\Common\Helpers;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\RuleStorage;
use ADP\Factory;

class Importer
{
    public static function importRules($data, $resetRules)
    {
        if ($resetRules) {
            Database::deleteAllRules();
        }
        $imported = array();
        /** @var RuleStorage $ruleStorage */
        $ruleStorage = Factory::get("External_RuleStorage", new Context());
        $rulesCol    = $ruleStorage->buildRules($data);
        $exporter    = Factory::get("Admin_Exporter", new Context());

        foreach ($rulesCol->getRules() as $ruleObject) {
            $rule = $exporter->convertRule($ruleObject);
            //unset( $rule['id'] );

            $rule['enabled'] = (isset($rule['enabled']) && $rule['enabled'] === 'on') ? 1 : 0;

            if ( ! empty($rule['filters'])) {
                foreach ($rule['filters'] as &$item) {
                    $item['value'] = isset($item['value']) ? $item['value'] : array();
                    $item['value'] = self::convertElementsFromNameToId($item['value'], $item['type']);
                }
                unset($item);
            }

            if ( ! empty($rule['get_products']['value'])) {
                foreach ($rule['get_products']['value'] as &$item) {
                    $item['value'] = isset($item['value']) ? $item['value'] : array();
                    $item['value'] = self::convertElementsFromNameToId($item['value'], $item['type']);
                }
                unset($item);
            }

            if ( ! empty($rule['conditions'])) {
                foreach ($rule['conditions'] as &$item) {
                    if ( ! isset($item['options'][2])) {
                        continue;
                    }

                    $item['options'][2] = self::convertElementsFromNameToId($item['options'][2], $item['type']);
                }
                unset($item);
            }

            $attributes = array(
                'options',
                'conditions',
                'filters',
                'limits',
                'cart_adjustments',
                'product_adjustments',
                'bulk_adjustments',
                'role_discounts',
                'get_products',
                'sortable_blocks_priority',
                'additional',
            );
            foreach ($attributes as $attr) {
                $rule[$attr] = serialize(isset($rule[$attr]) ? $rule[$attr] : array());
            }

            $imported[] = Database::storeRule($rule);
        }

        return $imported;
    }

    /**
     * @param array|string $items
     * @param string $type
     *
     * @return array|string
     */
    protected static function convertElementsFromNameToId($items, $type)
    {
        if (empty($items) || ! is_array($items)) {
            return $items;
        }
        foreach ($items as &$value) {
            if ('products' === $type) {
                $value = Helpers::getProductId($value);
            } elseif ('product_categories' === $type) {
                $value = Helpers::getCategoryId($value);
            } elseif ('product_tags' === $type) {
                $value = Helpers::getTagId($value);
            } elseif ('product_attributes' === $type) {
                $value = Helpers::getAttributeId($value);
            }

            if (empty($value)) {
                $value = 0;
            }
        }

        return $items;
    }


}
