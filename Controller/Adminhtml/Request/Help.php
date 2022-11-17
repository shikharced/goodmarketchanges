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
        $optionText="{\"type\":\"configurable\",\"set\":\"4\",\"image_role\":\"{\\\"image\\\":\\\"image1\\\",\\\"small_image\\\":\\\"image1\\\",\\\"thumbnail\\\":\\\"image1\\\",\\\"swatch_image\\\":\\\"\\\"}\",\"id\":\"344\",\"product\":\"{\\\"name\\\":\\\"Meat Cooler\\\",\\\"price\\\":0.13,\\\"sku\\\":\\\"914199\\\",\\\"status\\\":1,\\\"visibility\\\":4,\\\"weight\\\":0,\\\"category_ids\\\":[\\\"13\\\"],\\\"description\\\":\\\"<p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam.<\\\\\\\/p><p>Itaque earum rerum hic tenetur a sapiente delectus.<\\\\\\\/p><p>In common language usage, \\\\\\\"fruit\\\\\\\" normally means the fleshy seed-associated structures of a plant that are sweet or sour and edible in the raw state, such as apples, oranges, grapes, strawberries, bananas, and lemons.<\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p><p>I'm surprised you had the courage to take the responsibility yourself.<\\\\\\\/p><p>It's more like ... suicide.<\\\\\\\/p><p>It is also the high-end model of the MacBook family and is currently produced with 13- and 15-inch screens, although a 17-inch version has been offered previously.<\\\\\\\/p><p>Don't act so surprised, Your Highness.<\\\\\\\/p><p>No!<\\\\\\\/p>\\\",\\\"short_description\\\":\\\"Size guide: CHOOSING A BRACELET SIZE BY MEASURING YOUR WRIST\\\\r\\\\nUse a tape measure. Wrap the tape measure around the wrist on Make a note of the number at the point where the tape meets the 0. This bracelet is intended to be worn loose so we have designed it with plenty of extra space to make sure you\\\\u2019re always comfortable and don\\\\u2019t have the need to remove it when doing daily activities.\\\",\\\"meta_title\\\":\\\"Meat Cooler\\\",\\\"meta_keyword\\\":\\\"Meat Cooler\\\",\\\"meta_description\\\":\\\"Meat Cooler\\\",\\\"url_key\\\":\\\"914199\\\",\\\"product_has_weight\\\":\\\"1\\\",\\\"quantity_and_stock_status\\\":null,\\\"sources\\\":\\\"[{\\\\\\\"source_code\\\\\\\":\\\\\\\"1234\\\\\\\",\\\\\\\"name\\\\\\\":\\\\\\\"colombo\\\\\\\",\\\\\\\"quantity\\\\\\\":2771,\\\\\\\"source_status\\\\\\\":1,\\\\\\\"status\\\\\\\":1}]\\\"}\",\"category_id\":\"13\",\"configurable_matrix\":[{\"configurable_attribute\":{\"size\":\"xxs\",\"color\":\"black\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_FB161MBRGL1\",\"sku\":\"FB161MBRGL1\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":1394,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":3,\"image\":\"\"},{\"configurable_attribute\":{\"size\":\"xxl\",\"color\":\"pink\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_MECO-1\",\"sku\":\"MECO-1\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":20,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.13,\"image\":\"\"},{\"configurable_attribute\":{\"size\":\"xxs\",\"color\":\"pink\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_MECO-2\",\"sku\":\"MECO-2\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":321,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.16,\"image\":\"\"},{\"configurable_attribute\":{\"size\":\"xxs\",\"color\":\"orange\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_MECO-3\",\"sku\":\"MECO-3\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":965,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.16,\"image\":\"\"},{\"configurable_attribute\":{\"size\":\"xs\",\"color\":\"orange\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_MECO-4\",\"sku\":\"MECO-4\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":71,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.14999999999999999,\"image\":\"\"}],\"config_attributes\":[\"size\",\"color\",\"size\",\"color\",\"size\",\"color\",\"size\",\"color\",\"size\",\"color\"]}";
        echo "<pre>";
       // print_r($optionText);
        print_r(json_decode($optionText,true));
        die(__FILE__);
//        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
//        $resultPage = $this->resultPageFactory->create();
//        $resultPage->setActiveMenu('Ced_GoodMarket::GoodMarket_knowledge_base');
//        $resultPage->getConfig()->getTitle()->prepend(__('GoodMarket Knowledge Base'));
//
//        return $resultPage;
    }
}
