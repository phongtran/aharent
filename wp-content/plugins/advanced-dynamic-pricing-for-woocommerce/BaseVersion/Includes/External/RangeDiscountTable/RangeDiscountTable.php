<?php

namespace ADP\BaseVersion\Includes\External\RangeDiscountTable;

use ADP\BaseVersion\Includes\Cart\CartBuilder;
use ADP\BaseVersion\Includes\Cart\CartCalculator;
use ADP\BaseVersion\Includes\Cart\RulesCollection;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\External\Customizer\Customizer;
use ADP\BaseVersion\Includes\External\WC\PriceFunctions;
use ADP\BaseVersion\Includes\Product\ProcessedProductSimple;
use ADP\BaseVersion\Includes\Product\ProcessedVariableProduct;
use ADP\BaseVersion\Includes\Product\Processor;
use ADP\BaseVersion\Includes\Rule\Processors\SingleItemRuleProcessor;
use ADP\BaseVersion\Includes\Rule\Structures\Discount;
use ADP\BaseVersion\Includes\Rule\Structures\Range;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\Factory;
use Exception;
use WC_Product;

class RangeDiscountTable
{
    const CONTEXT_PRODUCT_PAGE = 'product';
    const CONTEXT_CATEGORY_PAGE = 'category';
    const LAYOUT_SIMPLE = 'simple';
    const LAYOUT_VERBOSE = 'verbose';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Context
     */
    protected $customizer;

    /**
     * @var FiltersFormatter
     */
    protected $filtersFormatter;

    /**
     * @var PriceFunctions
     */
    protected $priceFunctions;

    /**
     * @var array
     */
    protected $contextOptions;

    /**
     * @param Context $context
     * @param Customizer $customizer
     */
    public function __construct($context, $customizer)
    {
        $this->context          = $context;
        $this->customizer       = $customizer;
        $this->filtersFormatter = Factory::get("External_RangeDiscountTable_FiltersFormatter", $this->context);
        $this->priceFunctions   = new PriceFunctions($context);
        $this->contextOptions   = array();
    }

    /**
     * @param int|null $productId
     * @param array<string, string> $attributes
     *
     * @return string
     */
    public function getProductTableContent($productId = null, $attributes = array())
    {
        if ( ! $productId) {
            global $product;

            if ( ! isset($product)) {
                return "";
            }

            /**
             * @var $product WC_Product
             */
            $productId = $product->get_id();

            if ( ! $productId) {
                return "";
            }
        } else {
            $product = CacheHelper::getWcProduct($productId);
        }

        if ($product instanceof \WC_Product_Variation && array_filter($attributes)) {
            $product->set_attributes(array_filter($attributes));
        }

        $content = '<span class="wdp_bulk_table_content"></span>';
        try {
            $table = $this->getProductTable($product);
            if ($table) {
                /** @var $product WC_Product */
                if ($product->is_type('variable')) {
                    $availableProductsIDs = array_merge(array($product->get_id()), $product->get_children());
                    $content              = '<span class="wdp_bulk_table_content" data-available-ids="' . json_encode($availableProductsIDs) . '">';
                    $content              .= $table->getHtml();
                    $content              .= '</span>';
                } else {
                    $content = '<span class="wdp_bulk_table_content">';
                    $content .= $table->getHtml();
                    $content .= '</span>';
                }
            }
        } catch (Exception $exception) {

        }

        return $content;
    }

    /**
     * @param int|null $categoryID
     *
     * @return string
     */
    public function getCategoryTableContent($categoryID = null)
    {
        $content = "";

        try {
            $table = $this->getCategoryTable($categoryID);
            if ($table) {
                $content .= '<span class="wdp_bulk_table_content">';
                $content .= $table->getHtml();
                $content .= '</span>';
            }
        } catch (Exception $exception) {
            $content = "";
        }

        return $content;
    }

