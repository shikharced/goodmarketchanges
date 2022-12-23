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

/**
 * SchedulerSync cron
 */
class SchedulerSync
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
     *
     * @param \Ced\GoodMarket\Helper\Logger $logger
     * @param \Ced\GoodMarket\Helper\Config $config
     * @param \Ced\GoodMarket\Helper\Data $data
     * @param \Ced\GoodMarket\Model\SchedulerFactory $schedulerFactory
     * @param \Ced\GoodMarket\Model\ResourceModel\Scheduler\CollectionFactory $scheduleCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     */
    public function __construct(
        \Ced\GoodMarket\Helper\Logger $logger,
        \Ced\GoodMarket\Helper\Config $config,
        \Ced\GoodMarket\Helper\Data $data,
        \Ced\GoodMarket\Model\SchedulerFactory $schedulerFactory,
        \Ced\GoodMarket\Model\ResourceModel\Scheduler\CollectionFactory $scheduleCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $_productloader)
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->helper = $data;
        $this->schedulerFactory = $schedulerFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->productCollection = $productCollectionFactory;
        $this->_productloader = $_productloader;
    }

    /**
     * Fetch Order From GoodMarket
     */
    public function execute()
    {
        try {
            $cronModel = $this
                ->scheduleCollectionFactory
                ->create()
                ->addFieldToFilter('scheduler_product_sync', 'Processing')
                ->getFirstItem();

            if ($cronModel && $cronModel->getId()) {
                $chunkIds = $cronModel->getSchedulerId();
                if (!empty($chunkIds)) {
                    $status=false;
                    $response = json_decode($cronModel->getSchedulerResponse(), true);
                    if (isset($response['data']['pendingBulkresponse']) && !empty(isset($response['data']['pendingBulkresponse']))) {
                        if (isset($response['data']['pendingBulkresponse']['error_result']) && !empty($response['data']['pendingBulkresponse']['error_result'])) {
                            $status=true;
                            $error_result = $response['data']['pendingBulkresponse']['error_result'];
//                            echo "<pre>";
//                            print_r($error_result);
//                            die(__FILE__);
                            foreach ($error_result as $errors) {
                                $productLoad = $this->_productloader->create()->loadByAttribute('sku', $errors['product_sku']);
                                $productLoad->setData('goodmarket_product_error', $errors['message']);
                                $productLoad->save();
                            }
                        }
                        if (isset($response['data']['pendingBulkresponse']['product_ids']) && !empty($response['data']['pendingBulkresponse']['product_ids'])) {
                            $status=true;
                            $product_ids = $response['data']['pendingBulkresponse']['product_ids'];
                            foreach ($product_ids as $prod) {
                                $productLoad = $this->_productloader->create()
                                    ->loadByAttribute('sku', $prod['product_sku']);
                                $productLoad->setData('goodmarket_product_status', 'Uploaded');
                                $productLoad->setData('goodmarket_product_id', $prod['product_id']);
                                $productLoad->setData('goodmarket_product_error', '["valid"]');
                                $productLoad->save();
                            }
                        }
                        if ($status == true) {
                            $currentScheduler=$this->schedulerFactory->create()->load($cronModel->getId());
                            $currentScheduler->setData('scheduler_product_sync', 'Finished');
                            $currentScheduler->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            /*echo "<pre>";
            print_r($e->getMessage());
            die(__FILE__);*/
            $this->logger->addError('Error Occur Inside Inventory Cron '.$e->getMessage(), ['path' => __METHOD__]);
        }
    }
}
