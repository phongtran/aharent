<?php

namespace ADP\BaseVersion\Includes\Rule\Structures;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Structures\Abstracts\BaseRule;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule\ProductsAdjustment;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule\ProductsRangeAdjustments;
use ADP\BaseVersion\Includes\Rule\Processors\SingleItemRuleProcessor;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SingleItemRule extends BaseRule implements Rule
{
    /**
     * @var array<int,Filter>
     */
    protected $filters;

    /**
     * @var ProductsAdjustment
     */
    protected $productAdjustmentHandler;

    /**
     * @var ProductsRangeAdjustments
     */
    protected $productRangeAdjustmentHandler;

    /**
     * @var GiftsCollection
     */
    protected $itemGiftsCollection;

    /**
     * @var int|float
     */
    protected $itemGiftLimit;

    /**
     * @var bool
     */
    protected $itemGiftsUseProductFromFilter = false;

    /**
     * @var bool
     */
    protected $replaceItemGifts = false;

    /**
     * @var string
     */
    protected $replaceItemGiftsCode = '';

    /**
     * @var array<int,RoleDiscount>
     */
    protected $roleDiscounts;

    /**
     * @var string
     */
    protected $sortableApplyMode;

    /**
     * @var array
     */
    protected $sortableBlocksPriority;

    /**
     * @var bool
     */
    protected $dontApplyBulkIfRolesMatched;

    /**
     * @var string
     */
    protected $itemGiftStrategy;

    /**
     * @var int
     */
    protected $itemsCountLimit;

    /**
     * @var float
     */
    protected $itemGiftSubtotalDivider;

    /**
     * @var string
     */
    protected $applyFirstTo;

    const APPLY_FIRST_TO_EXPENSIVE = 'expensive';
    const APPLY_FIRST_TO_CHEAP = 'cheap';
    const APPLY_FIRST_AS_APPEAR = 'appeared';

    const BASED_ON_LIMIT_ITEM_GIFT_STRATEGY = 'based_on_limit';
    const BASED_ON_SUBTOTAL_ITEM_GIFT_STRATEGY = 'based_on_subtotal';

    public function __construct()
    {
        parent::__construct();
        $this->filters             = array();
        $this->itemGiftsCollection = new GiftsCollection($this);

        $this->sortableApplyMode           = 'consistently';
        $this->sortableBlocksPriority      = array('roles', 'bulk-adjustments');
        $this->dontApplyBulkIfRolesMatched = false;
        $this->itemsCountLimit             = -1;
        $this->applyFirstTo                = self::APPLY_FIRST_AS_APPEAR;

        $this->itemGiftStrategy        = self::BASED_ON_LIMIT_ITEM_GIFT_STRATEGY;
        $this->itemGiftLimit           = INF;
        $this->itemGiftSubtotalDivider = null;
        $this->roleDiscounts           = array();
    }

    public function __clone()
    {
        parent::__clone();

        $this->filters = array_map(function ($item) {
            return clone $item;
        }, $this->filters);

        $this->itemGiftsCollection = clone $this->itemGiftsCollection;

        $this->roleDiscounts = array_map(function ($item) {
            return clone $item;
        }, $this->roleDiscounts);

        if ($this->productAdjustmentHandler) {
            $this->productAdjustmentHandler = clone $this->productAdjustmentHandler;
        }

        if ($this->productRangeAdjustmentHandler) {
            $this->productRangeAdjustmentHandler = clone $this->productRangeAdjustmentHandler;
        }
    }

    /**
     * @param Context $context
     *
     * @return SingleItemRuleProcessor
     * @throws Exception
     */
    public function buildProcessor($context)
    {
        return new SingleItemRuleProcessor($context, $this);
    }

    /**
     * @param Filter $filter
     */
    public function addFilter($filter)
    {
        if ($filter instanceof Filter) {
            $this->filters[] = $filter;
        }
    }

    /**
     * @param array<int,Filter> $filters
     */
    public function setFilters($filters)
    {
        $this->filters = array();

        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * @return array<int,Filter>
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param ProductsAdjustment $handler
     */
    public function installProductAdjustmentHandler($handler)
    {
        if ($handler instanceof ProductsAdjustment) {
            $this->productAdjustmentHandler = $handler;
        }
    }

    /**
     * @param ProductsRangeAdjustments $handler
     */
    public function installProductRangeAdjustmentHandler($handler)
    {
        if ($handler instanceof ProductsRangeAdjustments) {
            $this->productRangeAdjustmentHandler = $handler;
        }
    }

    /**
     * @return ProductsAdjustment|null
     */
    public function getProductAdjustmentHandler()
    {
        return $this->productAdjustmentHandler;
    }

    /**
     * @return ProductsRangeAdjustments|null
     */
    public function getProductRangeAdjustmentHandler()
    {
        return $this->productRangeAdjustmentHandler;
    }

    /**
     * @return bool
     */
    public function hasProductAdjustment()
    {
        return isset($this->productAdjustmentHandler) && $this->productAdjustmentHandler->isValid();
    }

    /**
     * @return bool
     */
    public function hasProductRangeAdjustment()
    {
        return isset($this->productRangeAdjustmentHandler) && $this->productRangeAdjustmentHandler->isValid();
    }

    /**
     * @param array<int,Gift> $gifts
     */
    public function setItemGifts($gifts)
    {
        $this->itemGiftsCollection->purge();
        $this->itemGiftsCollection->bulkAdd(...$gifts);
    }

    /**
     * @return GiftsCollection
     */
    public function getItemGiftsCollection()
    {
        return $this->itemGiftsCollection;
    }

    /**
     * @param bool $itemGiftsUseProductFromFilter
     */
    public function setItemGiftsUseProductFromFilter($itemGiftsUseProductFromFilter)
    {
        $this->itemGiftsUseProductFromFilter = $itemGiftsUseProductFromFilter;
    }

    /**
     * @return bool
     */
    public function isItemGiftsUseProductFromFilter()
    {
        return $this->itemGiftsUseProductFromFilter;
    }

    /**
     * @param string $strategy
     */
    public function setItemGiftStrategy($strategy)
    {
        if (in_array($strategy,
            array(self::BASED_ON_LIMIT_ITEM_GIFT_STRATEGY, self::BASED_ON_SUBTOTAL_ITEM_GIFT_STRATEGY))) {
            $this->itemGiftStrategy = $strategy;
        }
    }

    /**
     * @return string
     */
    public function getItemGiftStrategy()
    {
        return $this->itemGiftStrategy;
    }

    /**
     * @param int|float $itemGiftLimit
     */
    public function setItemGiftLimit($itemGiftLimit)
    {
        $this->itemGiftLimit = $itemGiftLimit !== INF ? intval($itemGiftLimit) : INF;
    }

    /**
     * @return int|float
     */
    public function getItemGiftLimit()
    {
        return $this->itemGiftLimit;
    }

    /**
     * @param float $itemGiftSubtotalDivider
     */
    public function setItemGiftSubtotalDivider($itemGiftSubtotalDivider)
    {
        $this->itemGiftSubtotalDivider = floatval($itemGiftSubtotalDivider);
    }

    /**
     * @return float
     */
    public function getItemGiftSubtotalDivider()
    {
        return $this->itemGiftSubtotalDivider;
    }

    /**
     * @return bool
     */
    public function isReplaceItemGifts()
    {
        return $this->replaceItemGifts;
    }

    /**
     * @param bool $replaceItemGifts
     */
    public function setReplaceItemGifts($replaceItemGifts)
    {
        $this->replaceItemGifts = boolval($replaceItemGifts);
    }

    /**
     * @return string
     */
    public function getReplaceItemGiftsCode()
    {
        return $this->replaceItemGiftsCode;
    }

    /**
     * @param string $replaceItemGiftsCode
     */
    public function setReplaceItemGiftsCode($replaceItemGiftsCode)
    {
        $this->replaceItemGiftsCode = $replaceItemGiftsCode;
    }

    /**
     * @return array<int,RoleDiscount>
     */
    public function getRoleDiscounts()
    {
        return $this->roleDiscounts;
    }

    /**
     * @param array<int,RoleDiscount> $roleDiscounts
     */
    public function setRoleDiscounts($roleDiscounts)
    {
        $this->roleDiscounts = array();
        foreach ($roleDiscounts as $roleDiscount) {
            if ($roleDiscount instanceof RoleDiscount) {
                $this->roleDiscounts[] = $roleDiscount;
            }
        }
    }

    /**
     * @return string
     */
    public function getSortableApplyMode()
    {
        return $this->sortableApplyMode;
    }

    /**
     * @param string $sortableApplyMode
     */
    public function setSortableApplyMode($sortableApplyMode)
    {
        $this->sortableApplyMode = $sortableApplyMode;
    }

    /**
     * @return array
     */
    public function getSortableBlocksPriority()
    {
        return $this->sortableBlocksPriority;
    }

    /**
     * @param array $sortableBlocksPriority
     */
    public function setSortableBlocksPriority($sortableBlocksPriority)
    {
        $this->sortableBlocksPriority = $sortableBlocksPriority;
    }

    /**
     * @return bool
     */
    public function isDontApplyBulkIfRolesMatched()
    {
        return $this->dontApplyBulkIfRolesMatched;
    }

    /**
     * @param bool $dontApplyBulkIfRolesMatched
     */
    public function setDontApplyBulkIfRolesMatched($dontApplyBulkIfRolesMatched)
    {
        $this->dontApplyBulkIfRolesMatched = $dontApplyBulkIfRolesMatched;
    }

    /**
     * @param int $itemsCountLimit
     */
    public function setItemsCountLimit($itemsCountLimit)
    {
        $this->itemsCountLimit = intval($itemsCountLimit);
    }

    /**
     * @return int
     */
    public function getItemsCountLimit()
    {
        return $this->itemsCountLimit;
    }

    /**
     * @return bool
     */
    public function isItemsCountLimitExists()
    {
        return $this->itemsCountLimit !== -1;
    }

    /**
     * @param string $applyFirstTo
     */
    public function setApplyFirstTo($applyFirstTo)
    {
        if (in_array($applyFirstTo,
            array(self::APPLY_FIRST_AS_APPEAR, self::APPLY_FIRST_TO_EXPENSIVE, self::APPLY_FIRST_TO_CHEAP))) {
            $this->applyFirstTo = $applyFirstTo;
        }
    }

    /**
     * @return string
     */
    public function getApplyFirstTo()
    {
        return $this->applyFirstTo;
    }
}
