<?php


namespace ADP\BaseVersion\Includes\External\Cmp;


use ADP\BaseVersion\Includes\Context;

class PDFProductVouchersCmp
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var \WC_PDF_Product_Vouchers
     */
    private $voucher;

    public function __construct($context)
    {
        $this->context = $context;
        $this->loadRequirements();
    }

    public function loadRequirements()
    {
        if ( ! did_action('plugins_loaded')) {
            _doing_it_wrong(__FUNCTION__, sprintf(__('%1$s should not be called earlier the %2$s action.',
                'advanced-dynamic-pricing-for-woocommerce'), 'load_requirements', 'plugins_loaded'), WC_ADP_VERSION);
        }

        $this->loadVoucher();
    }

    private function loadVoucher()
    {
        if (function_exists('wc_pdf_product_vouchers')) {
            $this->voucher = wc_pdf_product_vouchers();
        }
    }

    public function isActive()
    {
        return ! is_null($this->voucher);
    }
}
