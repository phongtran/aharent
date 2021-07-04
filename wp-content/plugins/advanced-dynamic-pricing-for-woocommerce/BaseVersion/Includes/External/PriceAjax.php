<?php

namespace ADP\BaseVersion\Includes\External;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\PriceFormatters\TotalProductPriceFormatter;
use ADP\BaseVersion\Includes\External\WC\PriceFunctions;
use ADP\BaseVersion\Includes\Product\ProcessedProductSimple;
use ADP\BaseVersion\Includes\Product\ProcessedVariableProduct;

class PriceAjax
{
    const ACTION_GET_SUBTOTAL_HTML = 'get_price_product_with_bulk_table';
    const ACTION_CALCULATE_SEVERAL_PRODUCTS = 'adp_calculate_several_products';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var PriceFunctions
     */
    protected $priceFunctions;

    /**
     * @param Context $context
     * @param Engine $engine
     */
    public function __construct($context, Engine $engine)
    {
        $this->context        = $context;
        $this->engine         = $engine;
        $this->priceFunctions = new PriceFunctions($context);
    }

    public function register()
    {
        add_action("wp_ajax_nopriv_" . self::ACTION_GET_SUBTOTAL_HTML, array($this, "ajaxCalculatePrice"));
        add_action("wp_ajax_" . self::ACTION_GET_SUBTOTAL_HTML, array($this, "ajaxCalculatePrice"));

        add_action("wp_ajax_nopriv_" . self::ACTION_CALCULATE_SEVERAL_PRODUCTS,
            array($this, "ajaxCalculateSeveralProducts"));
        add_action("wp_ajax_" . self::ACTION_CALCULATE_SEVERAL_PRODUCTS, array($this, "ajaxCalculateSeveralProducts"));
    }

    public function ajaxCalculatePrice()
    {
        $prodId     = ! empty($_REQUEST['product_id']) ? intval($_REQUEST['product_id']) : false;
        $qty        = ! empty($_REQUEST['qty']) ? floatval($_REQUEST['qty']) : false;
        $attributes = ! empty($_REQUEST['attributes']) ? (array)$_REQUEST['attributes'] : array();

        $pageData  = ! empty($_REQUEST['page_data']) ? (array)$_REQUEST['page_data'] : array();
        $isProduct = isset($pageData['is_product']) ? wc_string_to_bool($pageData['is_product']) : null;

        $customPrice = null;
        if ( ! empty($_REQUEST['custom_price'])) {
            $customPrice = $this->parseCustomPrice($_REQUEST['custom_price']);
        }

        if ( ! $prodId || ! $qty) {
            wp_send_json_error();
        }

        $context = $this->context;

        $context->setProps(array(
            $context::ADMIN           => false,
            $context::AJAX            => false,
            $context::WC_PRODUCT_PAGE => $isProduct,
        ));

        $result = $this->calculatePrice($prodId, $qty, $attributes, $customPrice);

        if ($result === null) {
            wp_send_json_error();
        } else {
            wp_send_json_success($result);
        }
    }

    public function ajaxCalculateSeveralProducts()
    {
        $list = ! empty($_REQUEST['products_list']) ? $_REQUEST['products_list'] : array();

        if ( ! is_array($list) || count($list) === 0) {
            wp_send_json_success(array());
        }

        $readyList = array();
        foreach ($list as $item) {
            $productId   = isset($item['product_id']) ? intval($item['product_id']) : 0;
            $qty         = isset($item['qty']) ? floatval($item['qty']) : floatval(0);
            $customPrice = isset($item['custom_price']) ? $this->parseCustomPrice($item['custom_price']) : null;
            $attributes  = isset($item['attributes']) ? $item['attributes'] : array();

            if ($productId === 0 || $qty === floatval(0)) {
                continue;
            }

            $readyList[] = array(
                'product_id'   => $productId,
                'qty'          => $qty,
                'custom_price' => $customPrice,
                'attributes'   => $attributes,
            );
        }

        if (count($readyList) === 0) {
            wp_send_json_success(array());
        }

        $pageData  = ! empty($_REQUEST['page_data']) ? (array)$_REQUEST['page_data'] : array();
        $isProduct = isset($pageData['is_product']) ? wc_string_to_bool($pageData['is_product']) : null;

        $context = $this->context;
        $context->setProps(array(
            $context::ADMIN           => false,
            $context::AJAX            => false,
            $context::WC_PRODUCT_PAGE => $isProduct,
        ));

        $result = array();
        foreach ($readyList as $item) {
            $result[$item['product_id']] = $this->calculatePrice($item['product_id'], $item['qty'],
                $item['attributes'], $item['custom_price']);
        }

        wp_send_json_success($result);
    }

