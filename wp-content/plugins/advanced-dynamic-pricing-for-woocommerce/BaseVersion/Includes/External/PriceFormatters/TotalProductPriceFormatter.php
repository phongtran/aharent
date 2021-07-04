<?php

namespace ADP\BaseVersion\Includes\External\PriceFormatters;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\PriceFunctions;
use ADP\BaseVersion\Includes\Product\ProcessedProductSimple;
use ADP\BaseVersion\Includes\Product\ProcessedVariableProduct;

class TotalProductPriceFormatter
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Formatter
     */
    protected $formatter;

    /**
     * @var PriceFunctions
     */
    protected $priceFunctions;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context   = $context;
        $this->formatter = new Formatter($context);
        $this->formatter->setTemplate(htmlspecialchars_decode($this->context->getOption("total_price_for_product_template",
            "Total price : {{striked_total}}")));
        $this->priceFunctions = new PriceFunctions($context);
    }

    /**
     * @param \WC_Product $product
     * @param float $qty
     *
     * @return string
     */
    public function getHtmlAreRulesNotApplied($product, $qty)
    {
        $price        = $product->get_price('edit');
        $regularPrice = $product->get_regular_price('edit');
        $isOnSale     = $product->is_on_sale('edit');

        $strikedTotal = $isOnSale ?
            $this->priceFunctions->formatSalePrice(
                $this->priceFunctions->getPriceToDisplay($product,
                    array('qty' => $qty, 'price' => $regularPrice)),
                $this->priceFunctions->getPriceToDisplay($product, array('qty' => $qty, 'price' => $price))
            ) :
            $this->priceFunctions->format(
                $this->priceFunctions->getPriceToDisplay($product,
                    array('qty' => $qty, 'price' => $regularPrice))
            );

        $total = $this->priceFunctions->format(
            $this->priceFunctions->getPriceToDisplay($product,
                array('qty' => $qty, 'price' => $regularPrice))
        );

        $replacements = array(
            'striked_total' => $strikedTotal,
            'total'         => $total,
        );

        return $this->formatter->applyReplacements($replacements);
    }

    /**
     * @param \WC_Product $product
     * @param float $qty
     *
     * @return string
     */
    public function getHtmlNotIsModifyNeeded($product, $qty)
    {
        $strikedTotal = $this->priceFunctions->format($this->priceFunctions->getPriceToDisplay($product,
            array('qty' => $qty)));
        $total        = $strikedTotal;

        $replacements = array(
            'striked_total' => $strikedTotal,
            'total'         => $total,
        );

        return $this->formatter->applyReplacements($replacements);
    }

    /**
     * @param ProcessedProductSimple $processedProduct
     *
     * @return string
     */
    public function getHtmlProcessedProductSimple($processedProduct)
    {

        $strikethrough = $this->context->getOption('show_striked_prices_product_page', true);
        $strikedTotal = $processedProduct->getSubtotalHtml($strikethrough);
        $total        = $this->priceFunctions->format($this->priceFunctions->getPriceToDisplay($processedProduct->getProduct(),
            array('price' => $processedProduct->getCalculatedPrice(), 'qty' => $processedProduct->getQty())));

        $replacements = array(
            'striked_total' => $strikedTotal,
            'total'         => $total,
        );

        return $this->formatter->applyReplacements($replacements);
    }
}
