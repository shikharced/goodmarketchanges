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

class Shipment implements ObserverInterface
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
        $autoDespatch = $this->config->getAutoDespatch();
        if ($autoDespatch) {
            $this->logger = $this->objectManager->create(\Ced\GoodMarket\Helper\Logger::class);
            $purchaseOrderId = '';
            $orderData = [];
            try {
                $event = $observer->getEvent();
                $eventName = $event->getName();
                if($eventName == 'sales_order_shipment_save_after') {
                    $track = $event;
                } else {
                    $track = $observer->getEvent()->getTrack();
                }
                $shipment = $track->getShipment();

                //$shipment = $observer->getEvent()->getShipment();
                $order = $shipment->getOrder();
                $shippingMethod = $order->getShippingMethod();
                $trackArray = [];
                $shipData='';
                foreach ($shipment->getAllTracks() as $track) {
                    $trackArray = $track->getData();
                }
                $incrementId=$order->getIncrementId();
                $orderCollection =$this->collection->create()->load($incrementId, 'magento_increment_id');
                if(isset($orderCollection) && !empty($orderCollection)) {
                    if (empty($trackArray)) {
                        $shipData = $this->data->createOrderShipment(json_decode($orderCollection->getData('order_data'), true));
                    } else {
                        $shipData = $this->data->createTrackOrderShipment(json_decode($orderCollection->getData('order_data'), true), $trackArray);
                    }


                    if (isset($shipData) && ($shipData == 1)) {
                        $orderCollection->setStatus('Shipped');
                        $orderCollection->save();
                    }
                }


            } catch (\Exception $e) {
                $this->logger->addError(
                    $e->getMessage() . "Exception in Shipment"
                );
                return $observer;
            } catch (\Error $e) {;
                $this->logger->addError(
                    $e->getMessage() . "Error in Shipment"
                );
                return $observer;
            }
        }
        return $observer;
    }
}
