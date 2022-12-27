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

use Ced\GoodMarket\Observer\InventoryChange;
use Magento\Framework\Event\ObserverInterface;

class ProductSavedAfter extends InventoryChange implements ObserverInterface
{
    /**
     * Catalog product save after event handler
     *
     * @param object $observer
     * @return \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $stockItem = $observer->getEvent()->getItem();
            if (empty($stockItem)) {
                return $observer;
            }
            $stockItemId = $stockItem->getItemId();
            $stockRegistryKey = $this::STOCK_ITEM_DATA_KEY . $stockItemId;
            $origData = $this->registry->registry($stockRegistryKey);
            $this->processChangedProduct($stockItem, $origData);
            return $observer;
        } catch (\Exception $e) {
            $this->logger->error(
                'Inv Save Observer',
                [
                    'path' => __METHOD__,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );
            return $observer;
        }
    }
}