    /**
     * @param WC_Product $product
     *
     * @return SingleItemRule|null
     * @throws Exception
     */
    public function findRuleForProductTable($product)
    {
        if ( ! $product || ! ($product instanceof WC_Product)) {
            return null;
        }

        $context     = $this->context;
        $cartBuilder = new CartBuilder($context);
        $cart        = $cartBuilder->create(WC()->customer, WC()->session);
        $cartBuilder->populateCart($cart, WC()->cart);

        /** @var SingleItemRuleProcessor[] $ruleProcessors */
        $ruleProcessors = array();
        foreach (CacheHelper::loadActiveRules($context)->getRules() as $rule) {
            if ($rule instanceof SingleItemRule && $rule->getProductRangeAdjustmentHandler()) { // discount table only for 'SingleItem' rule
                $ruleProcessors[] = $rule->buildProcessor($context);
            }
        }

        $matchedRuleProcessor = null;
        if ( $product->get_children() ) {
            $availableProductsIDs = array_merge(array($product->get_id()), $product->get_children());

            foreach ($availableProductsIDs as $tmpProductId) {
                $tmpProduct = CacheHelper::getWcProduct($tmpProductId);
                if ( ! $tmpProduct) {
                    continue;
                }

                foreach ($ruleProcessors as $ruleProcessor) {
                    if ($ruleProcessor->isProductMatched($cart, $tmpProduct,
                        ! $context->getOption('discount_table_ignores_conditions'))) {
                        $matchedRuleProcessor = $ruleProcessor;
                        break;
                    }
                }
            }
        } else {
            if ( $tmpProduct = CacheHelper::getWcProduct($product->get_id())) {
                $tmpProduct->set_props($product->get_changes());

                foreach ($ruleProcessors as $ruleProcessor) {
                    if ($ruleProcessor->isProductMatched($cart, $tmpProduct,
                        ! $context->getOption('discount_table_ignores_conditions'))) {
                        $matchedRuleProcessor = $ruleProcessor;
                        break;
                    }
                }
            }
        }

        if ( ! $matchedRuleProcessor) {
            return null;
        }

        $rule = clone $matchedRuleProcessor->getRule();

        if ($context->getOption('discount_table_ignores_conditions')) {
            $rule->setConditions(array());
        }

        return $rule;
    }

    /**
     * @param SingleItemRule $rule
     *
     * @return Processor|null
     */
    public function makePriceProcessor($rule)
    {
        $context     = $this->context;
        $cartBuilder = new CartBuilder($context);

        $bulk_table_calculation_mode = $context->getOption('bulk_table_calculation_mode');

        if ($bulk_table_calculation_mode === 'only_bulk_rule_table') {
            /** @var CartCalculator $calc */
            $calc           = Factory::get("Cart_CartCalculator", $context, new RulesCollection(array($rule)));
            $priceProcessor = new Processor($context, $calc);
        } elseif ($bulk_table_calculation_mode === 'all') {
            if ( ! $context->getOption('discount_table_ignores_conditions')) {
                $priceProcessor = new Processor($context);
            } else {
                $newRules = array();
                foreach (CacheHelper::loadActiveRules($context)->getRules() as $loopRule) {
                    $newLoopRule = clone $loopRule;
                    $newLoopRule->setConditions(array());
                    $newRules[] = $newLoopRule;
                }

                /** @var CartCalculator $calc */
                $calc = Factory::get("Cart_CartCalculator", $context, new RulesCollection($newRules));

                $priceProcessor = new Processor($context, $calc);
            }
        } else {
            return null;
        }

        $priceProcessor->withCart($cartBuilder->create(WC()->customer, WC()->session));

        return $priceProcessor;
    }

    /**
     * @param WC_Product $product
     *
     * @return Table|null
     * @throws Exception
     */
    public function getProductTable($product)
    {
        $themeOptions   = array_replace_recursive($this->customizer->getThemeOptions(), $this->contextOptions);
        $contextOptions = $themeOptions[self::CONTEXT_PRODUCT_PAGE];
        $context        = $this->context;

        if ( ! empty($contextOptions['table']['simply_layout_force_percentage'])) {
            $contextOptions['table']['simply_layout_force_percentage'] = true;
        } else {
            $contextOptions['table']['simply_layout_force_percentage'] = false;
        }

        $rule = $this->findRuleForProductTable($product);
        if ( ! $rule) {
            return null;
        }

        $priceProcessor = $this->makePriceProcessor($rule);
        if ( ! $priceProcessor) {
            return null;
        }

        $table = new Table($context);

        $handler = $rule->getProductRangeAdjustmentHandler();

        /** HEADER */
        $headerTitle = '';
        if ($contextOptions['table_header']['use_message_as_title']) {
            $headerTitle = __(apply_filters('wdp_format_bulk_table_message', $handler->getPromotionalMessage()),
                'advanced-dynamic-pricing-for-woocommerce');
        } elseif ($handler::TYPE_BULK === $handler->getType()) {
            $headerTitle = $contextOptions['table_header']['bulk_title'];
        } elseif ($handler::TYPE_TIER === $handler->getType()) {
            $headerTitle = $contextOptions['table_header']['tier_title'];
        }
        $table->setTableHeader($headerTitle);

        /** COLUMNS AND ROWS */
        $ranges = $rule->getProductRangeAdjustmentHandler()->getRanges();

        if ($contextOptions['table']['table_layout'] === self::LAYOUT_SIMPLE) {
            $this->fillSimpleProductTable($table, $contextOptions, $rule, $priceProcessor, $product);
        } elseif ($contextOptions['table']['table_layout'] === self::LAYOUT_VERBOSE) {
            /** COLUMNS */
            $columns = $this->createColumnsForProductVerboseTable($contextOptions, $rule);
            foreach ($columns as $key => $title) {
                $table->addColumn($key, $title);
            }

            /** ROWS */
            foreach ($ranges as $range) {
                $row = array();
                foreach (array_keys($columns) as $key) {
                    $value     = $this->calculateColumnValueForProductVerboseTable($key, $range, $priceProcessor,
                        $product);
                    $row[$key] = ! is_null($value) ? $value : "-";
                }
                $table->addRow($row);
            }
        }

        /** FOOTER */
        $this->setUpFooter($table, $rule, $contextOptions);

        return apply_filters('adp_discount_product_table', $table, $contextOptions, $product, $rule, $priceProcessor);
    }

