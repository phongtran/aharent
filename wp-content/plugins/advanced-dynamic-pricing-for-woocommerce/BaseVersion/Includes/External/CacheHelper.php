<?php

namespace ADP\BaseVersion\Includes\External;

use ADP\BaseVersion\Includes\Cart\CartCalculator;
use ADP\BaseVersion\Includes\Cart\RulesCollection;
use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\External\WP\WpObjectCache;
use ADP\BaseVersion\Includes\Product\ProcessedProductSimple;
use ADP\BaseVersion\Includes\Product\ProcessedVariableProduct;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\Factory;
use WC_Product;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CacheHelper
{
    const KEY_ACTIVE_RULES_COLLECTION = 'adp_active_rule_collection';
    const KEY_ALREADY_LOADED_VARIABLES = 'adp_already_loaded_variables';
    const GROUP_RULES_CACHE = 'adp_rules';
    const GROUP_VARIATION_PROD_DATA_CACHE = 'adp_variation_product_data';
    const GROUP_PROCESSED_PRODUCTS_TO_DISPLAY = 'adp_processed_products_to_display';
    const GROUP_WC_PRODUCT = 'adp_wc_product';

    public static $objCache;

    /**
     * @return true
     */
    public static function flush()
    {
        if ( ! isset(self::$objCache)) {
            self::$objCache = new WpObjectCache();
        }

        return self::$objCache->flush();
    }

    public static function cacheGet($key, $group = '', $force = false, &$found = null)
    {
        if ( ! isset(self::$objCache)) {
            self::$objCache = new WpObjectCache();
        }

        return self::$objCache->get($key, $group, $force, $found);
    }

    public static function cacheSet($key, $data, $group = '', $expire = 0)
    {
        if ( ! isset(self::$objCache)) {
            self::$objCache = new WpObjectCache();
        }

        return self::$objCache->set($key, $data, $group, (int)$expire);
    }

    /**
     * @param Context $context
     *
     * @return RulesCollection
     */
    public static function loadActiveRules($context = null): RulesCollection
    {
        $rulesCollection = self::cacheGet(self::KEY_ACTIVE_RULES_COLLECTION);

        if ($rulesCollection instanceof RulesCollection) {
            return $rulesCollection;
        }

        if (is_null($context)) {
            $context = new Context();
        }

        /** @var RuleStorage $storage */
        $storage         = Factory::get("External_RuleStorage", $context);
        $rows            = Database::getRules(array('active_only' => true));
        $rulesCollection = $storage->buildRules($rows);

        self::cacheSet(self::KEY_ACTIVE_RULES_COLLECTION, $rulesCollection);
        self::addRulesToCache($rulesCollection->getRules());

        return $rulesCollection;
    }

    /**
     * @param array<int, int> $ruleIds
     * @param Context $context
     *
     * @return array<int, Rule>
     */
    public static function loadRules($ruleIds, Context $context = null)
    {
        $ruleIds = (array)$ruleIds;
        $ruleIds = array_map('intval', $ruleIds);

        if (count($ruleIds) === 0) {
            return array();
        }

        $rules            = array();
        $notCachedRuleIds = array();

        foreach ($ruleIds as $ruleId) {
            $rule = self::cacheGet($ruleId, self::GROUP_RULES_CACHE);

            if ($rule instanceof Rule) {
                $rules[$rule->getId()] = $rule;
            } else {
                $notCachedRuleIds[] = $ruleId;
            }
        }

        if (count($notCachedRuleIds) === 0) {
            return $rules;
        }

        if (is_null($context)) {
            $context = new Context();
        }

        $storage         = Factory::get("External_RuleStorage", $context);
        $rows            = Database::getRules(array('id' => $notCachedRuleIds));
        $rulesCollection = $storage->buildRules($rows);
        $rules           = $rulesCollection->getRules();
        self::addRulesToCache($rules);

        return $rules;
    }

    /**
     * @param array<int, Rule> $rules
     */
    protected static function addRulesToCache($rules)
    {
        foreach ($rules as $rule) {
            self::cacheSet($rule->getId(), $rule, self::GROUP_RULES_CACHE);
        }
    }

    /**
     * @param int $variableProductId
     */
    public static function loadVariationsPostMeta($variableProductId)
    {
        $loadedIds = self::cacheGet(self::KEY_ALREADY_LOADED_VARIABLES);

        if ($loadedIds === false) {
            $loadedIds = array();
        }

        if (in_array($variableProductId, $loadedIds)) {
            return;
        }

        $productsData = Database::getOnlyRequiredChildPostMetaData($variableProductId);

        foreach ($productsData as $productId => $data) {
            self::cacheSet($productId, $data, self::GROUP_VARIATION_PROD_DATA_CACHE);
        }

        $loadedIds[] = $variableProductId;
        self::cacheSet(self::KEY_ALREADY_LOADED_VARIABLES, $loadedIds);
    }

    /**
     * @param int $productId
     *
     * @return array|object
     */
    public static function getVariationProductData($productId)
    {
        $productMeta = self::cacheGet($productId, self::GROUP_VARIATION_PROD_DATA_CACHE);

        if (false === $productMeta) {
            $productMeta = get_post_meta($productId);
            array_walk($productMeta, function (&$item) {
                if (is_array($item)) {
                    $item = reset($item);
                }

                $item = maybe_unserialize($item);

                return $item;
            });

            self::cacheSet($productId, $productMeta, self::GROUP_VARIATION_PROD_DATA_CACHE);
        }

        return $productMeta;
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    public static function getVariationProductMeta($productId)
    {
        $product_data = self::getVariationProductData($productId);

        return $product_data ? $product_data->meta : array();
    }

    public static function flushRulesCache()
    {
        global $wp_object_cache;

        if ($wp_object_cache instanceof WpObjectCache) {
            // I have no idea how to delete cache group another way
            $cache = $wp_object_cache->cache;
            unset($cache[self::GROUP_RULES_CACHE]);
            $wp_object_cache->cache = $cache;

            $wp_object_cache->delete(self::KEY_ACTIVE_RULES_COLLECTION);
        } else {
            $wp_object_cache->flush();
        }
    }

    /**
     * @param int $productId
     * @param array $variationAttributes
     * @param float $qty
     * @param array $cartItemData
     * @param Cart $cart
     * @param CartCalculator $calc
     *
     * @return ProcessedProductSimple|ProcessedVariableProduct|null
     */
    public static function maybeGetProcessedProductToDisplay(
        $productId,
        $variationAttributes,
        $qty,
        $cartItemData,
        $cart,
        $calc
    ) {
        $hash      = self::calcHashProcessedProduct($productId, $variationAttributes, $qty, $cartItemData, $cart,
            $calc);
        $processed = self::cacheGet($hash, self::GROUP_PROCESSED_PRODUCTS_TO_DISPLAY);

        return $processed !== false ? $processed : null;
    }

    /**
     * @param WcCartItemFacade $cartItem
     * @param ProcessedProductSimple|null $processed
     * @param Cart $cart
     * @param CartCalculator $calc
     */
    public static function addProcessedProductToDisplay(
        WcCartItemFacade $cartItem,
        ProcessedProductSimple $processed,
        Cart $cart,
        CartCalculator $calc
    ) {
        $productId           = $cartItem->getVariationId() ? $cartItem->getVariationId() : $cartItem->getProductId();
        $qty                 = $cartItem->getQty();
        $cartItemData        = $cartItem->getThirdPartyData();
        $product             = $cartItem->getProduct();
        $variationAttributes = $product instanceof \WC_Product_Variation ? $product->get_variation_attributes() : array();
        $hash                = self::calcHashProcessedProduct($productId, $variationAttributes, $qty, $cartItemData,
            $cart, $calc);
        self::cacheSet($hash, $processed, self::GROUP_PROCESSED_PRODUCTS_TO_DISPLAY);
    }

    /**
     * @param int $productId
     * @param array $variationAttributes
     * @param float $qty
     * @param array $cartItemData
     * @param Cart $cart
     * @param CartCalculator $calc
     *
     * @return string
     */
    protected static function calcHashProcessedProduct(
        $productId,
        $variationAttributes,
        $qty,
        $cartItemData,
        $cart,
        $calc
    ) {
        $parts = array($productId, $qty);

        foreach ($variationAttributes as $key => $value) {
            $parts[] = trim($key) . trim($value);
        }

        if (is_array($cartItemData) && ! empty($cartItemData)) {
            $cartItemDataKey = '';
            foreach ($cartItemData as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = http_build_query($value);
                }
                $cartItemDataKey .= trim($key) . trim($value);

            }
            $parts[] = $cartItemDataKey;
        }

        foreach ($cart->getItems() as $item) {
            $parts[] = $item->getHash();
        }

        foreach ($calc->getRulesCollection()->getRules() as $rule) {
            $parts[] = $rule->getId();
        }

        return md5(implode('_', $parts));
    }

    /**
     * @param $theProduct int|WC_Product|\WP_Post
     *
     * @return false|WC_Product
     */
    public static function getWcProduct($theProduct)
    {
        if ($theProduct instanceof WC_Product) {
            $product = clone $theProduct;

            try {
                $reflection = new \ReflectionClass($product);
                $property   = $reflection->getProperty('changes');
                $property->setAccessible(true);
                $property->setValue($product, array());
            } catch (\ReflectionException $exception) {
                return false;
            }

            self::cacheSet($product->get_id(), $product, self::GROUP_WC_PRODUCT);

        } elseif (is_numeric($theProduct)) {
            $productId = $theProduct;

            $product = self::cacheGet($productId, self::GROUP_WC_PRODUCT);

            if ($product === false && ! empty($productById = wc_get_product($productId))) {
                $product = clone $productById;
                self::cacheSet($productId, $product, self::GROUP_WC_PRODUCT);
            }


        } elseif ($theProduct instanceof \WP_Post) {
            $productId = $theProduct->ID;

            $product = self::cacheGet($productId, self::GROUP_WC_PRODUCT);

            if ($product === false && ! empty($productById = wc_get_product($productId))) {
                $product = clone $productById;
                self::cacheSet($productId, $product, self::GROUP_WC_PRODUCT);
            }
        } else {
            return false;
        }

        return $product;
    }
}
