<?php
///**
// * CedCommerce
// *
// * NOTICE OF LICENSE
// *
// * This source file is subject to the End User License Agreement(EULA)
// * that is bundled with this package in the file LICENSE.txt.
// * It is also available through the world-wide-web at this URL:
// * http://cedcommerce.com/license-agreement.txt
// *
// * @category  Ced
// * @package   Ced_GoodMarket
// * @author    CedCommerce Core Team <connect@cedcommerce.com>
// * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
// * @license   http://cedcommerce.com/license-agreement.txt
// */
//
//namespace Ced\GoodMarket\Controller\Adminhtml\Request;
//
//use Magento\Backend\App\Action\Context;
//use Magento\Framework\Exception\NotFoundException;
//use Magento\Framework\View\Result\PageFactory;
//
///**
// * Class Help
// * @package Ced\GoodMarket\Controller\Adminhtml\Request
// */
//class Help extends \Magento\Backend\App\Action
//{
//    /**
//     * @var PageFactory
//     */
//    public $resultPageFactory;
//
//    /**
//     * Help constructor.
//     * @param Context $context
//     * @param PageFactory $resultPageFactory
//     */
//    public function __construct(
//        Context $context,
//        PageFactory $resultPageFactory
//
//    )
//    {
//        parent::__construct($context);
//        $this->resultPageFactory = $resultPageFactory;
//    }
//
//    /**
//     * @return \Magento\Backend\Model\View\Result\Page
//     */
//    public function execute()
//    {
//        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
//        $resultPage = $this->resultPageFactory->create();
//        $resultPage->setActiveMenu('Ced_GoodMarket::GoodMarket_knowledge_base');
//        $resultPage->getConfig()->getTitle()->prepend(__('GoodMarket Knowledge Base'));
//
//        return $resultPage;
//    }
//
//    /**
//     * @return bool
//     */
//    public function _isAllowed()
//    {
//        return $this->_authorization->isAllowed('Ced_GoodMarket::GoodMarket');
//    }
//}

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

namespace Ced\GoodMarket\Controller\Adminhtml\Request;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Class FetchCategory
 * @package Ced\GoodMarket\Controller\Adminhtml\Config
 */
class Help extends Action
{
    /**
     * @var JsonFactory
     */
    public $resultJsonFactory;
    /**
     * @var ObjectManagerInterface
     */
    public $objectManager;
    /**
     * @var Filesystem
     */
    public $filesystem;
    /**
     * @var Data
     */
    public $helper;
    /**
     * @var Filesystem\Io\File
     */
    public $file;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory                         $resultJsonFactory,
        Filesystem                          $filesystem,
        Filesystem\Io\File                  $file
    )
    {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /*
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $order = $objectManager->create('Ced\GoodMarket\Cron\SchedulerResponse')->execute();
//        $order = $objectManager->create('Ced\GoodMarket\Helper\Data')->bulkProductResponse('1234567');

        $json="{\"id\":\"1735\",\"product\":\"{\\\"product_has_weight\\\":1,\\\"price\\\":\\\"300\\\",\\\"sku\\\":\\\"arj234\\\",\\\"category_ids\\\":[\\\"75\\\"],\\\"sources\\\":\\\"[{\\\\\\\"source_code\\\\\\\":\\\\\\\"Levana\\\\\\\",\\\\\\\"name\\\\\\\":\\\\\\\"Levana\\\\\\\",\\\\\\\"quantity\\\\\\\":150,\\\\\\\"source_status\\\\\\\":1,\\\\\\\"status\\\\\\\":1}]\\\"}\"}";
        $json1=json_decode($json,true);
        echo "<pre>";
        print_r($json1);
        echo "<pre>";
        print_r(json_decode($json1['product'],true));
        die(__FILE__);
//        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
//        $resultPage = $this->resultPageFactory->create();
//        $resultPage->setActiveMenu('Ced_GoodMarket::GoodMarket_knowledge_base');
//        $resultPage->getConfig()->getTitle()->prepend(__('GoodMarket Knowledge Base'));
//
//        return $resultPage;
    }
}
