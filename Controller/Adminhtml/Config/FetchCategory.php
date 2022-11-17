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

namespace Ced\GoodMarket\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Class FetchCategory
 * @package Ced\GoodMarket\Controller\Adminhtml\Config
 */
class FetchCategory extends Action
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
        Filesystem\Io\File                  $file,
        \Ced\GoodMarket\Helper\Data $data
    )
    {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->data=$data;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /*
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $folderPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('ced/goodmarket');
        if (!file_exists($folderPath)) {
            $this->file->mkdir($folderPath, 0777, true);
        }
        // fetch category
        try {
            $taxonomy =$this->data->fetchCategories();
            //json_decode(file_get_contents($folderPath.'/Categories.json'),true);
            $arr1 = $arr2 = $arr3 = $arr4 = $arr5 = $arr6 = $arr7 = [];
            $arr1[]=['id' => '162', 'name' =>'Default', 'path' => ['1','162'],'parent_id' => '1', 'children' => '7'];
            foreach ($taxonomy['data']['category']['children'] as $key => $value) {
                if (count($value['children']) > 0) {
                    $arr2[] = ['id' => $value['id'], 'name' => $value['name'], 'path' => explode('/',$value['path']),'parent_id' => '162', 'children' => count($value['children'])];
                } else {
                    $arr2[] = ['id' => $value['id'], 'name' => $value['name'],'path' => explode('/',$value['path']), 'parent_id' =>'162', 'children' => 0];
                }
                foreach ($value['children'] as $key1 => $value1) {
                    if (count($value1['children']) > 0) {
                        $arr3[] = ['parent_id' => $value['id'], 'id' => $value1['id'], 'name' => $value1['name'], 'path' => explode('/',$value1['path']), 'children' => count($value1['children'])];
                    } else {
                        $arr3[] = ['parent_id' => $value['id'], 'id' => $value1['id'], 'name' => $value1['name'], 'path' => explode('/',$value1['path']), 'children' => 0];
                    }
                    foreach ($value1['children'] as $key2 => $value2) {
                        if (count($value2['children']) > 0) {
                            $arr4[] = ['parent_id' => $value1['id'], 'id' => $value2['id'], 'name' => $value2['name'], 'path' => explode('/',$value2['path']), 'children' => count($value2['children'])];
                        } else {
                            $arr4[] = ['parent_id' => $value1['id'], 'id' => $value2['id'], 'name' => $value2['name'], 'path' => explode('/',$value2['path']), 'children' => 0];
                        }
                        foreach ($value2['children'] as $key3 => $value3) {
                            if (count($value3['children']) > 0) {
                                $arr5[] = ['parent_id' => $value2['id'], 'id' => $value3['id'], 'name' => $value3['name'], 'path' => explode('/',$value3['path']), 'children' => count($value3['children'])];
                            } else {
                                $arr5[] = ['parent_id' => $value2['id'], 'id' => $value3['id'], 'name' => $value3['name'], 'path' =>explode('/',$value3['path']), 'children' => 0];
                            }
                            foreach ($value3['children'] as $key4 => $value4) {
                                if (count($value4['children']) > 0) {
                                    $arr6[] = ['parent_id' => $value3['id'], 'id' => $value4['id'], 'name' => $value4['name'], 'path' => explode('/',$value4['path']), 'children' => count($value4['children'])];
                                } else {
                                    $arr6[] = ['parent_id' => $value3['id'], 'id' => $value4['id'], 'name' => $value4['name'], 'path' => explode('/',$value4['path']), 'children' => 0];
                                }
                                foreach ($value4['children'] as $key5 => $value5) {
                                    if (count($value5['children']) > 0) {
                                        $arr7[] = ['parent_id' => $value4['id'], 'id' => $value5['id'], 'name' => $value5['name'], 'path' => explode('/',$value5['path']), 'children' => count($value5['children'])];
                                    } else {
                                        $arr7[] = ['parent_id' => $value4['id'], 'id' => $value5['id'], 'name' => $value5['name'], 'path' => explode('/',$value5['path']), 'children' => 0];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $path = $folderPath . '/categoryLevel-1.json';
            $file = fopen($path, "w");
            fwrite($file, json_encode($arr1));
            fclose($file);
            $path = $folderPath . '/categoryLevel-2.json';
            $file = fopen($path, "w");
            fwrite($file, json_encode($arr2));
            fclose($file);
            $path = $folderPath . '/categoryLevel-3.json';
            $file = fopen($path, "w");
            fwrite($file, json_encode($arr3));
            fclose($file);
            $path = $folderPath . '/categoryLevel-4.json';
            $file = fopen($path, "w");
            fwrite($file, json_encode($arr4));
            fclose($file);
            $path = $folderPath . '/categoryLevel-5.json';
            $file = fopen($path, "w");
            fwrite($file, json_encode($arr5));
            fclose($file);
            $path = $folderPath . '/categoryLevel-6.json';
            $file = fopen($path, "w");
            fwrite($file, json_encode($arr6));
            fclose($file);
            $path = $folderPath . '/categoryLevel-7.json';
            $file = fopen($path, "w");
            fwrite($file, json_encode($arr7));
            fclose($file);
            $this->messageManager->addSuccessMessage(__('Categories Updated Successfully !!'));

//            $response['data'] = "<span style='color:green'>Categories Updated Successfully !!</span>";
//            $response['msg'] = "success";
        } catch (\Exception $e) {

            $this->messageManager->addErrorMessage(__('An Exception Has Taken Place'));

//            $response['data'] = "<span style='color:red'>Exception : " . $e->getMessage() . "</span>";
//            $response['msg'] = "error";
        }

        /**
         * @var \Magento\Framework\Controller\Result\Json $resultJson
         */
        return  $this->_redirect('*/profile/index');
    }
}
