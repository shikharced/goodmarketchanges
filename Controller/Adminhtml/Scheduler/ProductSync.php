<?php
namespace Ced\GoodMarket\Controller\Adminhtml\Scheduler;

use Magento\Framework\View\Result\PageFactory;

/**
 * ProductSync for scheduler
 */
class ProductSync extends \Magento\Backend\App\Action
{
    protected $directoryList;

    /**
     * ProductSync Constructor.
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
     * ProductSync Execute
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
            ->addFieldToFilter('scheduler_product_sync', 'Processing')
            ->getFirstItem();

        if ($cronModel && $cronModel->getId()) {
            $chunkIds = $cronModel->getSchedulerId();
            if (!empty($chunkIds)) {
                $status=false;
                $response = json_decode($cronModel->getSchedulerResponse(), true);
                if (isset($response['data']['pendingBulkresponse']) && !empty(isset($response['data']['pendingBulkresponse']))) {
                    if (isset($response['data']['pendingBulkresponse']['error_result']) &&
                        !empty($response['data']['pendingBulkresponse']['error_result'])) {
                        $status=true;
                        $error_result = $response['data']['pendingBulkresponse']['error_result'];
                        foreach ($error_result as $errors) {
                            $productLoad = $this->_productloader->create()
                                ->loadByAttribute('sku', $errors['product_sku']);
                            $productLoad->setData('goodmarket_product_error', $errors['message']);
                            $productLoad->save();
                        }
                    }
                    if (isset($response['data']['pendingBulkresponse']['product_ids']) &&
                        !empty($response['data']['pendingBulkresponse']['product_ids'])) {
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
                    if ($status==true) {
                        $currentScheduler=$this->schedulerFactory->create()->load($cronModel->getId());
                        $currentScheduler->setData('scheduler_product_sync', 'Finished');
                        $currentScheduler->save();
                        $this
                            ->messageManager
                            ->addSuccessMessage("Products Got Synced Successfully!!");
                    }
                }
            }
        } else {
            $this->messageManager->addErrorMessage(__('Products Are Already Been Synced!!!'));
        }
        return $this->_redirect('goodmarket/scheduler/index');
    }
}
