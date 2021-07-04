<?php


namespace ADP\BaseVersion\Includes\External\WC;


use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\Engine;
use ADP\BaseVersion\Includes\Product\ProcessedProductSimple;
use ADP\BaseVersion\Includes\Product\ProcessedVariableProduct;

class StructuredData
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Engine
     */
    protected $globalEngine;

    /**
     * @param Context $context
     * @param Engine $engine
     */
    public function __construct($context, $engine = null)
    {
        $this->context      = $context;
        $this->globalEngine = $engine;
    }

    public function install()
    {
        add_filter('woocommerce_structured_data_product_offer', array($this, 'structuredProductData'), 10, 2);
    }

    /**
     * @param array $data
     * @param \WC_Product $product
     *
     * @return array
     */
    public function structuredProductData($data, $product)
    {
        if ( ! $this->globalEngine) {
            return $data;
        }

        if (is_object($product) && $product->get_price()) {
            $productProcessor = $this->globalEngine->getProductProcessor();
            $processedProduct = $productProcessor->calculateProduct($product, 1);

            if (is_null($processedProduct)) {
                return $data;
            }

            if ($processedProduct instanceof ProcessedVariableProduct) {
                $data['lowPrice']  = wc_format_decimal($processedProduct->getLowestPrice(), wc_get_price_decimals());
                $data['highPrice'] = wc_format_decimal($processedProduct->getHighestPrice(), wc_get_price_decimals());
            } elseif ($processedProduct instanceof ProcessedProductSimple) {
                $data['price'] = wc_format_decimal($processedProduct->getPrice(), wc_get_price_decimals());
            }
        }

        return $data;
    }
}
