<?php
namespace Ced\GoodMarket\Controller\Adminhtml\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Result\PageFactory;


class Massupload extends \Magento\Backend\App\Action
{
    protected $directoryList;

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
        $result = $this
            ->resultRedirectFactory
            ->create();
        $credentials=$this->data->checkAccountSetup();
        if($credentials!=1)
        {
            $this
                ->messageManager
                ->addNoticeMessage($credentials);
            $result->setPath('goodmarket/product/index');
            return $result;
        }
        $collection = $this->filter->getCollection($this->prodCollFactory->create());
        $collectionId=$collection->getAllIds();
//        foreach ($collectionId as $id)
//        {
//
//            $product=$this->_objectManager->create(\Magento\Catalog\Model\Product::class)->load($id);
//            $product->setgoodmarketProductId('');
//            $product->setgoodmarketProductStatus('');
//            $product->save();
//        }
//        die(__FILE__);
        $ids = array_chunk($collectionId, 100);
            if (!empty($ids)) {
                $this->_session->setProductChunks($ids);
                $resultPage = $this->resultPageFactory->create();
                $resultPage->setActiveMenu('Ced_GoodMarket::product');
                $resultPage->getConfig()->getTitle()->prepend(__('Upload Product(s) On GoodMarket'));
                return $resultPage;
            } else {
                $this->messageManager->addErrorMessage(__('No product available for upload.'));
                return $this->_redirect('goodmarket/product/index');
            }

        return  $this->_redirect('*/*/index');
    }

}
