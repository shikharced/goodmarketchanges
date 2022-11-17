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

class Savebymageid extends Action
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
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Ced\GoodMarket\Helper\Data $helper
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->profileFactory = $profileFactory;
        $this->data = $data;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->helper=$helper;
    }

    public function getProductCollectionByCategories($ids)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $ids]);
        $proId = [];
        foreach ($collection->getData() as $data) {
            $proId[] = $data['entity_id'];
        }
        $proId = implode(',', $proId);
        // echo '<pre>'; print_r($proId); exit;
        return $proId;
    }

    /**
     *
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        // echo '<pre>'; print_r($params);exit;
        $category = $this->getRequest()->getParam('profileCategory');
        $magentoCat = $this->getRequest()->getParam('magentoCat');
        if ($category == '0') {
            $profile = $this->profileFactory->create()->load($magentoCat, 'magento_category');
            // echo '<pre>'; print_r($profile->getData()); exit;
            // $profile->delete(); echo 'deleted';
            try {
                if ($profile->delete()){
                    echo 'Profile Deleted'; exit;
                } else {
                    echo  'Some error occured';
                }
            } catch (Exception $e) {
                echo $e->getMessage(); exit;
            }
        }
        $profileCode = 'gdmarket-' . $magentoCat;
        $profileProduct = $this->getProductCollectionByCategories($magentoCat);
        $profileName = 'Good Market ' . $profileCode;
        $profileStatus = 1;        
        $category = explode(',', $category);
        $categoryIds = array_filter($category);
        $categoryId = end($categoryIds);
        $categoryFetchData=$this->helper->getCategoryAttributes($categoryId);
        $configurableAttributes=$this->helper->getConfigurableAttributes($categoryId);
      
        // if(empty($profileId)) {
        //     $profile = $this->profileFactory->create()->load($this->data->getProfileId());
        //     $profile->setData('profile_code', $profileCode);
        // }else{
        $profile = $this->profileFactory->create()->load($magentoCat, 'magento_category');
        if ($profile->getData()){
        } else {
            $profile = $this->profileFactory->create()->load($this->data->getProfileId());
            $profile->setData('profile_code', $profileCode);
        }
        // }
        $attributeSet = '4';
        $profile->setData('attribute_set',$attributeSet);
        $profile->setData('profile_name', $profileName);
        $profile->setData('profile_status', $profileStatus);
        $profile->setData('magento_category', $magentoCat);
        $profile->setData('profile_category', json_encode($category));
        $profile->setData('category_data',json_encode($categoryFetchData));
        $reqAttribute1 = [];
        $optAttribute1 = [];
        $profileData['required_attributes'][] = 
        [
            'required' => 1,
            'goodmarket_attribute_name' => 'name',
            'goodmarket_attribute_type' => 'text',
            'magento_attribute_code' => 'name',
            'default' => '',
            'delete' => ''
        ];
        $profileData['required_attributes'][] = [
            'required' => 1,
            'goodmarket_attribute_name' => 'sku',
            'goodmarket_attribute_type' => 'text',
            'magento_attribute_code' => 'sku',
            'default' => '',
            'delete' => ''
        ];
        $profileData['required_attributes'][] = [
            'required' => 1,
            'goodmarket_attribute_name' => 'price',
            'goodmarket_attribute_type' => 'text',
            'magento_attribute_code' => 'price',
            'default' => '', 
            'delete' => ''
        ];
        $profileData['required_attributes'][] = [
            'required' => 1,
            'goodmarket_attribute_name' => 'description',
            'goodmarket_attribute_type' => 'textarea',
            'magento_attribute_code' => 'description',
            'default' => '',
            'delete' => ''
        ];
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
        echo 'saved';
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
