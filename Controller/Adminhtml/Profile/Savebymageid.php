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

use Ced\GoodMarket\Model\ProfileFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\DataObject;

/**
 * class Savebymageid extends Action
 */
class Savebymageid extends Action
{
    public $_coreRegistry;
    public $_cache;

    /**
     * Save By Magento Id Constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Registry
     * @param DataObject
     * @param ProfileFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     * @param \Ced\GoodMarket\Helper\Data
     * @param \Magento\Catalog\Model\Product\ActionFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory
     * @param \Ced\GoodMarket\Helper\Logger
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        DataObject $data,
        ProfileFactory $profileFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Ced\GoodMarket\Helper\Data $helper,
        \Magento\Catalog\Model\Product\ActionFactory $productActionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJson,
        \Ced\GoodMarket\Helper\Logger $logger
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->profileFactory = $profileFactory;
        $this->data = $data;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->helper=$helper;
        $this->logger = $logger;
        $this->resultJson = $resultJson;
        $this->productActionFactory = $productActionFactory;
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
     * Save By MageId Execute function
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception\
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        // echo '<pre>'; print_r($params);exit;
        $category = $this->getRequest()->getParam('profileCategory');
        $magentoCat = $this->getRequest()->getParam('magentoCat');
        if ($category == '0') {
            $profile = $this->profileFactory->create()->load($magentoCat, 'magento_category');
            $this->unLinkProduct($profile->getId());

            // echo '<pre>'; print_r($profile->getData()); exit;
            // $profile->delete(); echo 'deleted';
            try {
                $result = $this->resultJson->create();
                if ($profile->delete()) {
                    return $result->setData('Profile Deleted');
                } else {
                    return $result->setData('Some error occured');
                }
            } catch (Exception $e) {
                $this->logger->addError($e->getMessage(), ['path' => __METHOD__]);
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
        $allAttribute=json_decode($categoryFetchData['groupwise_attributes'], true);
        $configurableAttributes=$this->helper->getConfigurableAttributes($categoryId);
        foreach ($configurableAttributes as $variationattribute) {
            $variationAttributes[]=strtolower($variationattribute['label']);
        }

        // if(empty($profileId)) {
        //     $profile = $this->profileFactory->create()->load($this->data->getProfileId());
        //     $profile->setData('profile_code', $profileCode);
        // }else{
        $profile = $this->profileFactory->create()->load($magentoCat, 'magento_category');
        if ($profile->getData()) {
        } else {
            $profile = $this->profileFactory->create()->load($this->data->getProfileId());
            $profile->setData('profile_code', $profileCode);
        }
        // }
//        $attributeSet = '4';
        $profile->setData('attribute_set', $allAttribute['attribute_set_id']);
        $profile->setData('variation_attribute', json_encode($variationAttributes));
        $profile->setData('profile_name', $profileName);
        $profile->setData('profile_status', $profileStatus);
        $profile->setData('magento_category', $magentoCat);
        $profile->setData('profile_category', json_encode($category));
        $profile->setData('category_data', json_encode($categoryFetchData));
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
        $result = $this->resultJson->create();
        return $result->setData('saved');
    }

    /**
     * @param $productId
     * @param $profileId
     * @return bool
     */
    public function _addProductToProfile($productId, $profileId)
    {
        $profileproduct = $this->_objectManager->create(\Ced\GoodMarket\Model\Profileproducts::class)
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
     * _deleteProductFromProfile
     *
     * @param $productId
     * @return bool
     * @throws \Exception
     */
    public function _deleteProductFromProfile($productId)
    {
        try {
            $this->_objectManager->create(\Ced\GoodMarket\Model\Profileproducts::class)
                ->deleteFromProfile($productId);
        } catch (\Exception $e) {
            throw $e;
            return false;
        }
        return true;
    }

    /**
     * unique_multidim_array
     *
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

    /**
     * unLinkProduct
     *
     * @param $profileId
     * @return void
     */
    private function unLinkProduct($profileId)
    {
        $oldIds = $this->_productCollectionFactory->create()
            ->addAttributeToFilter('goodmarket_profile_id', ['eq' => $profileId])
            ->getAllIds();
        if (!empty($oldIds)) {
            $this->productActionFactory->create()
                ->updateAttributes($oldIds, ['goodmarket_profile_id' => ''], 0);
        }
    }
}
