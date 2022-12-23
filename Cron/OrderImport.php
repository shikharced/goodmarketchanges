<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Wish
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Cron;

/**
 * class OrderImport cron
 */
class OrderImport
{
    /**
     * @var \Ced\GoodMarket\Helper\Order
     */
    public $order;

    /**
     * @var \Ced\GoodMarket\Helper\Config
     */
    public $config;

    /**
     * OrderImport constructor.
     *
     * @param \Ced\GoodMarket\Helper\Order $order
     * @param \Ced\GoodMarket\Helper\Config $config
     */
    public function __construct(
        \Ced\GoodMarket\Helper\Order $order,
        \Ced\GoodMarket\Helper\Config $config
    ) {
        $this->order = $order;
        $this->config = $config;
    }

    /**
     * Execute method
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        if ($this->config->getOrderCronStatus()) {
            $this->order->import();
        }
    }
}