    /**
     * @param Table $table
     * @param array $contextOptions
     * @param SingleItemRule $rule
     * @param Processor $priceProcessor
     * @param \WC_Product $product
     */
    protected function fillSimpleProductTable(
        $table,
        $contextOptions,
        $rule,
        $priceProcessor,
        $product
    ) {
        $ranges = $rule->getProductRangeAdjustmentHandler()->getRanges();

        /** COLUMNS */
        $columns = array();
        foreach ($ranges as $index => $range) {
            if ($range->getFrom() == $range->getTo()) {
                $value = $range->getFrom();
            } else {
                if (is_infinite($range->getTo())) {
                    $value = $range->getFrom() . ' +';
                } else {
                    $value = $range->getFrom() . ' - ' . $range->getTo();
                }
            }

            $table->addColumn($index, apply_filters('adp_simple_discount_product_table_column', $value, $range));
            $columns[] = $range;
        }

        /**ROWS */
        $row = array();
        foreach (array_keys($columns) as $index) {
            $range    = $ranges[$index];
            $discount = $range->getData();

            $processedProd = $priceProcessor->calculateProduct($product, $range->getFrom());

            $value = null;
            if ( ! is_null($processedProd)) {
                if ($processedProd instanceof ProcessedVariableProduct) {
                    $lowestPriceProduct  = $processedProd->getLowestPriceProduct();
                    $highestPriceProduct = $processedProd->getHighestPriceProduct();

                    $value = "-";

                    if ( ! is_null($lowestPriceProduct) && ! is_null($highestPriceProduct)) {
                        $lowestPriceToDisplay  = $this->priceFunctions->getProcProductPriceToDisplay(
                            $lowestPriceProduct,
                            $lowestPriceProduct->getPrice($range->getFrom())
                        );
                        $highestPriceToDisplay = $this->priceFunctions->getProcProductPriceToDisplay(
                            $highestPriceProduct,
                            $highestPriceProduct->getPrice($range->getFrom())
                        );
                        if ($discount->getType() === $discount::TYPE_PERCENTAGE && $contextOptions['table']['simply_layout_force_percentage']) {
                            $value = "{$discount->getValue()}%";
                        } elseif ($lowestPriceToDisplay === $highestPriceToDisplay) {
                            $value = $this->priceFunctions->format($lowestPriceToDisplay);
                        }
                    }
                } elseif ($processedProd instanceof ProcessedProductSimple) {
                    if ($discount->getType() === $discount::TYPE_PERCENTAGE && $contextOptions['table']['simply_layout_force_percentage']) {
                        $value = "{$discount->getValue()}%";
                    } else {
                        $priceToDisplay = $this->priceFunctions->getProcProductPriceToDisplay(
                            $processedProd,
                            $processedProd->getPrice($range->getFrom())
                        );
                        $value          = $this->priceFunctions->format($priceToDisplay);
                    }
                }
            }

            $row[$index] = $value;
        }

        $table->addRow($row);
    }

