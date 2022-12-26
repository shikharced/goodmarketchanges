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
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Help Controller Action
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
    ) {
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
//        $optionText= Array
//        (
//            ['query'] => 'mutation saveBulkProduct($vendor_id: Int!,$product_data: String!, $hash_token: String) {
//        saveBulkProduct(vendor_id: $vendor_id, product_data:$product_data , hash_token:$hash_token){
//        success
        //						message
        //						job_id
        //					}
        //				}',
//    ['variables'] => Array
//    (
//        ['product_data'] => ["{\"type\":\"configurable\",\"set\":\"4\",\"image_role\":\"{\\\"image\\\":\\\"image1\\\",\\\"small_image\\\":\\\"image1\\\",\\\"thumbnail\\\":\\\"image1\\\",\\\"swatch_image\\\":\\\"\\\"}\",\"product\":\"{\\\"name\\\":\\\"Meat Cooler\\\",\\\"price\\\":0.13,\\\"sku\\\":\\\"914199\\\",\\\"color\\\":\\\"black, orange, pink\\\",\\\"size\\\":\\\"M, XS, XXL, XXS\\\",\\\"weight\\\":0,\\\"category_ids\\\":[\\\"60\\\"],\\\"sources\\\":\\\"[{\\\\\\\"source_code\\\\\\\":\\\\\\\"1234\\\\\\\",\\\\\\\"name\\\\\\\":\\\\\\\"colombo\\\\\\\",\\\\\\\"quantity\\\\\\\":300,\\\\\\\"source_status\\\\\\\":1,\\\\\\\"status\\\\\\\":1}]\\\",\\\"description\\\":\\\"<p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam.<\\\\\\\/p><p>Itaque earum rerum hic tenetur a sapiente delectus.<\\\\\\\/p><p>In common language usage, \\\\\\\"fruit\\\\\\\" normally means the fleshy seed-associated structures of a plant that are sweet or sour and edible in the raw state, such as apples, oranges, grapes, strawberries, bananas, and lemons.<\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p><p>I'm surprised you had the courage to take the responsibility yourself.<\\\\\\\/p><p>It's more like ... suicide.<\\\\\\\/p><p>It is also the high-end model of the MacBook family and is currently produced with 13- and 15-inch screens, although a 17-inch version has been offered previously.<\\\\\\\/p><p>Don't act so surprised, Your Highness.<\\\\\\\/p><p>No!<\\\\\\\/p>\\\",\\\"short_description\\\":\\\"Size guide: CHOOSING A BRACELET SIZE BY MEASURING YOUR WRIST\\\\r\\\\nUse a tape measure. Wrap the tape measure around the wrist on Make a note of the number at the point where the tape meets the 0. This bracelet is intended to be worn loose so we have designed it with plenty of extra space to make sure you\\\\u2019re always comfortable and don\\\\u2019t have the need to remove it when doing daily activities.\\\",\\\"media_gallery\\\":\\\"{\\\\\\\"images\\\\\\\":\\\\\\\"{\\\\\\\\\\\\\\\"image1\\\\\\\\\\\\\\\":{\\\\\\\\\\\\\\\"file\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\"{\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"file\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"http:\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/localhost:10036\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/wp-content\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/uploads\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/2022\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/09\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/product-1664432475-827701908.png\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\",\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"file_name\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"product-1664432475-827701908.png\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"}\\\\\\\\\\\\\\\"}}\\\\\\\"}\\\",\\\"meta_title\\\":\\\"Meat Cooler\\\",\\\"meta_keyword\\\":\\\"Meat Cooler\\\",\\\"meta_description\\\":\\\"Meat Cooler\\\",\\\"url_key\\\":\\\"914199\\\",\\\"product_has_weight\\\":\\\"1\\\"}\",\"category_id\":\"60\",\"configurable_matrix\":[{\"configurable_attribute\":{\"color\":\"pink\",\"size\":\"XXL\"},\"config_attributes\":[\"color\",\"size\"],\"name\":\"Meat Cooler_MECO-1\",\"sku\":\"MECO-1\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":20,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.13,\"image\":\"\"},{\"configurable_attribute\":{\"color\":\"pink\",\"size\":\"XXS\"},\"config_attributes\":[\"color\",\"size\"],\"name\":\"Meat Cooler_MECO-2\",\"sku\":\"MECO-2\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":321,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.16,\"image\":\"\"},{\"configurable_attribute\":{\"color\":\"orange\",\"size\":\"XXS\"},\"config_attributes\":[\"color\",\"size\"],\"name\":\"Meat Cooler_MECO-3\",\"sku\":\"MECO-3\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":965,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.16,\"image\":\"\"},{\"configurable_attribute\":{\"color\":\"orange\",\"size\":\"XS\"},\"config_attributes\":[\"color\",\"size\"],\"name\":\"Meat Cooler_MECO-4\",\"sku\":\"MECO-4\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":71,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.14999999999999999,\"image\":\"\"},{\"configurable_attribute\":{\"color\":\"black\",\"size\":\"XXS\"},\"config_attributes\":[\"color\",\"size\"],\"name\":\"Meat Cooler_MECO-5\",\"sku\":\"MECO-5\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":1410,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.17000000000000001,\"image\":\"\"}],\"config_attributes\":[\"color\",\"size\",\"color\",\"size\",\"color\",\"size\",\"color\",\"size\",\"color\",\"size\"]}"]
//        ,['vendor_id'] => '5869'
//            ,['hash_token'] => 'GWaQNoYWmgaxlEmXgSKAR949FL5xsy0a'
//        )
//
        //);
        $optionText['variables'] = [

        'product_data' => ["{\"type\":\"configurable\",\"set\":\"4\",\"image_role\":\"{\\\"image\\\":\\\"image1\\\",\\\"small_image\\\":\\\"image1\\\",\\\"thumbnail\\\":\\\"image1\\\",\\\"swatch_image\\\":\\\"\\\"}\",\"product\":\"{\\\"name\\\":\\\"Toy CD\\\",\\\"price\\\":0.89000000000000001,\\\"sku\\\":\\\"625772\\\",\\\"status\\\":1,\\\"visibility\\\":4,\\\"weight\\\":0,\\\"category_ids\\\":[\\\"41\\\"],\\\"description\\\":\\\"<p>It's just like the story of the grasshopper and the octopus.<\\\\\\\/p><p>On the other hand, the botanical sense of \\\\\\\"fruit\\\\\\\" includes many structures that are not commonly called \\\\\\\"fruits\\\\\\\", such as bean pods, corn kernels, wheat grains, and tomatoes.<\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p><p>Nisi ut aliquid ex ea commodi consequatur?<\\\\\\\/p><p><\\\\\\\/p><p>Some CDMA devices also have a similar card called a R-UIM.<\\\\\\\/p>\\\",\\\"short_description\\\":\\\"<p>On the other hand, the botanical sense of \\\\\\\"fruit\\\\\\\" includes many structures that are not commonly called \\\\\\\"fruits\\\\\\\", such as bean pods, corn kernels, wheat grains, and tomatoes.<\\\\\\\/p>\\\",\\\"media_gallery\\\":\\\"{\\\\\\\"images\\\\\\\":\\\\\\\"{\\\\\\\\\\\\\\\"image1\\\\\\\\\\\\\\\":{\\\\\\\\\\\\\\\"file\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\"{\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"file\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"http:\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/localhost:10036\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/wp-content\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/uploads\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/2022\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/09\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/product-1664432188-1395819063.png\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\",\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"file_name\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"product-1664432188-1395819063.png\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"}\\\\\\\\\\\\\\\"}}\\\\\\\"}\\\",\\\"meta_title\\\":\\\"Toy CD\\\",\\\"meta_keyword\\\":\\\"Toy CD\\\",\\\"meta_description\\\":\\\"Toy CD\\\",\\\"url_key\\\":\\\"625772\\\",\\\"product_has_weight\\\":\\\"1\\\",\\\"quantity_and_stock_status\\\":null,\\\"sources\\\":\\\"[{\\\\\\\"source_code\\\\\\\":\\\\\\\"1234\\\\\\\",\\\\\\\"name\\\\\\\":\\\\\\\"colombo\\\\\\\",\\\\\\\"quantity\\\\\\\":16,\\\\\\\"source_status\\\\\\\":1,\\\\\\\"status\\\\\\\":1}]\\\"}\",\"category_id\":\"41\",\"configurable_matrix\":[{\"configurable_attribute\":{\"color\":\"mint\"},\"config_attributes\":[\"color\"],\"name\":\"Toy CD_TOCD-1\",\"sku\":\"TOCD-1\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":16,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.89000000000000001,\"image\":\"\"}],\"config_attributes\":[\"color\"]}","{\"type\":\"configurable\",\"set\":\"4\",\"image_role\":\"{\\\"image\\\":\\\"image1\\\",\\\"small_image\\\":\\\"image1\\\",\\\"thumbnail\\\":\\\"image1\\\",\\\"swatch_image\\\":\\\"\\\"}\",\"product\":\"{\\\"name\\\":\\\"Gel Juice Oven\\\",\\\"price\\\":0.20999999999999999,\\\"sku\\\":\\\"238232\\\",\\\"status\\\":1,\\\"visibility\\\":4,\\\"weight\\\":0,\\\"category_ids\\\":[\\\"94\\\"],\\\"description\\\":\\\"<p>No!<\\\\\\\/p><p>Besides, these are adult stemcells, harvested from perfectly healthy adults whom I killed for their stemcells.<\\\\\\\/p><p>In your time, yes, but nowadays shut up!<\\\\\\\/p><p>All GSM phones use a SIM card to allow an account to be swapped among devices.<\\\\\\\/p><p>I'm surprised you had the courage to take the responsibility yourself.<\\\\\\\/p><p>Let's blow this thing and go home!<\\\\\\\/p><p>All year long, the grasshopper kept burying acorns for winter, while the octopus mooched off his girlfriend and watched TV.<\\\\\\\/p><p>No!<\\\\\\\/p><p>I'm getting too old for this sort of thing.<\\\\\\\/p>\\\",\\\"short_description\\\":\\\"<p>No!<\\\\\\\/p>\\\",\\\"media_gallery\\\":\\\"{\\\\\\\"images\\\\\\\":\\\\\\\"{\\\\\\\\\\\\\\\"image1\\\\\\\\\\\\\\\":{\\\\\\\\\\\\\\\"file\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\"{\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"file\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"http:\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/localhost:10036\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/wp-content\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/uploads\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/2022\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/09\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/product-1664432143-929083410.png\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\",\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"file_name\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"product-1664432143-929083410.png\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"}\\\\\\\\\\\\\\\"}}\\\\\\\"}\\\",\\\"meta_title\\\":\\\"Gel Juice Oven\\\",\\\"meta_keyword\\\":\\\"Gel Juice Oven\\\",\\\"meta_description\\\":\\\"Gel Juice Oven\\\",\\\"url_key\\\":\\\"238232\\\",\\\"product_has_weight\\\":\\\"1\\\",\\\"quantity_and_stock_status\\\":null,\\\"sources\\\":\\\"[{\\\\\\\"source_code\\\\\\\":\\\\\\\"1234\\\\\\\",\\\\\\\"name\\\\\\\":\\\\\\\"colombo\\\\\\\",\\\\\\\"quantity\\\\\\\":794,\\\\\\\"source_status\\\\\\\":1,\\\\\\\"status\\\\\\\":1}]\\\"}\",\"category_id\":\"94\",\"configurable_matrix\":[{\"configurable_attribute\":{\"size\":\"xl\"},\"config_attributes\":[\"size\"],\"name\":\"Gel Juice Oven_GEJUOV-1\",\"sku\":\"GEJUOV-1\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":794,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.20999999999999999,\"image\":\"\"}],\"config_attributes\":[\"size\"]}","{\"type\":\"configurable\",\"set\":\"4\",\"image_role\":\"{\\\"image\\\":\\\"image1\\\",\\\"small_image\\\":\\\"image1\\\",\\\"thumbnail\\\":\\\"image1\\\",\\\"swatch_image\\\":\\\"\\\"}\",\"product\":\"{\\\"name\\\":\\\"Potato Seat Cold\\\",\\\"price\\\":0.78000000000000003,\\\"sku\\\":\\\"135162\\\",\\\"status\\\":1,\\\"visibility\\\":4,\\\"weight\\\":0,\\\"category_ids\\\":[\\\"94\\\"],\\\"description\\\":\\\"<p>An input mechanism to allow the user to interact with the phone.<\\\\\\\/p><p>A person who refrains from dairy and meat products, and eats only plants (including vegetables) is known as a vegan.<\\\\\\\/p><p><\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p><p>As you wish.<\\\\\\\/p><p>Individual GSM, WCDMA, iDEN and some satellite phone devices are uniquely identified by an International Mobile Equipment Identity (IMEI) number.<\\\\\\\/p><p><\\\\\\\/p>\\\",\\\"short_description\\\":\\\"<p>A person who refrains from dairy and meat products, and eats only plants (including vegetables) is known as a vegan.<\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p>\\\",\\\"media_gallery\\\":\\\"{\\\\\\\"images\\\\\\\":\\\\\\\"{\\\\\\\\\\\\\\\"image1\\\\\\\\\\\\\\\":{\\\\\\\\\\\\\\\"file\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\"{\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"file\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"http:\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/localhost:10036\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/wp-content\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/uploads\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/2022\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/09\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\/product-1664432022-1484355871.png\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\",\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"file_name\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"product-1664432022-1484355871.png\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"}\\\\\\\\\\\\\\\"}}\\\\\\\"}\\\",\\\"meta_title\\\":\\\"Potato Seat Cold\\\",\\\"meta_keyword\\\":\\\"Potato Seat Cold\\\",\\\"meta_description\\\":\\\"Potato Seat Cold\\\",\\\"url_key\\\":\\\"135162\\\",\\\"product_has_weight\\\":\\\"1\\\",\\\"quantity_and_stock_status\\\":null,\\\"sources\\\":\\\"[{\\\\\\\"source_code\\\\\\\":\\\\\\\"1234\\\\\\\",\\\\\\\"name\\\\\\\":\\\\\\\"colombo\\\\\\\",\\\\\\\"quantity\\\\\\\":216,\\\\\\\"source_status\\\\\\\":1,\\\\\\\"status\\\\\\\":1}]\\\"}\",\"category_id\":\"94\",\"configurable_matrix\":[{\"configurable_attribute\":{\"color\":\"green\"},\"config_attributes\":[\"color\"],\"name\":\"Potato Seat Cold_POSECO-1\",\"sku\":\"POSECO-1\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":151,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":1.1100000000000001,\"image\":\"\"},{\"configurable_attribute\":{\"color\":\"mint\"},\"config_attributes\":[\"color\"],\"name\":\"Potato Seat Cold_POSECO-2\",\"sku\":\"POSECO-2\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":65,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.78000000000000003,\"image\":\"\"},{\"configurable_attribute\":{\"color\":\"blue\"},\"config_attributes\":[\"color\"],\"name\":\"Potato Seat Cold_POSECO-3\",\"sku\":\"POSECO-3\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":0,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.95999999999999996,\"image\":\"\"}],\"config_attributes\":[\"color\",\"color\",\"color\"]}"]
        ,'vendor_id' => '5869'
            ,'hash_token' => 'GWaQNoYWmgaxlEmXgSKAR949FL5xsy0a'
        ];
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $order = $objectManager->create('Ced\GoodMarket\Cron\SchedulerResponse')->execute();
//        $order = $objectManager->create('Ced\GoodMarket\Helper\Data')->bulkProductResponse('1234567');
        //$optionText="{\"type\":\"configurable\",\"set\":\"4\",\"image_role\":\"{\\\"image\\\":\\\"image1\\\",\\\"small_image\\\":\\\"image1\\\",\\\"thumbnail\\\":\\\"image1\\\",\\\"swatch_image\\\":\\\"\\\"}\",\"id\":\"344\",\"product\":\"{\\\"name\\\":\\\"Meat Cooler\\\",\\\"price\\\":0.13,\\\"sku\\\":\\\"914199\\\",\\\"status\\\":1,\\\"visibility\\\":4,\\\"weight\\\":0,\\\"category_ids\\\":[\\\"13\\\"],\\\"description\\\":\\\"<p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam.<\\\\\\\/p><p>Itaque earum rerum hic tenetur a sapiente delectus.<\\\\\\\/p><p>In common language usage, \\\\\\\"fruit\\\\\\\" normally means the fleshy seed-associated structures of a plant that are sweet or sour and edible in the raw state, such as apples, oranges, grapes, strawberries, bananas, and lemons.<\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p><p>Several transmissions were beamed to this ship by Rebel spies.<\\\\\\\/p><p>I'm surprised you had the courage to take the responsibility yourself.<\\\\\\\/p><p>It's more like ... suicide.<\\\\\\\/p><p>It is also the high-end model of the MacBook family and is currently produced with 13- and 15-inch screens, although a 17-inch version has been offered previously.<\\\\\\\/p><p>Don't act so surprised, Your Highness.<\\\\\\\/p><p>No!<\\\\\\\/p>\\\",\\\"short_description\\\":\\\"Size guide: CHOOSING A BRACELET SIZE BY MEASURING YOUR WRIST\\\\r\\\\nUse a tape measure. Wrap the tape measure around the wrist on Make a note of the number at the point where the tape meets the 0. This bracelet is intended to be worn loose so we have designed it with plenty of extra space to make sure you\\\\u2019re always comfortable and don\\\\u2019t have the need to remove it when doing daily activities.\\\",\\\"meta_title\\\":\\\"Meat Cooler\\\",\\\"meta_keyword\\\":\\\"Meat Cooler\\\",\\\"meta_description\\\":\\\"Meat Cooler\\\",\\\"url_key\\\":\\\"914199\\\",\\\"product_has_weight\\\":\\\"1\\\",\\\"quantity_and_stock_status\\\":null,\\\"sources\\\":\\\"[{\\\\\\\"source_code\\\\\\\":\\\\\\\"1234\\\\\\\",\\\\\\\"name\\\\\\\":\\\\\\\"colombo\\\\\\\",\\\\\\\"quantity\\\\\\\":2771,\\\\\\\"source_status\\\\\\\":1,\\\\\\\"status\\\\\\\":1}]\\\"}\",\"category_id\":\"13\",\"configurable_matrix\":[{\"configurable_attribute\":{\"size\":\"xxs\",\"color\":\"black\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_FB161MBRGL1\",\"sku\":\"FB161MBRGL1\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":1394,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":3,\"image\":\"\"},{\"configurable_attribute\":{\"size\":\"xxl\",\"color\":\"pink\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_MECO-1\",\"sku\":\"MECO-1\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":20,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.13,\"image\":\"\"},{\"configurable_attribute\":{\"size\":\"xxs\",\"color\":\"pink\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_MECO-2\",\"sku\":\"MECO-2\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":321,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.16,\"image\":\"\"},{\"configurable_attribute\":{\"size\":\"xxs\",\"color\":\"orange\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_MECO-3\",\"sku\":\"MECO-3\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":965,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.16,\"image\":\"\"},{\"configurable_attribute\":{\"size\":\"xs\",\"color\":\"orange\"},\"config_attributes\":[\"size\",\"color\"],\"name\":\"Meat Cooler_MECO-4\",\"sku\":\"MECO-4\",\"sources\":\"[{\\\"source_code\\\":\\\"1234\\\",\\\"name\\\":\\\"colombo\\\",\\\"quantity\\\":71,\\\"source_status\\\":1,\\\"status\\\":1}]\",\"price\":0.14999999999999999,\"image\":\"\"}],\"config_attributes\":[\"size\",\"color\",\"size\",\"color\",\"size\",\"color\",\"size\",\"color\",\"size\",\"color\"]}";
        $optionText= json_decode($optionText['variables']['product_data']['0'], true);
        /*echo "<pre>";
        // print_r($optionText);
        print_r($optionText);
        die(__FILE__);*/
//        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
//        $resultPage = $this->resultPageFactory->create();
//        $resultPage->setActiveMenu('Ced_GoodMarket::GoodMarket_knowledge_base');
//        $resultPage->getConfig()->getTitle()->prepend(__('GoodMarket Knowledge Base'));
//
//        return $resultPage;
    }
}
