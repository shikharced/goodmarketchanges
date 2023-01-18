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
 * @package   Ced_GoodMarket
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Cron;

use Ced\GoodMarket\Model\Source\Cron\Status;
use Ced\GoodMarket\Model\Source\Cron\Type;

/**
 * class SchedulerResponse Cron
 */
class SchedulerResponse
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
     */
    public function __construct(
        \Ced\GoodMarket\Helper\Logger $logger,
        \Ced\GoodMarket\Helper\Config $config,
        \Ced\GoodMarket\Helper\Data $data,
        \Ced\GoodMarket\Model\SchedulerFactory $schedulerFactory,
        \Ced\GoodMarket\Model\ResourceModel\Scheduler\CollectionFactory $scheduleCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->helper = $data;
        $this->schedulerFactory = $schedulerFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->productCollection = $productCollectionFactory;
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
                ->addFieldToFilter('scheduler_product_sync', 'pending')
                ->addFieldToFilter('scheduler_response', ['null'=>true])
                ->getFirstItem();

            if ($cronModel && $cronModel->getId()) {
                $chunkIds = $cronModel->getSchedulerId();
                if (!empty($chunkIds)) {
                    $response = $this->helper->bulkProductResponse($chunkIds);
                    if (isset($response['data']['pendingBulkresponse']) && !empty(isset($response['data']['pendingBulkresponse']))) {
                        $currentScheduler = $this->schedulerFactory->create()->load($cronModel->getId());
                        $currentScheduler->setData('scheduler_response', json_encode($response));
                        $currentScheduler->setData('scheduler_status', $response['data']['pendingBulkresponse']['job_status']);
                        $currentScheduler->setData('scheduler_product_sync', 'Processing');
                        $currentScheduler->save();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->addError('Error Occur Inside Inventory Cron '.$e->getMessage(), ['path' => __METHOD__]);
        }
    }
}