    /**
     * @param array $contextOptions
     * @param SingleItemRule $rule
     *
     * @return array
     */
    protected function createColumnsForProductVerboseTable($contextOptions, $rule)
    {
        $ranges         = $rule->getProductRangeAdjustmentHandler()->getRanges();
        $columns        = array();
        $columns['qty'] = $contextOptions['table_columns']['qty_column_title'];

        $isFixedDiscount = false;
        foreach ($ranges as $index => $range) {
            /** @var Discount $discount */
            $discount = $range->getData();
            if ($discount->getType() === $discount::TYPE_FIXED_VALUE) {
                $isFixedDiscount = true;
            }
        }

        if ($contextOptions['table']['show_discount_column']) {
            if ($isFixedDiscount) {
                $columns['discounted_price'] = $contextOptions['table_columns']['discount_column_title_for_fixed_price'];
            } else {
                $columns['discount_value'] = $contextOptions['table_columns']['discount_column_title'];
            }
        }

        if ( ! $isFixedDiscount && $contextOptions['table']['show_discounted_price']) {
            $columns['discounted_price'] = $contextOptions['table_columns']['discounted_price_title'];
        }

        return $columns;
    }

    /**
     * @param string $key
     * @param Range $range
     * @param Processor $priceProcessor
     * @param \WC_Product $product
     *
     * @return string|null
     */
    protected function calculateColumnValueForProductVerboseTable(
        $key,
        $range,
        $priceProcessor,
        $product
    ) {
        $value    = null;
        $discount = $range->getData();

        switch ($key) {
            case 'qty':
                if ($range->getFrom() == $range->getTo()) {
                    $value = $range->getFrom();
                } else {
                    if (is_infinite($range->getTo())) {
                        $value = $range->getFrom() . ' +';
                    } else {
                        $value = $range->getFrom() . ' - ' . $range->getTo();
                    }
                }

                $value = apply_filters('adp_verbose_discount_product_table_cell_qty', $value, $range, $product,
                    $priceProcessor);
                break;
            case 'discount_value':
                if ($discount->getValue()) {
                    if ($discount::TYPE_PERCENTAGE === $discount->getType()) {
                        $value = "{$discount->getValue()}%";
                    } else {
                        $value = $this->priceFunctions->format($discount->getValue());
                    }
                }

                $value = apply_filters('adp_verbose_discount_product_table_cell_discount_value', $value, $range,
                    $product, $priceProcessor);
                break;
            case 'discounted_price':
                $processedProd = $priceProcessor->calculateProduct($product, $range->getFrom());

                if ( ! is_null($processedProd)) {
                    if ($processedProd instanceof ProcessedVariableProduct) {
                        $lowestPriceProduct  = $processedProd->getLowestPriceProduct();
                        $highestPriceProduct = $processedProd->getHighestPriceProduct();

                        $value = "-";

                        if ( ! is_null($lowestPriceProduct) && ! is_null($highestPriceProduct)) {
                            if ($this->context->getOption("bulk_table_prices_tax") === 'incl') {
                                $lowestPriceToDisplay = $this->priceFunctions->getPriceIncludingTax(
                                    $lowestPriceProduct->getProduct(),
                                    array(
                                        'price' => $lowestPriceProduct->getPrice($range->getFrom()),
                                        'qty'   => 1,
                                    )
                                );

                                $highestPriceToDisplay = $this->priceFunctions->getPriceIncludingTax(
                                    $highestPriceProduct->getProduct(),
                                    array(
                                        'price' => $highestPriceProduct->getPrice($range->getFrom()),
                                        'qty'   => 1,
                                    )
                                );
                            } elseif ($this->context->getOption("bulk_table_prices_tax") === 'excl') {
                                $lowestPriceToDisplay = $this->priceFunctions->getPriceExcludingTax(
                                    $lowestPriceProduct->getProduct(),
                                    array(
                                        'price' => $lowestPriceProduct->getPrice($range->getFrom()),
                                        'qty'   => 1,
                                    )
                                );

                                $highestPriceToDisplay = $this->priceFunctions->getPriceExcludingTax(
                                    $highestPriceProduct->getProduct(),
                                    array(
                                        'price' => $highestPriceProduct->getPrice($range->getFrom()),
                                        'qty'   => 1,
                                    )
                                );
                            } else {
                                $lowestPriceToDisplay  = $this->priceFunctions->getProcProductPriceToDisplay(
                                    $lowestPriceProduct,
                                    $lowestPriceProduct->getPrice($range->getFrom())
                                );
                                $highestPriceToDisplay = $this->priceFunctions->getProcProductPriceToDisplay(
                                    $highestPriceProduct,
                                    $highestPriceProduct->getPrice($range->getFrom())
                                );
                            }

                            if ($lowestPriceToDisplay === $highestPriceToDisplay) {
                                $value = $this->priceFunctions->format($lowestPriceToDisplay);
                            }
                        }
                    } elseif ($processedProd instanceof ProcessedProductSimple) {
                        if ($this->context->getOption("bulk_table_prices_tax") === 'incl') {
                            $priceToDisplay = $this->priceFunctions->getPriceIncludingTax(
                                $processedProd->getProduct(),
                                array(
                                    'price' => $processedProd->getPrice($range->getFrom()),
                                    'qty'   => 1,
                                )
                            );
                        } elseif ($this->context->getOption("bulk_table_prices_tax") === 'excl') {
                            $priceToDisplay = $this->priceFunctions->getPriceExcludingTax(
                                $processedProd->getProduct(),
                                array(
                                    'price' => $processedProd->getPrice($range->getFrom()),
                                    'qty'   => 1,
                                )
                            );
                        } else {
                            $priceToDisplay = $this->priceFunctions->getProcProductPriceToDisplay(
                                $processedProd,
                                $processedProd->getPrice($range->getFrom())
                            );
                        }

                        $value = $this->priceFunctions->format($priceToDisplay);
                    }
                }

                $value = apply_filters('adp_verbose_discount_product_table_cell_discounted_price', $value, $range,
                    $product, $priceProcessor);
                break;
        }

        return $value;
    }

