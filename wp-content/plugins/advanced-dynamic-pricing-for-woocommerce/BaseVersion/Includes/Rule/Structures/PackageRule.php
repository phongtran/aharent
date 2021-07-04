<?php

namespace ADP\BaseVersion\Includes\Rule\Structures;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Processors\PackageRuleProcessor;
use ADP\BaseVersion\Includes\Rule\Structures\Abstracts\BaseRule;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule\ProductsAdjustmentSplit;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule\ProductsAdjustmentTotal;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule\PackageRangeAdjustments;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class PackageRule extends BaseRule implements Rule
{
    /**
     * @var array<int,PackageItem>
     */
    protected $packages;

    /**
     * @var ProductsAdjustmentTotal|ProductsAdjustmentSplit
     */
    protected $productAdjustmentHandler;

    /**
     * @var PackageRangeAdjustments
     */
    protected $productRangeAdjustmentHandler;

    /**
     * @var GiftsCollection
     */
    protected $itemGiftsCollection;

    /**
     * @var string
     */
    protected $itemGiftStrategy;

    /**
     * @var int|float
     */
    protected $itemGiftLimit;

    /**
     * @var float
     */
    protected $itemGiftSubtotalDivider;

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
     * @var RoleDiscount[]
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
     * @var int
     */
    protected $packagesCountLimit;

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
        $this->packages            = array();
        $this->itemGiftsCollection = new GiftsCollection($this);

        $this->sortableApplyMode           = 'consistently';
        $this->sortableBlocksPriority      = array('roles', 'bulk-adjustments');
        $this->dontApplyBulkIfRolesMatched = false;
        $this->packagesCountLimit          = -1;
        $this->applyFirstTo                = self::APPLY_FIRST_AS_APPEAR;

        $this->itemGiftStrategy        = self::BASED_ON_LIMIT_ITEM_GIFT_STRATEGY;
        $this->itemGiftLimit           = INF;
        $this->itemGiftSubtotalDivider = null;
        $this->roleDiscounts           = array();
    }

    public function __clone()
    {
        $this->packages = array_map(function ($item) {
            return clone $item;
        }, $this->packages);

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
     * @param PackageItem $package
     */
    public function addPackage($package)
    {
        if ($package instanceof PackageItem) {
            $this->packages[] = $package;
        }
    }

    /**
     * @param array<int,PackageItem> $packages
     */
    public function setPackages($packages)
    {
        $this->packages = array();

        foreach ($packages as $package) {
            $this->addPackage($package);
        }
    }

    /**
     * @return array<int,PackageItem>
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param Context $context
     *
     * @return PackageRuleProcessor
     * @throws Exception
     */
    public function buildProcessor($context)
    {
        return new PackageRuleProcessor($context, $this);
    }

    /**
     * @param int $packagesCountLimit
     */
    public function setPackagesCountLimit($packagesCountLimit)
    {
        $this->packagesCountLimit = intval($packagesCountLimit);
    }

    /**
     * @return int
     */
    public function getPackagesCountLimit()
    {
        return $this->packagesCountLimit;
    }

    /**
     * @param ProductsAdjustmentTotal|ProductsAdjustmentSplit $handler
     */
    public function installProductAdjustmentHandler($handler)
    {
        if ($handler instanceof ProductsAdjustmentTotal || $handler instanceof ProductsAdjustmentSplit) {
            $this->productAdjustmentHandler = $handler;
        }
    }

    /**
     * @param PackageRangeAdjustments $handler
     */
    public function installProductRangeAdjustmentHandler($handler)
    {
        if ($handler instanceof PackageRangeAdjustments) {
            $this->productRangeAdjustmentHandler = $handler;
        }
    }

    /**
     * @return ProductsAdjustmentTotal|ProductsAdjustmentSplit
     */
    public function getProductAdjustmentHandler()
    {
        return $this->productAdjustmentHandler;
    }

    /**
     * @return PackageRangeAdjustments|null
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
     * @return array<int, RoleDiscount>
     */
    public function getRoleDiscounts()
    {
        return $this->roleDiscounts;
    }

    /**
     * @param array<int, RoleDiscount> $roleDiscounts
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
