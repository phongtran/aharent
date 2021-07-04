<?php

namespace ADP\BaseVersion\Includes\External\Cmp;

use ADP\BaseVersion\Includes\Context;
use WC_Deposits_Cart_Manager;

class WcDepositsCmp
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return defined("WC_DEPOSITS_VERSION");
    }

    /**
     * @param \WC_Cart $wcCart
     */
    public function updateDepositsData($wcCart)
    {
        if ( ! class_exists("WC_Deposits_Cart_Manager")) {
            return;
        }

        WC_Deposits_Cart_Manager::get_instance()->get_cart_from_session($wcCart);

        $wcNoFilterWorker = new \ADP\BaseVersion\Includes\External\WC\WcNoFilterWorker();
        $wcNoFilterWorker->calculateTotals($wcCart, $wcNoFilterWorker::FLAG_ALLOW_TOTALS_HOOKS);
    }
}