    /**
     * @param int|null $termId
     *
     * @return SingleItemRule|null
     * @throws Exception
     */
    public function findRuleForCategoryTable($termId)
    {
        if ( ! $termId || ! is_int($termId)) {
            return null;
        }

        $context     = $this->context;
        $cartBuilder = new CartBuilder($context);
        $cart        = $cartBuilder->create(WC()->customer, WC()->session);
        $cartBuilder->populateCart($cart, WC()->cart);

        /** @var SingleItemRuleProcessor[] $ruleProcessors */
        $ruleProcessors = array();
        foreach (CacheHelper::loadActiveRules($context)->getRules() as $rule) {
            if ($rule instanceof SingleItemRule && $rule->getProductRangeAdjustmentHandler()) { // discount table only for 'SingleItem' rule
                $ruleProcessors[] = $rule->buildProcessor($context);
            }
        }

        $matchedRuleProcessor = null;
        foreach ($ruleProcessors as $ruleProcessor) {
            if ($ruleProcessor->isCategoryMatched($cart, $termId,
                ! $context->getOption('discount_table_ignores_conditions'))) {
                $matchedRuleProcessor = $ruleProcessor;
                break;
            }
        }

        if ( ! $matchedRuleProcessor) {
            return null;
        }

        $rule = clone $matchedRuleProcessor->getRule();

        if ($context->getOption('discount_table_ignores_conditions')) {
            $rule->setConditions(array());
        }

        return $rule;
    }


    /**
     * @param int|null $termId
     *
     * @return Table|null
     * @throws Exception
     */
    public function getCategoryTable($termId = null)
    {
        if ( ! $termId) {
            if (is_tax()) {
                global $wp_query;
                if (isset($wp_query->queried_object->term_id)) {
                    $termId = $wp_query->queried_object->term_id;
                }
            }

            if ( ! $termId) {
                return null;
            }
        }

        $themeOptions   = $this->customizer->getThemeOptions();
        $contextOptions = $themeOptions[self::CONTEXT_CATEGORY_PAGE];
        $context        = $this->context;

        if ( ! ($rule = $this->findRuleForCategoryTable($termId))) {
            return null;
        }

        $table   = new Table($context);
        $handler = $rule->getProductRangeAdjustmentHandler();

        /** HEADER */
        $headerTitle = '';
        if ($contextOptions['table_header']['use_message_as_title']) {
            $headerTitle = __(apply_filters('wdp_format_bulk_table_message', $handler->getPromotionalMessage()),
                'advanced-dynamic-pricing-for-woocommerce');
        } elseif ($handler::TYPE_BULK === $handler->getType()) {
            $headerTitle = $contextOptions['table_header']['bulk_title'];
        } elseif ($handler::TYPE_TIER === $handler->getType()) {
            $headerTitle = $contextOptions['table_header']['tier_title'];
        }
        $table->setTableHeader($headerTitle);

        /** COLUMNS AND ROWS */
        $ranges = $rule->getProductRangeAdjustmentHandler()->getRanges();
        if ($contextOptions['table']['table_layout'] === self::LAYOUT_SIMPLE) {
            return null;
        } elseif ($contextOptions['table']['table_layout'] === self::LAYOUT_VERBOSE) {
            /** COLUMNS */
            $columns = $this->createColumnsForCategoryVerboseTable($contextOptions, $rule);
            foreach ($columns as $key => $title) {
                $table->addColumn($key, $title);
            }

            /** ROWS */
            foreach ($ranges as $range) {
                $row = array();
                foreach (array_keys($columns) as $key) {
                    $value     = $this->calculateColumnValueForCategoryVerboseTable($key, $range);
                    $row[$key] = ! is_null($value) ? $value : "-";
                }

                $table->addRow($row);
            }
        }

        /** FOOTER */
        $this->setUpFooter($table, $rule, $contextOptions);


        return $table;
    }

