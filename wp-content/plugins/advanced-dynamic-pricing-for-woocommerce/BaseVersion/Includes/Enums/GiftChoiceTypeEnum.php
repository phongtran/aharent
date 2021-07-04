<?php

namespace ADP\BaseVersion\Includes\Enums;

/**
 * @method static self PRODUCT()
 * @method static self CATEGORY()
 * @method static self CLONE_ADJUSTED()
 */
class GiftChoiceTypeEnum extends BaseEnum
{
    const __default = self::PRODUCT;

    const PRODUCT = 'product';
    const CATEGORY = 'product_category';

    const CLONE_ADJUSTED = 'clone';

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
