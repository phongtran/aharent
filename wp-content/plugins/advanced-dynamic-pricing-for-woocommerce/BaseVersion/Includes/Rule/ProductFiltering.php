<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Common\Helpers;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\CacheHelper;
use WC_Meta_Data;
use WC_Product;
use WC_Product_Attribute;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ProductFiltering
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var array<int,WC_Product>
     */
    protected $cachedParents = array();

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * @param string $operationType
     * @param mixed $operationValues
     * @param string|null $operationMethod
     */
    public function prepare($operationType, $operationValues, $operationMethod)
    {
        $this->type = $operationType;

        if (is_array($operationValues)) {
            $this->values = $operationValues;
        } else {
            $this->value = $operationValues;
        }

        $this->method = ! empty($operationMethod) ? $operationMethod : 'in_list';
    }

    public function setType($operationType)
    {
        $this->type = $operationType;
    }

    public function isType($type)
    {
        return $type === $this->type;
    }

    public function setOperationValues($operationValues)
    {
        $this->values = $operationValues;
    }

    public function setMethod($operation_method)
    {
        $this->method = $operation_method;
    }

    /**
     * @param WC_Product $product
     *
     * @return false|WC_Product|null
     */
    protected function getMainProduct($product)
    {
        if ( ! $product->get_parent_id()) {
            return $product;
        }

        $parent = CacheHelper::getWcProduct($product->get_parent_id());

        return $parent ? $parent : $product;
    }

    /**
     * @param WC_Product $product
     * @param array $cartItem
     *
     * @return bool
     */
    public function checkProductSuitability($product, $cartItem = array())
    {
        if ($this->type === 'any' && $this->method === 'in_list') {
            return true;
        }

        if ($this->method === 'any') {
            return true;
        }

        if ( ! ((isset($this->values) && count($this->values)) || isset($this->value))) {
            return false;
        }

        $func = array($this, "compareProductWith" . ucfirst($this->type));

        if (is_callable($func)) {
            return call_user_func($func, $product, $cartItem);
        } elseif (in_array($this->type, array_keys(Helpers::getCustomProductTaxonomies()))) {
            return $this->compareProductWithCustom_taxonomy($product, $cartItem);
        }

        return false;
    }

    protected function compareProductWithProducts($product, $cartItem)
    {
        $result         = false;
        $product_parent = $this->getMainProduct($product);

        if ('in_list' === $this->method) {
            $result = (in_array($product->get_id(), $this->values) or in_array($product_parent->get_id(),
                    $this->values));
        } elseif ('not_in_list' === $this->method) {
            $result = ! (in_array($product->get_id(), $this->values) or in_array($product_parent->get_id(),
                    $this->values));
        } elseif ('any' === $this->method) {
            $result = true;
        }

        return $result;
    }

    protected function compareProductWithProduct_categories($product, $cartItem)
    {
        $product    = $this->getMainProduct($product);
        $categories = $product->get_category_ids();

        $values = array();
        foreach ($this->values as $value) {
            $values[] = $value;
            $child    = get_term_children($value, 'product_cat');

            if ($child && ! is_wp_error($child)) {
                $values = array_merge($values, $child);
            }
        }

        $is_product_in_category = count(array_intersect($categories, $values)) > 0;

        if ('in_list' === $this->method) {
            return $is_product_in_category;
        } elseif ('not_in_list' === $this->method) {
            return ! $is_product_in_category;
        }

        return false;
    }

    protected function compareProductWithProduct_category_slug($product, $cartItem)
    {
        $product        = $this->getMainProduct($product);
        $category_slugs = array_map(function ($category_id) {
            $term = get_term($category_id, 'product_cat');

            return $term ? $term->slug : '';
        }, $product->get_category_ids());

        $is_product_in_category = count(array_intersect($category_slugs, $this->values)) > 0;

        if ('in_list' === $this->method) {
            return $is_product_in_category;
        } elseif ('not_in_list' === $this->method) {
            return ! $is_product_in_category;
        }

        return false;
    }

    protected function compareProductWithProduct_tags($product, $cartItem)
    {
        $product = $this->getMainProduct($product);
        $tag_ids = $product->get_tag_ids();

        $is_product_has_tag = count(array_intersect($tag_ids, $this->values)) > 0;

        if ('in_list' === $this->method) {
            return $is_product_has_tag;
        } elseif ('not_in_list' === $this->method) {
            return ! $is_product_has_tag;
        }

        return false;
    }

    /**
     * @param WC_Product $product
     * @param array $cartItem
     *
     * @return bool
     */
    protected function compareProductWithProduct_attributes($product, $cartItem)
    {
//		$product = $this->get_cached_wc_product( $product ); // use variation attributes?
        $attrs = $product->get_attributes();

        $calculated_term_obj = array();
        $term_attr_ids       = array(
            'empty' => array(),
        );

        $attr_ids    = array();
        $attr_custom = array();

        if ($product->is_type('variation')) {
            if (count(array_filter($attrs)) < count($attrs)) {
                if (isset($cartItem['variation'])) {
                    $attrs = array();
                    foreach ($cartItem['variation'] as $attribute_name => $value) {
                        $attrs[str_replace('attribute_', '', $attribute_name)] = $value;
                    }
                }
            }

            $product_variable = $this->getMainProduct($product);
            $attrs_variable   = $product_variable->get_attributes();

            foreach ($attrs_variable as $attribute_name => $product_attr) {
                /**
                 * @var WC_Product_Attribute $product_attr
                 */
                if ( ! $product_attr->get_variation()) {
                    $attrs[$product_attr->get_name()] = "";
                }
            }

            foreach ($attrs as $attribute_name => $value) {
                $init_attribute_name = $attribute_name;
                $attribute_name      = $this->attributeTaxonomySlug($attribute_name);
                if ($value) {
                    $term_obj = get_term_by('slug', $value, $init_attribute_name);
                    if ( ! is_wp_error($term_obj) && $term_obj && $term_obj->name) {
                        $attr_ids[$attribute_name] = (array)($term_obj->term_id);
                    } else {
                        $attr_custom[$attribute_name] = (array)($value);
                    }
                } else {
                    // replace undefined variation attribute by the list of all option of this attribute
                    if (isset($attrs_variable[$attribute_name])) {
                        $attribute_object = $attrs_variable[$attribute_name];
                    } elseif (isset($attrs_variable['pa_' . $attribute_name])) {
                        $attribute_object = $attrs_variable['pa_' . $attribute_name];
                    } else {
                        continue;
                    }

                    /** @var WC_Product_Attribute $attribute_object */
                    if ($attribute_object->is_taxonomy()) {
                        $attr_ids[$attribute_name] = (array)($attribute_object->get_options());
                        foreach ($attribute_object->get_terms() as $term) {
                            /**
                             * @var \WP_Term $term
                             */
                            $attr_custom[$attribute_name][] = $term->name;
                        }
                    } else {
                        if (strtolower($attribute_name) == strtolower($attribute_object->get_name())) {
                            $attr_custom[$attribute_name] = $attribute_object->get_options();
                        }
                    }
                }
            }
        } else {
            foreach ($attrs as $attr) {
                /** @var WC_Product_Attribute $attr */
                if ($attr->is_taxonomy()) {
                    $attr_ids[strtolower($attr->get_name())] = (array)($attr->get_options());
                } else {
                    if (strtolower($attr->get_name()) == strtolower($attr->get_name())) {
                        $attr_custom[strtolower($attr->get_name())] = $attr->get_options();
                    }
                }
            }
        }

        $operation_values_tax          = array();
        $operation_values_custom_attrs = array();
        foreach ($this->values as $attr_id) {
            $term_obj = false;

            foreach ($term_attr_ids as $hash => $tmp_attr_ids) {
                if (in_array($attr_id, $tmp_attr_ids)) {
                    $term_obj = isset($calculated_term_obj[$hash]) ? $calculated_term_obj[$hash] : false;
                    break;
                }
            }

            if (empty($term_obj)) {
                $term_obj = get_term($attr_id);
                if ( ! $term_obj) {
                    $term_attr_ids['empty'][] = $attr_id;
                    continue;
                }

                if (is_wp_error($term_obj)) {
                    continue;
                }

                $hash                       = md5(json_encode($term_obj));
                $calculated_term_obj[$hash] = $term_obj;
                if ( ! isset($term_attr_ids[$hash])) {
                    $term_attr_ids[$hash] = array();
                }
                $term_attr_ids[$hash][] = $attr_id;
            }

            $attribute_name = $this->attributeTaxonomySlug($term_obj->taxonomy);
            if ( ! isset($operation_values_tax[$attribute_name])) {
                $operation_values_tax[$attribute_name] = array();
            }
            $operation_values_tax[$attribute_name][]          = $attr_id;
            $operation_values_custom_attrs[$attribute_name][] = $term_obj->name;
        }

        $is_product_has_attrs_id = true;
        foreach ($operation_values_tax as $attribute_name => $tmp_attr_ids) {
            if (( ! isset($attr_ids[$attribute_name]) || ! count(array_intersect($tmp_attr_ids,
                        $attr_ids[$attribute_name]))) && ( ! isset($attr_ids[wc_attribute_taxonomy_name($attribute_name)]) || ! count(array_intersect($tmp_attr_ids,
                        $attr_ids[wc_attribute_taxonomy_name($attribute_name)])))) {
                $is_product_has_attrs_id = false;
                break;
            }
        }

        $is_product_has_attrs_custom = true;
        foreach ($operation_values_custom_attrs as $attribute_name => $tmp_attr_names) {
            if ( ! isset($attr_custom[$attribute_name]) || ! count(array_intersect($tmp_attr_names,
                    $attr_custom[$attribute_name]))) {
                $is_product_has_attrs_custom = false;
                break;
            }
        }

        if ('in_list' === $this->method) {
            return $is_product_has_attrs_id || $is_product_has_attrs_custom;
        } elseif ('not_in_list' === $this->method) {
            return ! ($is_product_has_attrs_id || $is_product_has_attrs_custom);
        }

        return false;
    }

    /**
     * @param WC_Product $product
     * @param array $cartItem
     *
     * @return bool
     */
    protected function compareProductWithProduct_custom_attributes($product, $cartItem)
    {

        $attrs = $product->get_attributes();

        $attrsCustom = array();

        if ($product->is_type('variation')) {
            $productVariable = $this->getMainProduct($product);
            $attrsVariable   = $productVariable->get_attributes();

            foreach ($attrsVariable as $attributeName => $productAttr) {
                /**
                 * @var WC_Product_Attribute $productAttr
                 */
                if ( ! $productAttr->get_variation()) {
                    $attrs[$productAttr->get_name()] = $productAttr->get_options();
                }
            }

            foreach ($attrs as $attributeName => $value) {
                $initAttributeName = $attributeName;
                $attributeName     = $this->attributeTaxonomySlug($attributeName);
                if ($value) {
                    $termObj = get_term_by('slug', $value, $initAttributeName);
                    if (is_wp_error($termObj) || ! $termObj) {
                        $attrsCustom[$attributeName] = (array)($value);
                    }
                } else {
                    // replace undefined variation attribute by the list of all option of this attribute
                    if (isset($attrsVariable[$attributeName])) {
                        $attributeObject = $attrsVariable[$attributeName];
                    } elseif (isset($attrsVariable['pa_' . $attributeName])) {
                        $attributeObject = $attrsVariable['pa_' . $attributeName];
                    } else {
                        continue;
                    }

                    /** @var WC_Product_Attribute $attributeObject */
                    if ( ! $attributeObject->is_taxonomy()) {
                        if (strtolower($attributeName) == strtolower($attributeObject->get_name())) {
                            $attrsCustom[$attributeName] = $attributeObject->get_options();
                        }
                    }
                }
            }
        } else {
            foreach ($attrs as $attr) {
                /** @var WC_Product_Attribute $attr */
                if ( ! $attr->is_taxonomy()) {
                    $attrsCustom[strtolower($attr->get_name())] = $attr->get_options();
                }
            }
        }


        $attrsCustom = array_map(function ($options) {
            return array_map("strtolower", $options);
        }, $attrsCustom);

        $inList      = false;
        foreach ($this->values as $customAttr) {
            $pieces        = explode(":", $customAttr);
            $attributeName = strtolower(trim($pieces[0]));
            $option        = strtolower(trim($pieces[1]));

            if (isset($attrsCustom[$attributeName])) {
                if (in_array($option, $attrsCustom[$attributeName], true)) {
                    $inList = true;
                }
            }
        }

        if ('in_list' === $this->method) {
            return $inList;
        } elseif ('not_in_list' === $this->method) {
            return ! $inList;
        }

        return false;
    }

    private function attributeTaxonomySlug($attributeName)
    {
        $attributeName  = wc_sanitize_taxonomy_name($attributeName);
        $attribute_slug = 0 === strpos($attributeName, 'pa_') ? substr($attributeName, 3) : $attributeName;

        return $attribute_slug;
    }

    /**
     * @param \WC_Product $product
     * @param array $cartItem
     *
     * @return bool
     */
    protected function compareProductWithProduct_sku($product, $cartItem)
    {
        $result      = false;
        $productSkus = array($product->get_sku());

        if ($product->get_parent_id() && ($parent = CacheHelper::getWcProduct($product->get_parent_id()))) {
            $productSkus[] = $parent->get_sku();
        }

        if ('in_list' === $this->method) {
            $result = (count(array_intersect($productSkus, $this->values)) > 0);
        } elseif ('not_in_list' === $this->method) {
            $result = count(array_intersect($productSkus, $this->values)) === 0;
        } elseif ('any' === $this->method) {
            $result = true;
        }

        return $result;
    }

    protected function compareProductWithProductSellers($product, $cartItem)
    {
        $result = false;

        $product_post = get_post($product->get_id());
        $post_author  = $product_post->post_author;

        if ('in_list' === $this->method) {
            $result = (in_array($post_author, $this->values));
        } elseif ('not_in_list' === $this->method) {
            $result = ! (in_array($post_author, $this->values));
        }

        return $result;
    }

    protected function compareProductWithProduct_custom_fields($product, $cartItem)
    {
        $parentProduct                = $this->getMainProduct($product);
        $check_children_custom_fields = apply_filters('wdp_compare_product_with_product_custom_fields_check_children',
            false);
        $meta                         = array();

        if ($check_children_custom_fields) {
            $meta = $this->getProductMeta($product);
        }

        $meta                  = array_merge_recursive($this->getProductMeta($parentProduct), $meta);
        $custom_fields         = $this->prepareMeta($meta);
        $values                = is_array($this->values) ? $this->values : array();
        $is_product_has_fields = count(array_intersect($custom_fields, $values)) > 0;

        if ( ! $is_product_has_fields) {
            $meta = array();

            if ($check_children_custom_fields) {
                $meta = $this->getProductPostMeta($product);
            }

            $meta                  = array_merge_recursive($this->getProductPostMeta($parentProduct), $meta);
            $custom_fields         = $this->prepareMeta($meta);
            $is_product_has_fields = count(array_intersect($custom_fields, $values)) > 0;
        }

        if ('in_list' === $this->method) {
            return $is_product_has_fields;
        } elseif ('not_in_list' === $this->method) {
            return ! $is_product_has_fields;
        }

        return false;
    }

    protected function compareProductWithCustom_taxonomy($product, $cartItem)
    {
        $product  = $this->getMainProduct($product);
        $taxonomy = $this->type;

        $termIds          = wp_get_post_terms($product->get_id(), $taxonomy, array("fields" => "ids"));
        $isProductHasTerm = count(array_intersect($termIds, $this->values)) > 0;

        if ('in_list' === $this->method) {
            return $isProductHasTerm;
        } elseif ('not_in_list' === $this->method) {
            return ! $isProductHasTerm;
        }

        return false;
    }

    protected function compareProductWithProduct_shipping_class($product, $cartItem)
    {
        $shippingClass = $product->get_shipping_class();

        $hasProductShippingClass = in_array($shippingClass, $this->values);

        if ('in_list' === $this->method) {
            return $hasProductShippingClass;
        } elseif ('not_in_list' === $this->method) {
            return ! $hasProductShippingClass;
        }

        return false;
    }

    /**
     * @param WC_Product $product
     *
     * @return array
     */
    private function getProductMeta($product)
    {
        $meta = array();

        foreach ($product->get_meta_data() as $metaDatum) {
            /**
             * @var WC_Meta_Data $metaDatum
             */
            $data = $metaDatum->get_data();

            if ( ! isset($meta[$data['key']])) {
                $meta[$data['key']] = array();
            }
            $meta[$data['key']][] = $data['value'];
        }

        return $meta;
    }

    /**
     * @param WC_Product $product
     *
     * @return array
     */
    private function getProductPostMeta($product)
    {
        if ( ! ($postMeta = get_post_meta($product->get_id(), ""))) {
            return array();
        };
        $meta = array();

        foreach ($postMeta as $key => $value) {
            $meta[$key] = $value;
        }

        return $meta;
    }

    /**
     * @param array $meta
     *
     * @return array
     */
    private function prepareMeta($meta)
    {
        $customFields = array();
        foreach ($meta as $key => $values) {
            foreach ($values as $value) {
                if ( ! is_array($value)) {
                    $customFields[] = "$key=$value";
                }
            }
        }

        return $customFields;
    }

}
