<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_GoodMarket
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\GoodMarket\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Ced\GoodMarket\Model\ProfileFactory;
use Magento\Framework\DataObject;

class Save extends Action
{
    public $_coreRegistry;
    public $_cache;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        DataObject $data,
        ProfileFactory $profileFactory,
        \Ced\GoodMarket\Helper\Data $helper
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->profileFactory = $profileFactory;
        $this->data = $data;
        $this->helper=$helper;
    }

    /**
     *
     * @param string $idFieldName
     * @return mixed
     */
    protected function _initProfile($idFieldName = 'pcode')
    {
        $profileCode = $this->getRequest()->getParam($idFieldName);
        $profile = $this->_objectManager->get('Ced\GoodMarket\Model\Profile');
        if ($profileCode) {
            $profile->loadByField('profile_code', $profileCode);
        }

        $this->getRequest()->setParam('is_goodmarket', 1);
        $this->_coreRegistry->register('current_profile', $profile);
        return $this->_coreRegistry->registry('current_profile');
    }

    /**
     *
     */
    public function execute()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        $profileId=$this->getRequest()->getParam('id');
        $profileCode = $this->getRequest()->getParam('profile_code');
        $profileProduct = $this->getRequest()->getParam('in_profile');
        $profileName = $this->getRequest()->getParam('profile_name');
        $profileStatus = $this->getRequest()->getParam('profile_status');
        $mageCategory = $this->getRequest()->getParam('magento_category');
        $profileData = $this->getRequest()->getPostValue();
        $category[] = isset($profileData['level_0']) ? $profileData['level_0'] : "";
        $category[] = isset($profileData['level_1']) ? $profileData['level_1'] : "";
        $category[] = isset($profileData['level_2']) ? $profileData['level_2'] : "";
        $category[] = isset($profileData['level_3']) ? $profileData['level_3'] : "";
        $category[] = isset($profileData['level_4']) ? $profileData['level_4'] : "";
        $category[] = isset($profileData['level_5']) ? $profileData['level_5'] : "";
        $category[] = isset($profileData['level_6']) ? $profileData['level_6'] : "";
        $categoryIds = array_filter($category);
        $categoryId = end($categoryIds);
        $categoryFetchData=$this->helper->getCategoryAttributes($categoryId);
        $configurableAttributes=$this->helper->getConfigurableAttributes($categoryId);
        foreach ($configurableAttributes as $variationattribute) {
            $variationAttributes[]=strtolower($variationattribute['label']);
        }
        if(empty($profileId)) {
            $profile = $this->profileFactory->create()->load($this->data->getProfileId());
            $profile->setData('profile_code', $profileCode);
        }else{
            $profile = $this->profileFactory->create()->load($profileId);
        }
        $attributeSet=$this->_session->getAttributeSet();
        $profile->setData('variation_attribute',json_encode($variationAttributes));
        $profile->setData('attribute_set',$attributeSet);
        $profile->setData('profile_name', $profileName);
        $profile->setData('profile_status', $profileStatus);
        $profile->setData('magento_category', $mageCategory);
        $profile->setData('profile_category', json_encode($category));
        $profile->setData('category_data',json_encode($categoryFetchData));
            $reqAttribute1 = [];
    $optAttribute1 = [];
    if (!empty($profileData['required_attributes'])) {
        $temAttribute1 = $this->unique_multidim_array($profileData['required_attributes'], 'goodmarket_attribute_name');
        $temp3 = $temp4 = [];
        foreach ($temAttribute1 as $item) {
            if ($item['required']) {
                $temp3['goodmarket_attribute_name'] = $item['goodmarket_attribute_name'];
                $temp3['goodmarket_attribute_type'] = $item['goodmarket_attribute_type'];
                $temp3['magento_attribute_code'] = $item['magento_attribute_code'];
                if (isset($item['default'])) {
                    $temp3['default'] = $item['default'];
                }
                $temp3['required'] = $item['required'];
                $reqAttribute1[] = $temp3;
            } else {
                $temp4['goodmarket_attribute_name'] = $item['goodmarket_attribute_name'];
                $temp4['goodmarket_attribute_type'] = $item['goodmarket_attribute_type'];
                $temp4['magento_attribute_code'] = $item['magento_attribute_code'];
                if (isset($item['default'])) {
                    $temp4['default'] = $item['default'];
                }
                $temp4['required'] = 0;
                $optAttribute1[] = $temp4;
            }
        }
        $goodmarketReqOptAttribute['required_attributes'] = $reqAttribute1;
        $goodmarketReqOptAttribute['optional_attributes'] = $optAttribute1;
        $profile->setProfileReqOptAttribute(json_encode($goodmarketReqOptAttribute));
    } else {
        $profile->setProfileReqOptAttribute('');
    }
        $profile->save();
        if ($profileProduct) {
            $profile->updateProducts($profileProduct);
        }
        if ($redirectBack && $redirectBack == 'edit') {
            $this->messageManager->addSuccessMessage(
                __(
                    '
		   		You Saved The GoodMarket Profile And Its Products.
		   			'
                )
            );
            $this->_redirect(
                '*/*/edit',
                [
                    'pcode' => $profileId,
                ]
            );
        } elseif ($redirectBack && $redirectBack == 'upload') {
            $this->messageManager->addSuccessMessage(
                __(
                    '
		   		You Saved The GoodMarket Profile And Its Products. Upload Product Now.
		   			'
                )
            );
            $this->_redirect(
                'goodmarket/product/index',
                [
                    'profile_id' => $profile->getId()
                ]
            );
        } else {
            $this->messageManager->addSuccessMessage(
                __(
                    '
		   		You Saved The GoodMarket Profile And Its Products.
		   		'
                )
            );
            $this->_redirect('*/*/');
        }
        return ;
    }

    /**
     * @param $productId
     * @param $profileId
     * @return bool
     */
    public function _addProductToProfile($productId, $profileId)
    {
        $profileproduct = $this->_objectManager->create("Ced\GoodMarket\Model\Profileproducts")
            ->deleteFromProfile($productId);

        if ($profileproduct->profileProductExists($productId, $profileId) === true) {
            return false;
        } else {
            $profileproduct->setProductId($productId);
            $profileproduct->setProfileId($profileId);
            $profileproduct->save();
            return true;
        }
    }

    /**
     * @param $productId
     * @return bool
     * @throws \Exception
     */
    public function _deleteProductFromProfile($productId)
    {
        try {
            $this->_objectManager->create("Ced\GoodMarket\Model\Profileproducts")
                ->deleteFromProfile($productId);
        } catch (\Exception $e) {
            throw $e;
            return false;
        }
        return true;
    }

    /**
     * @param $array
     * @param $key
     * @return array
     */
    public function unique_multidim_array($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ($array as $val) {

            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
}

//$optAttribute = $goodmarketAttribute = $goodmarketReqOptAttribute = [];
//$data = $this->_objectManager->create('Magento\Config\Model\Config\Structure\Element\Group')->getData();
//$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//$this->_context = $this->_objectManager->get('Magento\Framework\App\Helper\Context');
//$redirectBack = $this->getRequest()->getParam('back', false);
//$tab = $this->getRequest()->getParam('tab', false);
//$pcode = $this->getRequest()->getParam('pcode', false);
//$profileData = $this->getRequest()->getPostValue();
//echo"<pre>";
//print_r($profileData);
//die(__FILE__);
//$category[] = isset($profileData['level_0']) ? $profileData['level_0'] : "";
//$category[] = isset($profileData['level_1']) ? $profileData['level_1'] : "";
//$category[] = isset($profileData['level_2']) ? $profileData['level_2'] : "";
//$category[] = isset($profileData['level_3']) ? $profileData['level_3'] : "";
//$category[] = isset($profileData['level_4']) ? $profileData['level_4'] : "";
//$category[] = isset($profileData['level_5']) ? $profileData['level_5'] : "";
//$category[] = isset($profileData['level_6']) ? $profileData['level_6'] : "";
//
//$profileData = json_decode(json_encode($profileData), 1);
//
//$inProfile = $this->getRequest()->getParam('in_profile');
//$profileProducts = $this->getRequest()->getParam('in_profile_products', null);
//if (strlen($profileProducts) > 0) {
//    $profileProducts = explode(',', $this->getRequest()->getParam('in_profile_products', null));
//} else {
//    $profileProducts = [];
//}
//
//$profileData = json_decode(json_encode($profileData), 1);
//
//$resource = $this->getRequest()->getPost('resource', false);
//
//try {
//    $profile = $this->_initProfile('pcode');
//    if (!$profile->getId() && $pcode) {
//        $this->messageManager->addErrorMessage(__('This Profile no longer exists.'));
//        $this->_redirect('*/*/');
//        return;
//    }
//
//    $pname = $profileData['profile_name'];
//    if (isset($profileData['profile_code'])) {
//        $pcode = $profileData['profile_code'];
//        $profileCollection = $this->_objectManager->get('Ced\GoodMarket\Model\Profile')->getCollection()
//            ->addFieldToFilter('profile_code', $profileData['profile_code']);
//        if (count($profileCollection) > 0) {
//            $this->messageManager->addErrorMessage(__('This Profile Already Exist Please Change Profile Code'));
//            $this->_redirect('*/*/new');
//            return;
//        }
//    }

//    $profile->addData($profileData);
//    $profile->setProfileCategory(json_encode($category));
//    // save variant attribute
//    if (isset($profileData['variant_attributes'])) {
//        $profile->setConfigAttributes(json_encode($profileData['variant_attributes']));
//    } else {
//        $profile->setConfigAttributes('');
//    }
//    // save additional attribute
//    if (isset($profileData['additional_attributes'])) {
//        $profile->setAdditionalAttributes(json_encode($profileData['additional_attributes']));
//    } else {
//        $profile->setAdditionalAttributes('');
//    }
//    // save required attribute
//    $reqAttribute1 = [];
//    $optAttribute1 = [];
//    if (!empty($profileData['required_attributes'])) {
//        $temAttribute1 = $this->unique_multidim_array($profileData['required_attributes'], 'goodmarket_attribute_name');
//        $temp3 = $temp4 = [];
//        foreach ($temAttribute1 as $item) {
//            if ($item['required']) {
//                $temp3['goodmarket_attribute_name'] = $item['goodmarket_attribute_name'];
//                $temp3['goodmarket_attribute_type'] = $item['goodmarket_attribute_type'];
//                $temp3['magento_attribute_code'] = $item['magento_attribute_code'];
//                if (isset($item['default'])) {
//                    $temp3['default'] = $item['default'];
//                }
//                $temp3['required'] = $item['required'];
//                $reqAttribute1[] = $temp3;
//            } else {
//                $temp4['goodmarket_attribute_name'] = $item['goodmarket_attribute_name'];
//                $temp4['goodmarket_attribute_type'] = $item['goodmarket_attribute_type'];
//                $temp4['magento_attribute_code'] = $item['magento_attribute_code'];
//                if (isset($item['default'])) {
//                    $temp4['default'] = $item['default'];
//                }
//                $temp4['required'] = 0;
//                $optAttribute1[] = $temp4;
//            }
//        }
//        $goodmarketReqOptAttribute['required_attributes'] = $reqAttribute1;
//        $goodmarketReqOptAttribute['optional_attributes'] = $optAttribute1;
//
//        $profile->setProfileReqOptAttribute(json_encode($goodmarketReqOptAttribute));
//    } else {
//        $profile->setProfileReqOptAttribute('');
//    }
//
//    // save category personalization_instructions
//    if (isset($profileData['personalization_instructions'])) {
//        $profile->setPersonalizationInstructions($profileData['personalization_instructions']);
//    }
//    // save category styles
//    if (isset($profileData['styles'])) {
//        $profile->setStyles($profileData['styles']);
//    }
//    // save category tags
//    if (isset($profileData['tags'])) {
//        $profile->setTags($profileData['tags']);
//    }
//
//    //save profile
//    $profile->save();
////            $profile = $this->profileFactory->create()->load($pcode);
//    $profileProducts = $this->getRequest()->getParams();
//    if ($profileProducts) {
//        $profile->updateProducts($profileProducts);
//    }
//    //cache values
////            $oldProfileProducts = $this->_objectManager->create("Ced\GoodMarket\Model\Profileproducts")
////                ->getProfileProducts($profile->getId());
////
////
////            $deleteProds = array_diff($oldProfileProducts, $profileProducts);
////            $addProds = array_diff($profileProducts, $oldProfileProducts);
////
////
////            foreach ($deleteProds as $oUid) {
////                $this->_deleteProductFromProfile($oUid);
////                //$this->_cache->removeValue(\Ced\GoodMarket\Helper\Cache::PROFILE_PRODUCT_CACHE_KEY.$oUid);
////            }
////
////            foreach ($addProds as $nRuid) {
////                $this->_addProductToProfile($nRuid, $profile->getId());
////                //$this->_cache->setValue(\Ced\GoodMarket\Helper\Cache::PROFILE_PRODUCT_CACHE_KEY.$nRuid, $profile->getId());
////            }
//
//    if ($redirectBack && $redirectBack == 'edit') {
//        $this->messageManager->addSuccessMessage(
//            __(
//                '
//		   		You Saved The GoodMarket Profile And Its Products.
//		   			'
//            )
//        );
//        $this->_redirect(
//            '*/*/edit',
//            [
//                'pcode' => $pcode,
//            ]
//        );
//    } elseif ($redirectBack && $redirectBack == 'upload') {
//        $this->messageManager->addSuccessMessage(
//            __(
//                '
//		   		You Saved The GoodMarket Profile And Its Products. Upload Product Now.
//		   			'
//            )
//        );
//        $this->_redirect(
//            'goodmarket/products/index',
//            [
//                'profile_id' => $profile->getId()
//            ]
//        );
//    } else {
//        $this->messageManager->addSuccessMessage(
//            __(
//                '
//		   		You Saved The GoodMarket Profile And Its Products.
//		   		'
//            )
//        );
//        $this->_redirect('*/*/');
//    }
//} catch (\Exception $e) {
//    $this->messageManager->addErrorMessage(
//        __(
//            '
//		   		Unable to Save Profile Please Try Again.
//		   			' . $e->getMessage()
//        )
//    );
//    $this->_redirect(
//        '*/*/edit',
//        ['pcode' => $pcode]
//    );
//}
//}catch(\Exception $e)
//        {
//            echo "<pre>";
//            print_r($e->getMessage());
//            die(__FILE__);
//        }catch(\Error $e)
//        {
//            echo "<pre>";
//            print_r($e->getMessage());
//            die(__FILE__);
//        }
