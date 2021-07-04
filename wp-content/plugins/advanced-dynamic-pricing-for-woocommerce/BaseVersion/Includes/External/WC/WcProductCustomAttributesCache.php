<?php


namespace ADP\BaseVersion\Includes\External\WC;


class WcProductCustomAttributesCache
{
    static $transientKey = "wc_adp_cache_custom_attributes";

    public function installHooks()
    {
        add_action('woocommerce_after_product_object_save', function ($product, $dataStore) {
            /** @var \WC_Product $product */
            if ( ! $product->is_type("variation")) {
                $this->recalculateTransient();
            }
        }, 10, 2);
    }

    /**
     * @return array
     */
    public function getAllCustomAttributes()
    {
        $attributes = get_transient(self::$transientKey);

        if ( ! $attributes || ! is_array($attributes)) {
            $attributes = $this->recalculateTransient();
        }

        return $attributes;
    }

    /**
     * @return array
     */
    protected function recalculateTransient()
    {
        $attributes = $this->calculateProductCustomAttributes();
        set_transient(self::$transientKey, $attributes);

        return $attributes;
    }

    protected function calculateProductCustomAttributes()
    {
        $products = wc_get_products(array(
            'return' => 'ids',
            'limit'  => -1,
        ));

        $attributes = array();
        foreach ($products as $productID) {
            $metaAttributes = get_post_meta($productID, "_product_attributes", true);

            if (empty($metaAttributes) || ! is_array($metaAttributes)) {
                continue;
            }

            foreach ($metaAttributes as $metaAttributeKey => $metaAttributeValue) {
                $metaValue = array_merge(
                    array(
                        'name'         => '',
                        'value'        => '',
                        'position'     => 0,
                        'is_visible'   => 0,
                        'is_variation' => 0,
                        'is_taxonomy'  => 0,
                    ),
                    (array)$metaAttributeValue
                );

                if ( ! empty($metaValue['is_taxonomy'])) {
                    continue;
                }

                $name    = $metaValue['name'];
                $options = wc_get_text_attributes($metaValue['value']);

                if (isset($attributes[$name])) {
                    $attributes[$name] = array_unique(array_merge($attributes[$name], $options));
                } else {
                    $attributes[$name] = $options;
                }
            }
        }

        return $attributes;
    }
}
