<?php
namespace Ced\GoodMarket\Controller\Adminhtml\Scheduler;

use Magento\Framework\View\Result\PageFactory;

/**
 * Sync Scheduler
 */
class Sync extends \Magento\Backend\App\Action
{
    protected $directoryList;

    /**
     * Sync Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ced\GoodMarket\Helper\Data $data
     * @param PageFactory $resultPageFactory
     * @param \Ced\GoodMarket\Model\SchedulerFactory $schedulerFactory
     * @param \Ced\GoodMarket\Model\ResourceModel\Scheduler\CollectionFactory $scheduleCollectionFactory
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ced\GoodMarket\Helper\Data $data,
        PageFactory $resultPageFactory,
        \Ced\GoodMarket\Model\SchedulerFactory $schedulerFactory,
        \Ced\GoodMarket\Model\ResourceModel\Scheduler\CollectionFactory $scheduleCollectionFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Catalog\Model\ProductFactory $_productloader
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->data=$data;
        $this->schedulerFactory = $schedulerFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_productloader = $_productloader;
        parent::__construct($context);
    }

    /**
     * Sync Execute Method
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $id=$this->getRequest()->getParams();
        $result = $this
            ->resultRedirectFactory
            ->create();
        $cronModel = $this
            ->scheduleCollectionFactory
            ->create()
            ->addFieldToFilter('id', $id)
            ->addFieldToFilter('scheduler_product_sync', 'pending')
            ->addFieldToFilter('scheduler_response', ['null'=>true])
            ->getFirstItem();

        if ($cronModel && $cronModel->getId()) {
            $chunkIds = $cronModel->getSchedulerId();
            if (!empty($chunkIds)) {
                $response = $this->data->bulkProductResponse($chunkIds);
//                echo "<pre>";
//                print_r($response);
//                die(__FILE__);
                if (isset($response['data']['pendingBulkresponse']) && !empty(isset($response['data']['pendingBulkresponse']))) {
                    $currentScheduler=$this->schedulerFactory->create()->load($cronModel->getId());
                    $currentScheduler->setData('scheduler_response', json_encode($response));
                    $currentScheduler->setData('scheduler_status', $response['data']['pendingBulkresponse']['job_status']);
                    $currentScheduler->setData('scheduler_product_sync', 'Processing');
                    $currentScheduler->save();
                    $this->messageManager->addSuccessMessage(__('Selected Feed Got Synced Successfully!!'));
                }
            }
        } else {
            $this->messageManager->addErrorMessage(__('Feed Already Been Synced!!'));
        }
        return $this->_redirect('goodmarket/scheduler/index');
    }
}
