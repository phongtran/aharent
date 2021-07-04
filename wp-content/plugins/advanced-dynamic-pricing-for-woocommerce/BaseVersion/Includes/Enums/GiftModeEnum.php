<?php

namespace ADP\BaseVersion\Includes\Enums;


/**
 * @method static self USE_PRODUCT_PROM_FILTER()
 * @method static self GIFTABLE_PRODUCTS()
 * @method static self GIFTABLE_PRODUCTS_ROTATION()
 * @method static self GIFTABLE_PRODUCTS_IN_RANDOM()
 * @method static self ALLOW_TO_CHOOSE()
 * @method static self ALLOW_TO_CHOOSE_FROM_PRODUCT_CAT()
 * @method static self REQUIRE_TO_CHOOSE()
 * @method static self REQUIRE_TO_CHOOSE_FROM_PRODUCT_CAT()
 */
class GiftModeEnum extends BaseEnum
{
    const __default = self::USE_PRODUCT_PROM_FILTER;

    const USE_PRODUCT_PROM_FILTER = 'use_product_from_filter';
    const GIFTABLE_PRODUCTS = 'giftable_products';
    const GIFTABLE_PRODUCTS_ROTATION = 'giftable_products_in_rotation';
    const GIFTABLE_PRODUCTS_IN_RANDOM = 'giftable_products_in_random';
    const ALLOW_TO_CHOOSE = 'allow_to_choose';
    const ALLOW_TO_CHOOSE_FROM_PRODUCT_CAT = 'allow_to_choose_from_product_cat';
    const REQUIRE_TO_CHOOSE = 'require_to_choose';
    const REQUIRE_TO_CHOOSE_FROM_PRODUCT_CAT = 'require_to_choose_from_product_cat';

    /**
     * @param self $variable
     *
     * @return bool
     */
    public function equals($variable)
    {
        return parent::equals($variable);
    }
}
