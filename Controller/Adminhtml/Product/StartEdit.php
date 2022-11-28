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
 * @package   Ced_\Ced\GoodMarket\Model\Carrier\GoodMarket
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Controller\Adminhtml\Product;

use Ced\GoodMarket\Model\Carrier\GoodMarket;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Startrevise
 * @package Ced\GoodMarket\Controller\Adminhtml\Product
 */
class StartEdit  extends Action
{
    const FLAG_CODE = 'CED_JSON_FIL';
    /**
     * @var PageFactory
     */
    public $resultPageFactory;
    /**
     * @var JsonFactory
     */
    public $resultJsonFactory;
    /**
     * @var GoodMarket
     */
    public $bolHelper;
    /**
     * @var Data
     */
    public $dataHelper;
    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var \Ced\GoodMarket\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Startrevise constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param GoodMarket $bolHelper
     * @param Data $dataHelper
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Ced\GoodMarket\Helper\Product $product,
        \Ced\GoodMarket\Helper\Config                $config,
        \Ced\GoodMarket\Helper\Logger $logger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->product=$product;
        $this->config = $config;
        $this->logger=$logger;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $message = $error = $success =[];
        $message['error'] = "";
        $message['success'] = "";

        $key = $this->getRequest()->getParam('index');
        $totalChunk = $this->_session->getProductEditChunks();
        $index = $key + 1;
        if (count($totalChunk) <= $index) {
            $this->_session->unsProductEditChunks();
        }
        try {
            $uploadMessage=[];
            $errorMessage=[];
            if (isset($totalChunk[$key])) {
                $ids = $totalChunk[$key];
                $productData=$this->product->prepareData($ids,'EditProduct');
//                        echo "<pre>";
//                        print_r($productData);
//                        die(__FILE__);
                if(isset($productData['data']['errorMessage']))
                {
                    $errorMessage[] = implode(',',$productData['data']['errorMessage']);
                }
//                        $productId = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($prodIds);
                if(isset($productData['data']['saveBulkProduct'])) {
                    if ($productData['data']['saveBulkProduct']['success'] == '1') {
                        $uploadMessage[] = "Product Update Request Has Been Send Successfully!!,Check Feed Section and Bulk Scheduler Section.";
                    } else {
                        $errorMessage[] = $productData['data']['saveBulkProduct']['message'];
                    }
                }else{
                    $errorMessage[] = "Something Went Wrong,Please Try After Sometime.";
                }

                $message['success'] = implode(',',$uploadMessage);
                $message['error'] = implode(',',$errorMessage);
            }else {
                $message['error'] = "Batch ".$index.":included Product(s) data not found.";
            }
        } catch (\Exception $e) {
            $message['error'] = $e->getMessage();
        }
        return $resultJson->setData($message);
    }
}