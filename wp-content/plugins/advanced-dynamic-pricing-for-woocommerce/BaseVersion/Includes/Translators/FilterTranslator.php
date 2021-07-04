<?php

namespace ADP\BaseVersion\Includes\Translators;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class FilterTranslator
{
    /**
     * @param string $type
     * @param $value
     * @param string|null $languageCode
     *
     * @return array|mixed
     */
    public function translateByType($type, $value, $languageCode)
    {
        $returnAsArray = is_array($value);
        $values        = is_array($value) ? $value : array($value);

        if ('products' === $type) {
            $values = $this->translateProduct($values, $languageCode);
        } elseif ('product_categories' === $type) {
            $values = $this->translateCategory($values, $languageCode);
        } elseif ('product_category_slug' === $type) {
            $values = $this->translateCategorySlug($values, $languageCode);
        } elseif ('product_attributes' === $type) {
            $values = $this->translateAttribute($values, $languageCode);
        } elseif ('product_tags' === $type) {
            $values = $this->translateTag($values, $languageCode);
        } elseif ('product_skus' === $type) {
            // do not translate
        } elseif ('product_custom_fields' === $type) {
            // do not translate
        } else {
            $values = $this->translateCustomTax($values, $type, $languageCode);
        }

        return $returnAsArray ? $values : reset($values);
    }

    /**
     * @param mixed $theValue
     * @param string|null $languageCode
     *
     * @return array|mixed
     */
    public function translateProduct($theValue, $languageCode)
    {
        $returnAsArray = is_array($theValue);
        $ids           = is_array($theValue) ? $theValue : array($theValue);

        foreach ($ids as &$id) {
            $translValue = apply_filters('translate_object_id', $id, 'post', false, $languageCode);
            if ($translValue) {
                $id = $translValue;
            }
        }

        return $returnAsArray ? $ids : reset($ids);
    }

    /**
     * @param mixed $theValue
     * @param string|null $languageCode
     *
     * @return array|mixed
     */
    public function translateCategory($theValue, $languageCode)
    {
        $returnAsArray = is_array($theValue);
        $ids           = is_array($theValue) ? $theValue : array($theValue);

        foreach ($ids as &$id) {
            $transl_value = apply_filters('translate_object_id', $id, 'product_cat', false, $languageCode);
            if ($transl_value) {
                $id = $transl_value;
            }
        }

        return $returnAsArray ? $ids : reset($ids);
    }

    /**
     * @param mixed $theValue
     * @param string|null $languageCode
     *
     * @return array|mixed
     */
    public function translateCategorySlug($theValue, $languageCode)
    {
        $return_as_array = is_array($theValue);
        $slugs           = is_array($theValue) ? $theValue : array($theValue);

        foreach ($slugs as &$slug) {
            // translated in get_term_by
            $term = get_term_by('slug', $slug, 'product_cat');
            if ($term && ! is_wp_error($term)) {
                $slug = $term->slug;
            }
        }

        return $return_as_array ? $slugs : reset($slugs);
    }

    /**
     * @param mixed $theValue
     * @param string|null $languageCode
     *
     * @return array|mixed
     */
    public function translateAttribute($theValue, $languageCode)
    {
        $returnAsArray = is_array($theValue);
        $ids           = is_array($theValue) ? $theValue : array($theValue);

        foreach ($ids as &$id) {
            // translated in get_term
            $term = get_term($id);
            if ($term && ! is_wp_error($term)) {
                $id = $term->term_id;
            }
        }

        return $returnAsArray ? $ids : reset($ids);
    }

    /**
     * @param mixed $theValue
     * @param string|null $languageCode
     *
     * @return array|mixed
     */
    public function translateTag($theValue, $languageCode)
    {
        $return_as_array = is_array($theValue);
        $ids             = is_array($theValue) ? $theValue : array($theValue);

        foreach ($ids as &$id) {
            $transl_value = apply_filters('translate_object_id', $id, 'product_tag', false, $languageCode);
            if ($transl_value) {
                $id = $transl_value;
            }
        }

        return $return_as_array ? $ids : reset($ids);
    }

    /**
     * @param mixed $theValue
     * @param string $tax
     * @param string|null $languageCode
     *
     * @return array|mixed
     */
    public function translateCustomTax($theValue, $tax, $languageCode)
    {
        $returnAsArray = is_array($theValue);
        $ids           = is_array($theValue) ? $theValue : array($theValue);

        foreach ($ids as &$id) {
            $transl_value = apply_filters('translate_object_id', $id, $tax, false, $languageCode);
            if ($transl_value) {
                $id = $transl_value;
            }
        }

        return $returnAsArray ? $ids : reset($ids);
    }

}
