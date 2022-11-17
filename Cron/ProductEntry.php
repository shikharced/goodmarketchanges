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

use Ced\GoodMarket\Model\Source\Cron\Status;
use Ced\GoodMarket\Model\Source\Cron\Type;

class ProductEntry
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public $productCollection;

    /**
     * @var \Ced\GoodMarket\Helper\Logger
     */
    public $logger;

    /**
     * @var \Ced\GoodMarket\Helper\Config
     */
    public $config;

    /**
     * @var \Ced\GoodMarket\Helper\Product
     */
    public $product;

    /**
     * @var \Ced\GoodMarket\Model\CronFactory
     */
    public $cron;

    /**
     * @var \Ced\GoodMarket\Model\ResourceModel\Cron\CollectionFactory
     */
    public $cronCollection;

    /**
     * InventoryPrice constructor.
     * @param \Ced\GoodMarket\Helper\Logger $logger
     * @param \Ced\GoodMarket\Helper\Config $config
     * @param \Ced\GoodMarket\Helper\Product $product
     * @param \Ced\GoodMarket\Model\CronFactory $cronFactory
     * @param \Ced\GoodMarket\Model\ResourceModel\Cron\CollectionFactory $cronCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Ced\GoodMarket\Helper\Logger $logger,
        \Ced\GoodMarket\Helper\Config $config,
        \Ced\GoodMarket\Helper\Product $product,
        \Ced\GoodMarket\Model\ProductFactory $cronFactory,
        \Ced\GoodMarket\Model\ResourceModel\Product\CollectionFactory $cronCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory)
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->product = $product;
        $this->cron = $cronFactory;
        $this->cronCollection = $cronCollectionFactory;
        $this->productCollection = $productCollectionFactory;
    }

    /**
     * Fetch Order From GoodMarket
     */
    public function execute()
    {
        try {
                $cronModel = $this
                    ->cronCollection
                    ->create()
                    ->addFieldToFilter('status', Status::PENDING)
                    ->addFieldToFilter('cron_type', Type::INVENTORY)
                    ->getFirstItem();

                if ($cronModel && $cronModel->getId()) {
                    $chunkIds = json_decode($cronModel->getChunkIds(), true);
                    if (!empty($chunkIds)) {
                        $response = $this->product->updateBulkInventory($chunkIds);
                        $cronModel->setData('status', Status::SUBMITTED);
                        $cronModel->save();
                    }
                } else {
                    $productIds = $this
                        ->productCollection
                        ->create()
                        ->addFieldToFilter('goodmarket_profile_id', ['neq' => ''])
                        ->addFieldToFilter('goodmarket_product_id', ['neq' => ''])
                        ->addAttributeToFilter('type_id', ['in' => ['simple', 'configurable']])
                        ->addAttributeToFilter('visibility', ['neq' => 1])
                        ->getAllIds();
                    if (!empty($productIds)) {
                        $productChunk = array_chunk($productIds, 5);
                        foreach ($productChunk as $product_ids) {
                            $cronObject = $this->cron->create();
                            $cronObject->setData('cron_type', Type::INVENTORY);
                            $cronObject->setData('status', Status::PENDING);
                            $cronObject->setData('chunk_ids', json_encode($product_ids));
                            $cronObject->save();
                        }
                    }
                }
        } catch(\Exception $e) {
            $this->logger->addError('Error Occur Inside Inventory Cron '.$e->getMessage(), ['path' => __METHOD__]);
        }
    }
}
