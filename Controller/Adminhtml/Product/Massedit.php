<?php
namespace Ced\GoodMarket\Controller\Adminhtml\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * MassEdir Product
 */
class Massedit extends \Magento\Backend\App\Action
{
    protected $directoryList;

    /**
     * MassEdit Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ced\GoodMarket\Helper\Product $product
     * @param \Ced\GoodMarket\Helper\Data $data
     * @param CollectionFactory $prodCollFactory
     * @param PageFactory $resultPageFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Ced\GoodMarket\Helper\Config $config
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ced\GoodMarket\Helper\Product $product,
        \Ced\GoodMarket\Helper\Data $data,
        CollectionFactory $prodCollFactory,
        PageFactory $resultPageFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Ced\GoodMarket\Helper\Config                $config,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
    ) {
        $this->product=$product;
        $this->prodCollFactory = $prodCollFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->data=$data;
        $this->config = $config;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultRedirectFactory->create();
        $credentials=$this->data->checkAccountSetup();
        if ($credentials!=1) {
            $this
                ->messageManager
                ->addNoticeMessage($credentials);
            $result->setPath('goodmarket/product/index');
            return $result;
        }
        $collection = $this->filter->getCollection($this->prodCollFactory->create());
        $collectionId=$collection->getAllIds();
        $ids = array_chunk($collectionId, 100);
        if (!empty($ids)) {
            $this->_session->setProductEditChunks($ids);
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Ced_GoodMarket::product');
            $resultPage->getConfig()->getTitle()->prepend(__('Update Product(s) On GoodMarket'));
            return $resultPage;
        } else {
            $this->messageManager->addErrorMessage(__('No product available for upload.'));
            return $this->_redirect('goodmarket/product/index');
        }
        return  $this->_redirect('*/*/index');
    }
}
