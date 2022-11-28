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
 * @category    Ced
 * @package     Ced_GoodMarket
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Observer;

use Magento\Framework\Event\ObserverInterface;

class Invoice implements ObserverInterface
{
    /**
     * Request
     * @var  \Magento\Framework\App\RequestInterface
     */
    public $request;

    /**
     * Object Manager
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * Registry
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Ced\GoodMarket\Helper\Logger $logger
     */
    public $logger;

    /**
     * Shipment constructor.
     * @param \Ced\GoodMarket\Helper\Logger $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Ced\GoodMarket\Helper\Config $config,
        \Ced\GoodMarket\Model\OrderFactory $collection,
        \Ced\GoodMarket\Helper\Data $data
    ) {
        $this->request = $request;
        $this->registry = $registry;
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->collection=$collection;
        $this->data=$data;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer|void
     */
    // phpcs:ignore Generic.Metrics.NestingLevel
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $invoice = $observer->getEvent()->getInvoice();
            $order = $invoice->getOrder();
            $incrementId = $order->getIncrementId();
            $orderCollection = $this->collection->create();
            $orderCollection =$this->collection->create()->load($incrementId, 'magento_increment_id');
            if(isset($orderCollection) && !empty($orderCollection)) {
                $invoice = $this->data->createOrderInvoice(json_decode($orderCollection->getData('order_data'), true));

                if (isset($invoice) && ($invoice == 1)) {
                    $orderCollection->setStatus('Invoiced');
                    $orderCollection->save();
                }
            }
            return $observer;
        }catch (\Exception $e)
        {
            $this->logger->addError(
                $e->getMessage() . "Exception in Invoice Creation"
            );
            return $observer;
        }
        catch (\Error $e)
        {
            $this->logger->addError(
                $e->getMessage() . "Error in Invoice Creation"
            );
            return $observer;
        }
    }
}