    /**
     * @param array $contextOptions
     * @param SingleItemRule $rule
     *
     * @return array
     */
    protected function createColumnsForCategoryVerboseTable($contextOptions, $rule)
    {
        $ranges         = $rule->getProductRangeAdjustmentHandler()->getRanges();
        $columns        = array();
        $columns['qty'] = $contextOptions['table_columns']['qty_column_title'];

        $isFixedDiscount = false;
        foreach ($ranges as $index => $range) {
            /** @var Discount $discount */
            $discount = $range->getData();
            if ($discount->getType() === $discount::TYPE_FIXED_VALUE) {
                $isFixedDiscount = true;
            }
        }

        if ($contextOptions['table']['show_discount_column']) {
            if ($isFixedDiscount) {
                $columns['discount_value'] = $contextOptions['table_columns']['discount_column_title_for_fixed_price'];
            } else {
                $columns['discount_value'] = $contextOptions['table_columns']['discount_column_title'];
            }
        }

        return $columns;
    }

    /**
     * @param string $key
     * @param Range $range
     *
     * @return string|null
     */
    protected function calculateColumnValueForCategoryVerboseTable($key, $range)
    {
        $discount = $range->getData();
        $value    = null;

        switch ($key) {
            case 'qty':
                if ($range->getFrom() == $range->getTo()) {
                    $value = $range->getFrom();
                } else {
                    if (is_infinite($range->getTo())) {
                        $value = $range->getFrom() . ' +';
                    } else {
                        $value = $range->getFrom() . ' - ' . $range->getTo();
                    }
                }

                $value = apply_filters('adp_verbose_discount_category_table_cell_qty', $value, $range);
                break;
            case 'discount_value':
                if ($discount->getValue()) {
                    if ($discount::TYPE_PERCENTAGE === $discount->getType()) {
                        $value = "{$discount->getValue()}%";
                    } else {
                        $value = $this->priceFunctions->format($discount->getValue());
                    }
                }

                $value = apply_filters('adp_verbose_discount_category_table_cell_discount_value', $value, $range);
                break;
        }

        return $value;
    }

    /**
     * @param array $contextOptions
     */
    public function setContextOptions($contextOptions)
    {
        $this->contextOptions = $contextOptions;
    }

    /**
     * @param Table $table
     * @param SingleItemRule $rule
     * @param array $contextOptions
     */
    protected function setUpFooter($table, $rule, $contextOptions)
    {
        $isShowFooter = $contextOptions['table']['show_footer'];
        $footerText   = '';
        if ($isShowFooter) {
            if ($rule->getProductRangeAdjustmentHandler()->getPromotionalMessage()) {
                $useMessageAsHeader = $contextOptions['table_header']['use_message_as_title'];

                if ( ! $useMessageAsHeader) {
                    $footerText = "<p>" . $rule->getProductRangeAdjustmentHandler()->getPromotionalMessage() . "</p>";
                }
            } else {
                $footerText       = '';
                $humanizedFilters = $this->filtersFormatter->formatRule($rule);
                if ($humanizedFilters) {
                    $footerText = "<div>" . __('Bulk pricing will be applied to package:',
                            'advanced-dynamic-pricing-for-woocommerce') . "</div>";
                    $footerText .= "<ul>";
                    foreach ($humanizedFilters as $filterText) {
                        $footerText .= "<li>" . $filterText . "</li>";
                    }
                    $footerText .= "</ul>";
                }
            }
        }

        $table->setTableFooter($footerText);
    }
}
