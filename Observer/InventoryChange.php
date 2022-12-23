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

class InventoryChange
{
    public const STOCK_ITEM_DATA_KEY = 'CED_GOODMARKET_STOCK_DATA_';

    /**
     * Object Manager
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * Data Helper
     * @var \Ced\GoodMarket\Helper\Data
     */
    public $dataHelper;
    /**
     * @var \Ced\GoodMarket\Helper\MultiAccount
     */
    public $multiaccounthelper;

    /** @var \Magento\Framework\Registry $registry */
    public $registry;

    /** @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory */
    public $stockItemFactory;

    /** @var \Ced\GoodMarket\Helper\Logger $logger */
    public $logger;

    /** @var \Ced\GoodMarket\Helper\Config $configHelper */
    public $configHelper;

    /**
     * ProductSaveAfter constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\GoodMarket\Helper\Logger $logger
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\GoodMarket\Helper\Logger $logger,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->objectManager = $objectManager;
        $this->stockItemFactory = $stockItemFactory;
        $this->registry = $registry;
        $this->logger = $logger;
    }

    /**
     * processChangedProduct
     *
     * @param $stockItem
     * @param $origData
     * @return bool
     */
    public function processChangedProduct($stockItem, $origData)
    {
        try {
            if (empty($stockItem) || empty($origData)) {
                return false;
            }
            $productId = $stockItem->getProductId();
            //capture stock change
            $orgQty = (int)$origData->getOrigData('qty');
            $newQty = (int)$stockItem->getData('qty');

            $orgIsInStock = $origData->getOrigData('is_in_stock');
            $newIsInStock = $stockItem->getData('is_in_stock');
            if ($orgQty != $newQty || $orgIsInStock != $newIsInStock) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $this->helperProduct=$objectManager->create(\Ced\GoodMarket\Helper\Product::class);
                $product = $objectManager->get(\Magento\Catalog\Model\Product::class)->load($productId);
                if (!empty($product->getData('goodmarket_product_id'))) {
                    $this->helperProduct->updateBulkInventory($product->getId());
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