    /**
     * @param string $customPrice
     *
     * @return float|null
     */
    protected function parseCustomPrice($customPrice)
    {
        $result = null;

        if (preg_match('/\d+\\' . wc_get_price_decimal_separator() . '\d+/', $customPrice, $matches) !== false) {
            $result = floatval(reset($matches));
        }

        return $result;
    }

    /**
     * @param int $productId
     * @param float $qty
     * @param array<string, string> $attributes
     * @param float|null $customPrice
     *
     * @return array|null
     */
    protected function calculatePrice($productId, $qty, $attributes = array(), $customPrice = null)
    {
        $product = CacheHelper::getWcProduct($productId);
        if ($customPrice !== null) {
            $product->set_price($customPrice);
        }

        if ($product instanceof \WC_Product_Variation && array_filter($attributes)) {
            $product->set_attributes(array_filter($attributes));
        }

        $processedProduct = $this->engine->getProductProcessor()->calculateProduct($product, $qty);

        if (is_null($processedProduct)) {
            return null;
        }

        $priceDisplay  = $this->engine->getPriceDisplay();
        $strikethrough = $priceDisplay::priceHtmlIsAllowToStrikethroughPrice($this->context);

        $totalProductPriceFormatter = new TotalProductPriceFormatter($this->context);

        if ( ! $processedProduct->areRulesApplied()) {
            $price        = $product->get_price('edit');
            $regularPrice = $product->get_regular_price('edit');
            $isOnSale     = $product->is_on_sale('edit');

            return array(
                'price_html'          => $processedProduct->getPriceHtml($strikethrough),
                'subtotal_html'       => $isOnSale ?
                    $this->priceFunctions->formatSalePrice(
                        $this->priceFunctions->getPriceToDisplay($product,
                            array('qty' => $qty, 'price' => $regularPrice)),
                        $this->priceFunctions->getPriceToDisplay($product, array('qty' => $qty, 'price' => $price))
                    ) :
                    $this->priceFunctions->format(
                        $this->priceFunctions->getPriceToDisplay($product,
                            array('qty' => $qty, 'price' => $regularPrice))
                    ),
                'total_price_html'    => $totalProductPriceFormatter->getHtmlAreRulesNotApplied($product, $qty),
                'original_price'      => floatval($regularPrice),
                'discounted_price'    => floatval($price),
                'original_subtotal'   => $this->priceFunctions->getPriceToDisplay(
                    $product, array('qty' => $qty, 'price' => $regularPrice)
                ),
                'discounted_subtotal' => $this->priceFunctions->getPriceToDisplay(
                    $product, array('qty' => $qty, 'price' => $price)
                ),
            );
        }

        if ( ! $priceDisplay->priceHtmlIsModifyNeeded()) {
            return array(
                'price_html'          => $this->priceFunctions->format($this->priceFunctions->getPriceToDisplay($product)),
                'subtotal_html'       => $this->priceFunctions->format($this->priceFunctions->getPriceToDisplay($product,
                    array('qty' => $qty))),
                'total_price_html'    => $totalProductPriceFormatter->getHtmlNotIsModifyNeeded($product, $qty),
                'original_price'      => $this->priceFunctions->getPriceToDisplay($product),
                'discounted_price'    => $this->priceFunctions->getPriceToDisplay($product),
                'original_subtotal'   => $this->priceFunctions->getPriceToDisplay($product, array('qty' => $qty)),
                'discounted_subtotal' => $this->priceFunctions->getPriceToDisplay($product, array('qty' => $qty)),
            );
        }

        if ($processedProduct instanceof ProcessedProductSimple) {
            /** @var ProcessedProductSimple $processedProduct */
            return array(
                'price_html'          => $processedProduct->getPriceHtml($strikethrough),
                'subtotal_html'       => $processedProduct->getSubtotalHtml($strikethrough),
                'total_price_html'    => $totalProductPriceFormatter->getHtmlProcessedProductSimple($processedProduct),
                'original_price'      => $this->priceFunctions->getPriceToDisplay($product,
                    array('price' => $processedProduct->getOriginalPrice())),
                'discounted_price'    => $this->priceFunctions->getPriceToDisplay($product,
                    array('price' => $processedProduct->getCalculatedPrice())),
                'original_subtotal'   => $this->priceFunctions->getPriceToDisplay($product,
                    array('price' => $processedProduct->getOriginalPrice(), 'qty' => $qty)),
                'discounted_subtotal' => $this->priceFunctions->getPriceToDisplay($product,
                    array('price' => $processedProduct->getCalculatedPrice(), 'qty' => $qty)),
            );
        } elseif ($processedProduct instanceof ProcessedVariableProduct) {
            /** @var ProcessedVariableProduct $processedProduct */

            $lowestOrigPriceProduct  = $processedProduct->getLowestInitialPriceProduct();
            $highestOrigPriceProduct = $processedProduct->getHighestInitialPriceProduct();
            $lowestPriceProduct      = $processedProduct->getLowestPriceProduct();
            $highestPriceProduct     = $processedProduct->getHighestPriceProduct();

            return array(
                'price_html'       => $processedProduct->getPriceHtml($strikethrough),
                'subtotal_html'    => $processedProduct->getSubtotalHtml($strikethrough),
                'total_price_html' => "",

                'lowest_original_price'    => $this->priceFunctions->getPriceToDisplay($lowestOrigPriceProduct->getProduct(),
                    array('price' => $lowestOrigPriceProduct->getOriginalPrice())),
                'highest_original_price'   => $this->priceFunctions->getPriceToDisplay($highestOrigPriceProduct->getProduct(),
                    array('price' => $highestOrigPriceProduct->getOriginalPrice())),
                'lowest_discounted_price'  => $this->priceFunctions->getPriceToDisplay($lowestPriceProduct->getProduct(),
                    array('price' => $lowestPriceProduct->getCalculatedPrice())),
                'highest_discounted_price' => $this->priceFunctions->getPriceToDisplay($highestPriceProduct->getProduct(),
                    array('price' => $highestPriceProduct->getCalculatedPrice())),

                'lowest_original_subtotal'    => $this->priceFunctions->getPriceToDisplay($lowestOrigPriceProduct->getProduct(),
                    array('price' => $lowestOrigPriceProduct->getOriginalPrice(), 'qty' => $qty)),
                'highest_original_subtotal'   => $this->priceFunctions->getPriceToDisplay($highestOrigPriceProduct->getProduct(),
                    array('price' => $highestOrigPriceProduct->getOriginalPrice(), 'qty' => $qty)),
                'lowest_discounted_subtotal'  => $this->priceFunctions->getPriceToDisplay($lowestPriceProduct->getProduct(),
                    array('price' => $lowestPriceProduct->getCalculatedPrice(), 'qty' => $qty)),
                'highest_discounted_subtotal' => $this->priceFunctions->getPriceToDisplay($highestPriceProduct->getProduct(),
                    array('price' => $highestPriceProduct->getCalculatedPrice(), 'qty' => $qty)),
            );
        }

        return null;
    }
}
