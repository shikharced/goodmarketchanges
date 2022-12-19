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
 * @category    Ced
 * @package     Ced_GoodMarket
 * @author      CedCommerce Core Team
<connect@cedcommerce.com>
 * @copyright   Copyright � 2018 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\FlagManager;
use Magento\Framework\Filesystem;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory */
    public $productCollectionFactory;

    /** @var \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory */
    public $productConfigurableFactory;

    /** @var \Ced\GoodMarket\Repository\Profile */
    public $profileRepository;

    /** @var Config */
    public $config;

    /** @var Logger */
    public $logger;

    /** @var Sdk */
    public $sdk;

    /** @var array $pause */
    public $pause = [];

    /** @var $product */
    public $product;

    /** @var \Magento\Store\Model\StoreManagerInterface $store */
    public $store;

    /** @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry */
    public $stockRegistry;

    /** @var MultiAccount */
    public $multiaccount;

    const INVENTORYSOUCEMAPPING = 'goodmarket/inventory_settings/inventoryMapping';

    /**
     * Product constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $configurableFactory
     * @param \Ced\GoodMarket\Repository\Profile $profileRepository
     * @param Logger $logger
     * @param Sdk $sdk
     * @param Config $config
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    public function __construct(
        Context                                                        $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Ced\GoodMarket\Helper\Config                                  $config,
        \Magento\CatalogInventory\Api\StockRegistryInterface           $stockRegistry,
        \Ced\GoodMarket\Model\ProfileFactory                           $profile,
        \Magento\Framework\App\Filesystem\DirectoryList                $directoryList,
        \Magento\Framework\Filesystem                                  $filesystem,
        \Ced\GoodMarket\Helper\Logger                                  $logger,
        \Magento\Catalog\Api\ScopedProductTierPriceManagementInterface $tierPrice,
        \Ced\GoodMarket\Helper\Data                                    $data,
        \Magento\Store\Model\StoreManagerInterface                     $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        FlagManager $flagManager,
        Filesystem\Io\File $file
    )
    {
        parent::__construct($context);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config = $config;
        $this->stockRegistry = $stockRegistry;
        $this->profile = $profile;
        $this->filesystem =$filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->directoryList = $directoryList;
        $this->logger = $logger;
        $this->tierPrice = $tierPrice;
        $this->data = $data;
        $this->storeManager = $storeManager;
        $this->objectManager=$objectmanager;
        $this->flagManager = $flagManager;
        $this->file = $file;
    }

    /**
     * Get inventory mapping
     *
     * @return string
     */
    public function getInventoryMapping()
    {
        $arr = [];
        $getJson = $this->scopeConfig->getValue(self::INVENTORYSOUCEMAPPING);
        $arr = json_decode($getJson, true);
        $source = [];
        foreach ($arr as $item) {
            $source[$item['local_inventory_code']] = $item['good_market_source'];
        }
        return $source;
    }

    /**
     * @param $ids
     * @param $type
     * @return array|void|null
     */
    public function prepareData($ids, $type)
    {
        try {
            $prepareData = [];
            $report = [];
            $prodData = [];
            if (!empty($ids)) {
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
                $collection = $this
                    ->productCollectionFactory
                    ->create()
                    ->setStoreId($this
                        ->config
                        ->getStoreId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', ['in' => $ids])/*->addFieldToFilter('goodmarket_status', '1')*/
                    ->addMediaGalleryData();
                $count = 0;
                $reportInv = [];
                $productArray = [];
                $customerGroupId = $this->config->getGroupName();
                if ($collection->getSize() > 0) {
                    /** @var \Magento\Catalog\Model\Product $product */
                    foreach ($collection->getItems() as $product) {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $configLoader = $objectManager->create('Ced\GoodMarket\Model\Profile');
                        $collection = $configLoader->load($product->getData('goodmarket_profile_id'));
                        $profile = $collection->getData();
                        $validate = $this->validateProduct($product->getData(), $type, $profile);
                        if ($validate != 1) {
                            $prodData[] = $validate;
                            continue;
                        }
                        if ($product->getTypeId() == 'configurable') {
                            $varientMapping=$this->scopeConfig->getvalue(
                                'goodmarket/goodmarket_product/mapping/varient'
                            );
                            if(!$varientMapping) {
                                $prodData[]='Please Map the Product Attributes Inside "GoodMarket Product Settings->Product Attribute Mapping" in the Configuration Section';
                                continue;
                            }
                            $catalogSession = $objectManager->create('\Magento\Catalog\Model\Session');
                            $catalogSession->unsQtyCount();
                            $productAttributes=[];
                            $category = [];
                            $categoryIds = json_decode($profile['profile_category'], true);
                            $filterCategoryId = array_filter($categoryIds);
                            $categoryId = end($filterCategoryId);
                            $productAttributes['set'] = $profile['attribute_set'];//$categoryAttribute['attribute_set_id'];
                            $productAttributes['type'] = 'configurable';
//                            $productAttributes['integ_type']='magento';
                            $image_role = array('image'=>'image1','small_image'=>'image1','thumbnail'=>'image1','swatch_image'=> '');
                            $productAttributes['image_role'] = json_encode($image_role);
                            if ($type == 'EditProduct') {
                                $products = $product->getData();
                                $productAttributes['id'] = $products['goodmarket_product_id'];
                                $productAttributes['category_id'] = $categoryId;
//                                $productAttributes['sku'] = $products['sku'];
                            }
                            $variant = true;
                            $parentId = $product->getId();
                            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productType */
                            $productType = $product->getTypeInstance();
                            $variationAttributes = $productType->getConfigurableAttributes($product);
                            $attributes = $productType->getConfigurableAttributes($product);
                            foreach($attributes as $_attribute){
                                $attributeCode = $_attribute->getProductAttribute()->getAttributeCode();;
                                $getConfigAttribute[] = strtolower($attributeCode);
                            }
                            /** @codingStandardsIgnoreStart */
                            $childIds = $productType->getChildrenIds($parentId);
                            /** @codingStandardsIgnoreEnd */

                            if (isset($childIds[0])) {
                                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $childs */
                                $childs = $this
                                    ->productCollectionFactory
                                    ->create()
                                    ->addAttributeToSelect('*')
                                    ->addAttributeToFilter('entity_id', ['in' => $childIds[0]])
                                    ->addMediaGalleryData();
                                /** @var \Magento\Catalog\Model\Product $child */
                                $variationcount=0;
                                foreach ($childs as $child) {
                                    $variations[] = $this->getVariationProducts($child, $profile,$getConfigAttribute);
                                    $variationcount++;
                                }
                                $superAttributeList=$this->getConfigurableVariable($profile);
                                $productAttributes['configurable_matrix'] = $variations;
                                for($i=0;$i<=$variationcount;$i++) {
                                    foreach ($getConfigAttribute as $value) {
                                        $config_attributes[] = $value;
                                    }
                                }
                                $productAttributes['config_attributes'] = $config_attributes;
                            }
                            $productAttributes['product'] = $this->getparentProductData($product, $profile, $categoryId, $type);
                            $prepareData[]=json_encode($productAttributes);
                        } else if ($product->getTypeId() == 'simple') {
                            $productAttributes=[];
                            if ($type == 'EditProduct') {
                                $products = $product->getData();
                                $productAttributes['id'] = $products['goodmarket_product_id'];
                            }
                            $category = [];
                            $categoryIds = json_decode($profile['profile_category'], true);
                            $filterCategoryId = array_filter($categoryIds);
                            $categoryId = end($filterCategoryId);
                            $productAttributes['set'] = $profile['attribute_set'];//$categoryAttribute['attribute_set_id'];
                            $productAttributes['type'] = 'simple';
//                            $productAttributes['integ_type']='magento';
                            $image_role = array('image'=>'image1','small_image'=>'image1','thumbnail'=>'image1','swatch_image'=> '');
                            $productAttributes['image_role'] = json_encode($image_role);
                            $category[] = $categoryId;
                            $productAttributes['product'] = $this->getSimpleProductData($product, $profile, $categoryId, $type);
                            $prepareData[]=json_encode($productAttributes);
//                            $report = $this->data->createProduct($productAttributes, $product, $type);
//                            return $report;

                        } else if ($product->getTypeId() == 'virtual') {
                            $productAttributes=[];
                            if ($type == 'EditProduct') {
                                $products = $product->getData();
                                $productAttributes['id'] = $products['goodmarket_product_id'];
                            }
                            $category = [];
                            $categoryIds = json_decode($profile['profile_category'], true);
                            $filterCategoryId = array_filter($categoryIds);
                            $categoryId = end($filterCategoryId);
                            $productAttributes['set'] = $profile['attribute_set'];//$categoryAttribute['attribute_set_id'];
                            $productAttributes['type'] = 'simple';
//                            $productAttributes['integ_type']='magento';
                            $image_role = array('image'=>'image1','small_image'=>'image1','thumbnail'=>'image1','swatch_image'=> '');
                            $productAttributes['image_role'] = json_encode($image_role);
                            $category[] = $categoryId;
                            $productAttributes['product'] = $this->getVirtualProductData($product, $profile, $categoryId, $type);
                            $prepareData[]=json_encode($productAttributes);
//                            $report = $this->data->createProduct($productAttributes, $product, $type);
//                            return $report;

                        }
                    }
                    if(!empty($prepareData)){
                        $report = $this->data->bulkProductUpload($prepareData, $type);
                    }
                    $report['data']['errorMessage']=$prodData;
                    return $report;
                }
            }
        }catch (\Exception $e) {
            $prodData=[];
//            $prodData['data']['saveProduct']['success'] = 0;
            $prodData['data']['errorMessage'][] = "Exception Has Taken Placed ".$e->getMessage();
            return $prodData;
        } catch (\Error $e) {
            $prodData=[];
//            $prodData['data']['saveProduct']['success'] = 0;
            $prodData['data']['errorMessage'][] = "Error Has Taken Placed ".$e->getMessage();
            return $prodData;
        }
    }

    /**
     * @param $ids
     * @param $type
     * @return array|array[]|void
     */
    public function productDeleteSync($ids,$type)
    {
        try {
            $prepareData = [];
            $report = [];
            if (!empty($ids)) {
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
                $collection = $this
                    ->productCollectionFactory
                    ->create()
                    ->setStoreId($this
                        ->config
                        ->getStoreId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', ['in' => $ids])/*->addFieldToFilter('goodmarket_status', '1')*/
                    ->addMediaGalleryData();
                $count = 0;
                $reportInv = [];
                $productArray = [];
                $customerGroupId = $this->config->getGroupName();
                if ($collection->getSize() > 0) {
                    /** @var \Magento\Catalog\Model\Product $product */
                    foreach ($collection->getItems() as $product) {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $configLoader = $objectManager->create('Ced\GoodMarket\Model\Profile');
                        $collection = $configLoader->load($product->getData('goodmarket_profile_id'));
                        $profile = $collection->getData();
                        $validate = $this->validateProduct($product->getData(), $type, $profile);
                        if ($validate != 1) {
                            $prodData = [];
                            if ($type == 'SyncInvProduct' || $type == 'DeleteProduct') {
                                $prodData['success'] = 0;
                                $prodData['message'] = $validate;
                                return [$prodData];
                            }
                        }
                        $priceAttribute= $this->getprofilePrice($profile);
                        $categoryIds = json_decode($profile['profile_category'], true);
                        $filterCategoryId = array_filter($categoryIds);
                        $categoryId = end($filterCategoryId);
                        if ($product->getTypeId() == 'configurable') {
                            $variant = true;
                            $parentId = $product->getId();
                            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productType */
                            $productType = $product->getTypeInstance();
                            /** @codingStandardsIgnoreStart */
                            $attributes = $productType->getConfigurableAttributes($product);
                            $childIds = $productType->getChildrenIds($parentId);
                            /** @codingStandardsIgnoreEnd */

                            if (isset($childIds[0])) {
                                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $childs */
                                $childs = $this
                                    ->productCollectionFactory
                                    ->create()
                                    ->addAttributeToSelect('*')
                                    ->addAttributeToFilter('entity_id', ['in' => $childIds[0]])
                                    ->addMediaGalleryData();
                                /** @var \Magento\Catalog\Model\Product $child */

                                foreach ($childs as $child) {
                                    if ($type == 'SyncInvProduct') {
                                        $price=$this->getGoodMarketProfilePrice($child,$priceAttribute);
                                        /*$qty = $this->getQuantityForUpload($child, $profile);
                                        $allSources=[];
                                        $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
                                        $allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);*/
                                        $allSources=[];
                                        $sourceMapping = $this->getInventoryMapping();
                                        foreach ($sourceMapping as $localSource => $gdmarketSource){
                                            $quantity = $this->getQuantityForMsi($child, $localSource);
                                            $gdmarketSourceArr = [];
                                            $gdmarketSourceArr = explode('+', $gdmarketSource);
                                            $allSources[] = [
                                                'source_code' => $gdmarketSourceArr[0],
                                                'name' => $gdmarketSourceArr[1],
                                                'quantity' => $quantity,
                                                'source_status' => 1,
                                                'status' => 1
                                            ];
                                        }
                                        $reportInv[] = $this->data->getProductInventorySync($child->getData(), $quantity, $price, $type,$allSources, $categoryId);
                                        continue;
                                    }
                                    if ($type == 'DeleteProduct') {
                                        $deleteproData[] = ['product_id' => $child->getGoodmarketProductId()];
                                        $deleteProdResponse[] = $this->data->deleteProduct($child, $deleteproData, $type);
                                        continue;
                                    }
                                }
                                if ($type == 'DeleteProduct') {
                                    $deleteproData[] = ['product_id' => $product->getGoodmarketProductId()];
                                    $deleteProdResponse[] = $this->data->deleteProduct($product, $deleteproData, $type);
                                    return $deleteProdResponse;
                                }
                                if ($type == 'SyncInvProduct') {
                                    return $reportInv;
                                }
                            }
                        } else if ($product->getTypeId() == 'simple') {
                            if ($type == 'SyncInvProduct') {
                                $price=$this->getGoodMarketProfilePrice($product,$priceAttribute);
                                /*$qty = $this->getQuantityForUpload($product, $profile);
                                $allSources=[];
                                $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
                                $allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);*/
                                $allSources=[];
                                $sourceMapping = $this->getInventoryMapping();
                                foreach ($sourceMapping as $localSource => $gdmarketSource){
                                    $quantity = $this->getQuantityForMsi($product, $localSource);
                                    $gdmarketSourceArr = [];
                                    $gdmarketSourceArr = explode('+', $gdmarketSource);
                                    $allSources[] = [
                                        'source_code' => $gdmarketSourceArr[0],
                                        'name' => $gdmarketSourceArr[1],
                                        'quantity' => $quantity,
                                        'source_status' => 1,
                                        'status' => 1
                                    ];
                                }

                                $reportInv[] = $this->data->getProductInventorySync($product->getData(), $quantity, $price,$type,$allSources,$categoryId);
                                return $reportInv;
                            }
                            if ($type == 'DeleteProduct') {
                                $deleteproData[] = ['product_id' => $product->getGoodmarketProductId()];
                                $deleteProdResponse[] = $this->data->deleteProduct($product, $deleteproData, $type);
                                return $deleteProdResponse;
                            }
                        }
                    }
                    return $report;
                }
            }
        }catch (\Exception $e)
        {
            if ($type == 'SyncInvProduct' || $type == 'DeleteProduct') {
                $prodData['success'] = 0;
                $prodData['message'] = "Exception Has Taken Placed ".$e->getMessage();
                return [$prodData];
            }
        }
        catch (\Error $e)
        {
            if ($type == 'SyncInvProduct' || $type == 'DeleteProduct') {
                $prodData['success'] = 0;
                $prodData['message'] = "Error Has Taken Placed ".$e->getMessage();
                return [$prodData];
            }
        }
    }

    /**
     * @param $profile
     * @return mixed
     */
    public function getprofilePrice($profile)
    {
        $requireAttribute=json_decode($profile['profile_req_opt_attribute'],true);
        foreach ($requireAttribute['required_attributes'] as $attribute)
        {
            if($attribute['goodmarket_attribute_name']=='price')
            {
                $price= $attribute['magento_attribute_code'];
            }
        }
        return $price;

    }

    /**
     * @param $product
     * @param $profile
     * @param $catId
     * @return false|string
     */
    public function getparentProductData($product, $profile, $catId,$type)
    {
        $reqAtt = ['name', 'sku', 'price'];
        $currentStore = $this->storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageurl = $mediaUrl . 'catalog/product';
        $productArray = [];
        $weight = false;
        $description = false;
        $quantity = false;
        $profileMapping = json_decode($profile['profile_req_opt_attribute'], true);
        foreach ($profileMapping['required_attributes'] as $required_attribute) {
            if(!in_array($required_attribute['goodmarket_attribute_name'],$reqAtt)) {
                continue;
            }
            if ($required_attribute['magento_attribute_code']=='default') {
                $productArray[$required_attribute['goodmarket_attribute_name']] = $required_attribute['default'];
                continue;
            }
            if($required_attribute['goodmarket_attribute_name']=='price') {
                $price=$this->getGoodMarketProfilePrice($product,$required_attribute['magento_attribute_code']);
                $productArray[$required_attribute['goodmarket_attribute_name']]=$price;
                continue;
            }
            if ($required_attribute['magento_attribute_code']=='description') {
                $productArray[$required_attribute['goodmarket_attribute_name']] = strip_tags($product->getData('description'), "<p><b>");
                continue;
            }
            $productArray[$required_attribute['goodmarket_attribute_name']] = $product->getData($required_attribute['magento_attribute_code']);
        }
        foreach ($profileMapping['optional_attributes'] as $optional_attribute) {
            if ($optional_attribute['goodmarket_attribute_name'] == 'weight') {
                $weight = true;
            }
            if ($optional_attribute['goodmarket_attribute_name'] == 'description') {
                $description = true;
            }
            if ($optional_attribute['goodmarket_attribute_name'] == 'quantity_and_stock_status') {
                $quantity = true;
                $allSources = [];
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $catalogSession = $objectManager->create('\Magento\Catalog\Model\Session');
                $qty=$catalogSession->getQtyCount();
                $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
                $allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);
                $productArray['sources']=json_encode($allSources);
                continue;
            }
            if ($optional_attribute['magento_attribute_code']=='default') {
                $productArray[$optional_attribute['goodmarket_attribute_name']] = $optional_attribute['default'];
                continue;
            }
            $productArray[$optional_attribute['goodmarket_attribute_name']] = $product->getData($optional_attribute['magento_attribute_code']);
        }
        if (!$description) {
            $productArray['description'] = $product->getData('description');
        }
        $productArray['meta_title']= $product->getData('name');
        $productArray['meta_keyword']= $product->getData('name');
        $productArray['meta_description']= $product->getData('name');
        $productArray['url_key']= $product->getData('name').'12345678988668';
        $productArray['quantity_and_stock_status']= 'null';
        if (!$weight) {
            $weightUnit = $this->getWeightUnit();
//            Change weight to grams 30Nov - Shikhar
            if ($weightUnit == 'lbs') {
                $productWeight = $product->getWeight() * 453.6;
            } elseif($weightUnit == 'kg'){
                $productWeight = $product->getWeight()*1000;
            }
            $productweight = !empty($product->getWeight()) ? round($productWeight) : '2';
            $productArray['weight'] = $productweight;
            $productArray['product_has_weight'] = '1';
        }

        if (!$quantity) {
            $allSources = [];
//            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//            $catalogSession = $objectManager->create('\Magento\Catalog\Model\Session');
//            $qty=$catalogSession->getQtyCount();
//            $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
//            $allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);

            /*Inventory Work - SHIKHAR*/
            $sourceMapping = $this->getInventoryMapping();
            foreach ($sourceMapping as $localSource => $gdmarketSource){
                $quantity = $this->getQuantityForMsi($product, $localSource);
                $gdmarketSourceArr = [];
                $gdmarketSourceArr = explode('+', $gdmarketSource);
                $allSources[] = [
                    'source_code' => $gdmarketSourceArr[0],
                    'name' => $gdmarketSourceArr[1],
                    'quantity' => $quantity,
                    'source_status' => 1,
                    'status' => 1
                ];
            }

            /*Inventory Work END - SHIKHAR*/

            $productArray['sources']=json_encode($allSources);
        }
        $media_gallery = $product->getData('media_gallery');
//        New Changes By Shikhar
        $imagecount=2;
        foreach ($media_gallery['images'] as $key=>$image) {
            $baseImageUrl=$imageurl.$image['file'];
            $base = $imageurl.$product->getData('image');
            if ($baseImageUrl == $base) {
                $imageencode=$this->get_img_data($baseImageUrl);
                if(!empty($imageencode)) {
                    $productImage['image1']=$imageencode;
                }
            } else {
                $imageencode=$this->get_img_data($baseImageUrl);
                if(!empty($imageencode)) {
                    $productImage['image'.$imagecount]=$imageencode;
                    $imagecount++;
                }
            }
        }
//      New Changes END By Shikhar
        if (isset($productImage)) {
            $productImages['images'] = json_encode($productImage);
            if($type!='EditProduct') {
                $productArray['media_gallery'] = json_encode($productImages);
            }
        }
        $productArray['category_ids'][] = $catId;
        $productArray['integ_type']='magento';
//        echo '<pre>'; print_r($productArray);exit;
        return json_encode($productArray);
    }

    public function getQuantityForMsi($product, $sourceCode)
    {
        $_children = $product->getTypeInstance()->getUsedProducts($product);
        $qtySum = 0;
        foreach ($_children as $child){
            $msiSourceDataModel = $this->objectManager->create('\Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku');
            $invSourceData = $msiSourceDataModel->execute($child->getSku());
            if ($invSourceData && is_array($invSourceData) && count($invSourceData) > 0) {
                $invSourceData = array_column($invSourceData, 'quantity', 'source_code');
                $quantity = isset($invSourceData[$sourceCode]) ? $invSourceData[$sourceCode] : 0;
                $qtySum = $qtySum + $quantity;
            }
        }
        return $qtySum;
    }

    /**
     * Get Weight Unit
     *
     * @return mixed
     */
    public function getWeightUnit()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_scopeConfig = $objectManager->create(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        return $this->_scopeConfig->getValue(
            'general/locale/weight_unit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $ids
     * @return array|void
     */
    public function updateBulkInventory($ids)
    {
        if (!empty($ids)) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this
                ->productCollectionFactory
                ->create()
                ->setStoreId($this
                    ->config
                    ->getStoreId())
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', ['in' => $ids])/*->addFieldToFilter('goodmarket_status', '1')*/
                ->addMediaGalleryData();
            $reportInv = [];
            if ($collection->getSize() > 0) {
                /** @var \Magento\Catalog\Model\Product $product */
                foreach ($collection->getItems() as $product) {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $configLoader = $objectManager->create('Ced\GoodMarket\Model\Profile');
                    $collection = $configLoader->load($product->getData('goodmarket_profile_id'));
                    $profile = $collection->getData();
                    if(empty($product->getData('goodmarket_product_id'))) {
                        continue;
                    }
                    $priceAttribute= $this->getprofilePrice($profile);
                    $categoryIds = json_decode($profile['profile_category'], true);
                    $filterCategoryId = array_filter($categoryIds);
                    $categoryId = end($filterCategoryId);
                    if ($product->getTypeId() == 'configurable') {
                        $variant = true;
                        $parentId = $product->getId();
                        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productType */
                        $productType = $product->getTypeInstance();
                        /** @codingStandardsIgnoreStart */
                        $childIds = $productType->getChildrenIds($parentId);
                        if (isset($childIds[0])) {
                            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $childs */
                            $childs = $this
                                ->productCollectionFactory
                                ->create()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('entity_id', ['in' => $childIds[0]])
                                ->addMediaGalleryData();
                            /** @var \Magento\Catalog\Model\Product $child */

                            foreach ($childs as $child) {
                                $price=$this->getGoodMarketProfilePrice($product,$priceAttribute);
                                $qty=$this->getQuantityForUpload($child,$profile);
                                $allSources=[];
                                $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
                                $allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);
                                $reportInv[] = $this->data->getProductInventorySync($child->getData(),$qty,$price,'InvSyncByCron',$allSources,$categoryId);
                            }
                        }
                    } else if ($product->getTypeId() == 'simple') {
                        $price=$this->getGoodMarketProfilePrice($product,$priceAttribute);
                        $qty=$this->getQuantityForUpload($product,$profile);
                        $allSources=[];
                        $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
                        $allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);
                        $reportInv[] = $this->data->getProductInventorySync($product->getData(),$qty,$price,'InvSyncByCron',$allSources,$categoryId);
                    }
                }
                return $reportInv;
            }
        }
    }

    /**
     * @param $child
     * @param $profile
     * @param $variationAttributes
     * @return false|string
     */
    public function getVariationProducts($child,$profile,$getConfigAttribute)
    {
        try {
            $color=explode(',',$this->scopeConfig->getvalue(
                'goodmarket/goodmarket_product/mapping/product_color'
            ));
            $size=explode(',',$this->scopeConfig->getvalue(
                'goodmarket/goodmarket_product/mapping/product_size'
            ));
            $type=explode(',',$this->scopeConfig->getvalue(
                'goodmarket/goodmarket_product/mapping/product_type'
            ));

//            $material=$this->scopeConfig->getvalue(
//                'goodmarket/goodmarket_product/mapping/product_material'
//            );
//            $reqAtt = ['name', 'sku', 'price'];
//            $profileVariationAttribute = json_decode($profile['variation_attribute'], true);
//            $requiredAttribute = json_decode($profile['profile_req_opt_attribute'], true);
//            foreach ($requiredAttribute['required_attributes'] as $attribute) {
//                if (in_array($attribute['goodmarket_attribute_name'],$reqAtt)) {
//                    if ($attribute['magento_attribute_code'] == 'default') {
//                        $configurable_attribute[$attribute['goodmarket_attribute_name']] = $attribute['default'];
//                        continue;
//                    } else if ($attribute['magento_attribute_code'] == 'Not-Required') {
//                        continue;
//                    }
//                    $configurable_attribute[$attribute['goodmarket_attribute_name']] = $child->getData($attribute['magento_attribute_code']);
//                }
//            }
            foreach ($getConfigAttribute as $key=>$prodAttribute) {
                $this->_objectManager= \Magento\Framework\App\ObjectManager::getInstance();
                $_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($child->getData('entity_id'));
                $_product->getResource()->getAttribute($prodAttribute)->getFrontend()->getValue($_product);
                $optionId = $child->getData($prodAttribute);
                $attribute = $_product->getResource()->getAttribute($prodAttribute);
                if ($attribute->usesSource()) {
                    $optionText = $attribute->getSource()->getOptionText($optionId);
                }
                if(in_array($prodAttribute,$size)) {
                    $configurableAttri[$prodAttribute]=$optionText;
                    $config_attributes[]=$prodAttribute;//$child->getData($attribute);
                }else if(in_array($prodAttribute,$color)) {
                    $configurableAttri[$prodAttribute]=$optionText;
                    $config_attributes[]=$prodAttribute;//$child->getData($attribute);
                }else if(in_array($prodAttribute,$type)) {
                    $configurableAttri[$prodAttribute]=$optionText;
                    $config_attributes[]=$prodAttribute;//$child->getData($attribute);
                }
            }
            $productArray['name'] = $child->getName();
            $productArray['sku'] = $child->getSku();
            $qty=$this->getQuantityForUpload($child,$profile);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $catalogSession = $objectManager->create('\Magento\Catalog\Model\Session');
            $sessionQty=$catalogSession->getQtyCount();
            if(isset($sessionQty))
            {
                $sessionQty=$sessionQty+$qty;
                $catalogSession->setQtyCount($sessionQty);
            }else{
                $catalogSession->setQtyCount($qty);
            }
            $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
            /*$allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);*/

            /*Shikhar - Changes for source Quantity*/
            $sourceMapping = $this->getInventoryMapping();
            foreach ($sourceMapping as $localSource => $gdmarketSource){
                /*$msiSourceCode = $this->config->getMsiSourceCode();*/
                $msiSourceDataModel = $this->objectManager->create('\Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku');
                $invSourceData = $msiSourceDataModel->execute($child->getSku());
                if ($invSourceData && is_array($invSourceData) && count($invSourceData) > 0) {
                    $invSourceData = array_column($invSourceData, 'quantity', 'source_code');
                    $quantity = isset($invSourceData[$localSource]) ? $invSourceData[$localSource] : 0;
                }
                $gdmarketSourceArr = [];
                $gdmarketSourceArr = explode('+', $gdmarketSource);
                $allSources[] = [
                    'source_code' => $gdmarketSourceArr[0],
                    'name' => $gdmarketSourceArr[1],
                    'quantity' => $quantity,
                    'source_status' => 1,
                    'status' => 1
                ];
            }
            /*Shikhar - Changes for source Quantity END*/
            $productArray['sources']=json_encode($allSources);
//            $productArray['quantity'] = $qty;
            $price = $this->getGoodMarketProfilePrice($child, 'price');
            $productArray['price'] = $price;
            $productArray['integ_type']='magento';
//        foreach ($variationAttributes as $attribute) {
//            if(in_array($attribute,$profileVariationAttribute)) {
//                $configurable_attribute[$attribute] = $child->getData($attribute);
//            }
//        }
            $productArray['configurable_attribute'] = $configurableAttri;
            $productArray['config_attributes'] = $config_attributes;
//            echo '<pre>'; print_r($productArray); exit;
            return $productArray;
        }catch (\Exception $e)
        {
            echo "<pre>";
            print_r($e->getMessage());
            die(__FILE__);
        }

    }

    public function getConfigurableVariable($profile)
    {
        try {
            $reqAtt = ['name', 'sku', 'price'];
            $requiredAttribute = json_decode($profile['profile_req_opt_attribute'], true);
            foreach ($requiredAttribute['required_attributes'] as $attribute) {
                if (!in_array($attribute['goodmarket_attribute_name'], $reqAtt)) {
                    if ($attribute['magento_attribute_code'] == 'default') {
                        if ($attribute['default'] == 'No') {
                            continue;
                        }
                        $configurable_attribute[] = $attribute['goodmarket_attribute_name'];
                        continue;
                    } else if ($attribute['magento_attribute_code'] == 'Not-Required') {
                        continue;
                    }
                    $configurable_attribute[] = $attribute['goodmarket_attribute_name'];
                }
            }
            return $configurable_attribute;
        }catch (\Exception $e)
        {
            echo "<pre>";
            print_r($e->getMessage());
            die(__FILE__);
        }
    }
    /**
     * @param $product
     * @param $profile
     * @param $catId
     * @return false|string
     */
    public function getSimpleProductData($product,$profile,$catId,$type)
    {
        $currentStore = $this->storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageurl=$mediaUrl.'catalog/product';
        $productArray=[];
        $weight=false;
        $description=false;
        $quantity=false;
//        $simpleRequireAttribut=['price','sku','name'];
        $profileMapping=json_decode($profile['profile_req_opt_attribute'],true);
        foreach ($profileMapping['required_attributes'] as $required_attribute) {

//            if(!in_array($required_attribute['goodmarket_attribute_name'],$simpleRequireAttribut)) {
//                continue;
//            }
            if ($required_attribute['magento_attribute_code']=='default') {
                $productArray[$required_attribute['goodmarket_attribute_name']] = $required_attribute['default'];
                continue;
            }

            if ($required_attribute['magento_attribute_code']=='description') {
                $productArray[$required_attribute['goodmarket_attribute_name']] = strip_tags($product->getData('description'), "<p><b>");
                continue;
            }

            if($required_attribute['goodmarket_attribute_name']=='price') {
                $price=$this->getGoodMarketProfilePrice($product,$required_attribute['magento_attribute_code']);
                $productArray[$required_attribute['goodmarket_attribute_name']]=$price;
                continue;
            }
            $productArray[$required_attribute['goodmarket_attribute_name']]=$product->getData($required_attribute['magento_attribute_code']);
        }
        $productArray['meta_title']= $product->getData('name');
        $productArray['meta_keyword']= $product->getData('name');
        $productArray['meta_description']= $product->getData('name');
        $productArray['url_key']= $product->getData('name');
        foreach ($profileMapping['optional_attributes'] as $optional_attribute) {
            if($optional_attribute['goodmarket_attribute_name']=='weight') {
                $weight=true;
            }
            if($optional_attribute['goodmarket_attribute_name']=='quantity_and_stock_status') {
                $allSources = [];
                $qty=$this->getQuantityForUpload($product,$profile);
                $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
                $allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);
                $productArray['sources']=json_encode($allSources);
                $quantity=true;
                continue;
            }
            if($optional_attribute['magento_attribute_code']=='default') {
                $productArray[$optional_attribute['goodmarket_attribute_name']] = $optional_attribute['default'];
                continue;
            }
            $productArray[$optional_attribute['goodmarket_attribute_name']]=$product->getData($optional_attribute['magento_attribute_code']);
        }
        if(!$weight) {
//            $productweight=!empty($product->getWeight())?$product->getWeight():'2';
//            Change weight to grams 30Nov - Shikhar
            $weightUnit = $this->getWeightUnit();
            if ($weightUnit == 'lbs') {
                $productWeight = $product->getWeight() * 453.6;
            } elseif($weightUnit == 'kg'){
                $productWeight = $product->getWeight() * 1000;
            }
            $productweight = !empty($product->getWeight()) ? round($productWeight) : '2';
            $productArray['weight']=$productweight;
            $productArray['product_has_weight']='1';

        }
        if(!$quantity) {
            $allSources = [];
            /*$qty=$this->getQuantityForUpload($product,$profile);
            $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
            $allSources[] = array(
                "source_code"=>$location_saved_data[0]['source_code'],
                "name"=> $location_saved_data[0]['name'],
                "quantity"=>$qty,
                "source_status"=>1,
                "status"=>1
            );*/

            /*Quantity Multi Source Assign Work - Shikhar*/

            $sourceMapping = $this->getInventoryMapping();
            foreach ($sourceMapping as $localSource => $gdmarketSource){
                /*$msiSourceCode = $this->config->getMsiSourceCode();*/
                $msiSourceDataModel = $this->objectManager->create('\Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku');
                $invSourceData = $msiSourceDataModel->execute($product->getSku());
                if ($invSourceData && is_array($invSourceData) && count($invSourceData) > 0) {
                    $invSourceData = array_column($invSourceData, 'quantity', 'source_code');
                    $quantity = isset($invSourceData[$localSource]) ? $invSourceData[$localSource] : 0;
                }
                $gdmarketSourceArr = [];
                $gdmarketSourceArr = explode('+', $gdmarketSource);
//                echo '<pre>'; print_r($gdmarketSourceArr); exit;
                $allSources[] = [
                    'source_code' => $gdmarketSourceArr[0],
                    'name' => $gdmarketSourceArr[1],
                    'quantity' => $quantity,
                    'source_status' => 1,
                    'status' => 1
                ];
            }
            /*Quantity Multi Source Assign Work END - Shikhar*/


            $productArray['sources']=json_encode($allSources);
        }
        $media_gallery=$product->getData('media_gallery');
//        New Changes By Shikhar
        $imagecount=2;
        foreach ($media_gallery['images'] as $key=>$image) {
            $baseImageUrl=$imageurl.$image['file'];
            $base = $imageurl.$product->getData('image');
             if ($baseImageUrl == $base) {
                 $imageencode=$this->get_img_data($baseImageUrl);
                 if(!empty($imageencode)) {
                     $productImage['image1']=$imageencode;
                 }
             } else {
                 $imageencode=$this->get_img_data($baseImageUrl);
                 if(!empty($imageencode)) {
                     $productImage['image'.$imagecount]=$imageencode;
                     $imagecount++;
                 }
             }
        }
//      New Changes END By Shikhar
        if(isset($productImage)) {
            $productImages['images']=json_encode($productImage);
            if($type!='EditProduct') {
                $productArray['media_gallery']=json_encode($productImages);
            }
            /*$productArray['media_gallery']=json_encode($productImages);*/
        }

        $productArray['category_ids']=[$catId];
        $productArray['integ_type']='magento';
//        echo '<pre>'; print_r($productArray);exit;
        return json_encode($productArray);
    }

    /**
     * @param $product
     * @param $profile
     * @param $catId
     * @return false|string
     */
    public function getVirtualProductData($product,$profile,$catId,$type)
    {
        $currentStore = $this->storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageurl=$mediaUrl.'catalog/product';
        $productArray=[];
        $weight=false;
        $description=false;
        $quantity=false;
//        $simpleRequireAttribut=['price','sku','name'];
        $profileMapping=json_decode($profile['profile_req_opt_attribute'],true);
        foreach ($profileMapping['required_attributes'] as $required_attribute) {

//            if(!in_array($required_attribute['goodmarket_attribute_name'],$simpleRequireAttribut)) {
//                continue;
//            }
            if ($required_attribute['magento_attribute_code']=='default') {
                $productArray[$required_attribute['goodmarket_attribute_name']] = $required_attribute['default'];
                continue;
            }

            if ($required_attribute['magento_attribute_code']=='description') {
                $productArray[$required_attribute['goodmarket_attribute_name']] = strip_tags($product->getData('description'), "<p><b>");
                continue;
            }

            if($required_attribute['goodmarket_attribute_name']=='price') {
                $price=$this->getGoodMarketProfilePrice($product,$required_attribute['magento_attribute_code']);
                $productArray[$required_attribute['goodmarket_attribute_name']]=$price;
                continue;
            }
            $productArray[$required_attribute['goodmarket_attribute_name']]=$product->getData($required_attribute['magento_attribute_code']);
        }
        $productArray['meta_title']= $product->getData('name');
        $productArray['meta_keyword']= $product->getData('name');
        $productArray['meta_description']= $product->getData('name');
        $productArray['url_key']= $product->getData('name');
        foreach ($profileMapping['optional_attributes'] as $optional_attribute) {
            if($optional_attribute['goodmarket_attribute_name']=='weight') {
                $weight=true;
            }
            if($optional_attribute['goodmarket_attribute_name']=='quantity_and_stock_status') {
                $allSources = [];
                $qty=$this->getQuantityForUpload($product,$profile);
                $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
                $allSources[] = array("source_code"=>$location_saved_data[0]['source_code'], "name"=> $location_saved_data[0]['name'],"quantity"=>$qty,"source_status"=>1,"status"=>1);
                $productArray['sources']=json_encode($allSources);
                $quantity=true;
                continue;
            }
            if($optional_attribute['magento_attribute_code']=='default') {
                $productArray[$optional_attribute['goodmarket_attribute_name']] = $optional_attribute['default'];
                continue;
            }
            $productArray[$optional_attribute['goodmarket_attribute_name']]=$product->getData($optional_attribute['magento_attribute_code']);
        }

        $productArray['weight'] = '0';
        $productArray['product_has_weight'] = '0';

        if(!$quantity) {
            $allSources = [];
            $qty=$this->getQuantityForUpload($product,$profile);
            $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);

            /*Quantity Multi Source Assign Work - Shikhar*/

            $sourceMapping = $this->getInventoryMapping();
            foreach ($sourceMapping as $localSource => $gdmarketSource){
                /*$msiSourceCode = $this->config->getMsiSourceCode();*/
                $msiSourceDataModel = $this->objectManager->create('\Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku');
                $invSourceData = $msiSourceDataModel->execute($product->getSku());
                if ($invSourceData && is_array($invSourceData) && count($invSourceData) > 0) {
                    $invSourceData = array_column($invSourceData, 'quantity', 'source_code');
                    $quantity = isset($invSourceData[$localSource]) ? $invSourceData[$localSource] : 0;
                }
                $gdmarketSourceArr = [];
                $gdmarketSourceArr = explode('+', $gdmarketSource);
                $allSources[] = [
                    'source_code' => $gdmarketSourceArr[0],
                    'name' => $gdmarketSourceArr[1],
                    'quantity' => $quantity,
                    'source_status' => 1,
                    'status' => 1
                ];
            }
            /*Quantity Multi Source Assign Work END - Shikhar*/

            $productArray['sources']=json_encode($allSources);
        }
        $media_gallery=$product->getData('media_gallery');
//        New Changes By Shikhar
        $imagecount=2;
        foreach ($media_gallery['images'] as $key=>$image) {
            $baseImageUrl=$imageurl.$image['file'];
            $base = $imageurl.$product->getData('image');
            if ($baseImageUrl == $base) {
                $imageencode=$this->get_img_data($baseImageUrl);
                if(!empty($imageencode)) {
                    $productImage['image1']=$imageencode;
                }
            } else {
                $imageencode=$this->get_img_data($baseImageUrl);
                if(!empty($imageencode)) {
                    $productImage['image'.$imagecount]=$imageencode;
                    $imagecount++;
                }
            }
        }
//      New Changes END By Shikhar

        if(isset($productImage)) {
            $productImages['images']=json_encode($productImage);
            if($type!='EditProduct') {
                $productArray['media_gallery']=json_encode($productImages);
            }

        }
        $productArray['category_ids']=[$catId];
        $productArray['integ_type']='magento';
//        echo '<pre>'; print_r($productArray); exit;
        return json_encode($productArray);
    }

//    /**
//     * @param $image_url_id
//     * @return array|string
//     */
//    public function get_img_data($image_url_id) {
//        $woo_products_image        = $image_url_id;
//        $image_type_check = @exif_imagetype($woo_products_image);//Get image type + check if exists
//        if (strpos($http_response_header[0], "403") || strpos($http_response_header[0], "404") || strpos($http_response_header[0], "302") || strpos($http_response_header[0], "301")) {
//            return "";
//        }
//        $imgdata = file_get_contents($woo_products_image);
//        $mime_type = getimagesizefromstring($imgdata);
//        $img_send_data = base64_encode($imgdata);
//        $image_data_api[0] = 'http://localhost/web/wc_pro/';
//        $image_data_api_file['file'] = "data:".$mime_type['mime'].";base64,".$img_send_data;
//        $image_data_api_file['file_name'] = basename($woo_products_image);
//        $img_file_wrp['file'] = json_encode($image_data_api_file);
//        return ($img_file_wrp);
//
//    }

    public function get_img_data($image_url_id) {
        try {
            $woo_products_image = $image_url_id;
            $image_type_check = @exif_imagetype($woo_products_image);//Get image type + check if exists
            if (strpos($http_response_header[0], "403") || strpos($http_response_header[0], "404") || strpos($http_response_header[0], "302") || strpos($http_response_header[0], "301")) {
                return "";
            }
            $imgdata = file_get_contents($woo_products_image);
            $mime_type = getimagesizefromstring($imgdata);
            $image_data_api[0] = 'http://localhost/web/wc_pro/';
//            echo "<pre>";
//            print_r($image_data_api);
//            die(__FILE__);
            $image_data_api_file['file'] = "data:" . $mime_type['mime'] . ";base64," . $imgdata;
            $image_data_api_file['file'] = $woo_products_image;

            // $image_data_api_file['file'] = 'https://woodemo.cedcommerce.com/woocommerce/goodmarket/wp-content/uploads/2022/09/cap-2.jpg';
            $image_data_api_file['file_name'] = basename($woo_products_image);
            $img_file_wrp['file'] = json_encode($image_data_api_file);
            return ($img_file_wrp);

            return '';
        }catch (\Exception $e)
        {
            echo "<pre>";
            print_r($e->getMessage());
            die(__FILE__);
        }catch (\Error $e)
        {
            echo "<pre>";
            print_r($e->getMessage());
            die(__FILE__);
        }
    }


    /**
     * @param $product
     * @param $attribute
     * @return float|null
     */
    public function getGoodMarketProfilePrice($product,$attribute)
    {
        $price = (float)$product->getData($attribute);
        $configPrice = trim(
            $this->scopeConfig->getvalue(
                'goodmarket/goodmarket_product/product_price'
            )
        );
        switch ($configPrice) {
            case 'plus_fixed':
                $fixedPrice = trim(
                    $this->scopeConfig->getvalue(
                        'goodmarket/goodmarket_product/fix_price'
                    )
                );
                $price = $this->forFixPrice($price, $fixedPrice, 'plus_fixed');
                break;

            case 'plus_per':
                $percentPrice = trim(
                    $this->scopeConfig->getvalue(
                        'goodmarket/goodmarket_product/percentage_price'
                    )
                );
                $price = $this->forPerPrice($price, $percentPrice, 'plus_per');
                break;

            case 'min_fixed':
                $fixedPrice =trim(
                    $this->scopeConfig->getvalue(
                        'goodmarket/goodmarket_product/fix_price'
                    )
                );
                $price = $this->forFixPrice($price, $fixedPrice, 'min_fixed');
                break;

            case 'min_per':
                $percentPrice = trim(
                    $this->scopeConfig->getvalue(
                        'goodmarket/goodmarket_product/percentage_price'
                    )
                );
                $price = $this->forPerPrice($price, $percentPrice, 'min_per');
                break;

            case 'differ':
                $customPriceAttr = trim(
                    $this->scopeConfig->getvalue(
                        'goodmarket/goodmarket_product/different_price'
                    )
                );
                try {
                    $cprice = (float)$product->getData($customPriceAttr);
                } catch (\Exception $e) {
                    $this->getResponse()->setBody($e->getMessage());
                }
                $price = ($cprice != 0) ? $cprice : $price;
                break;

            default:
                $price;
        }
        $price=$this->convertPrice($price,'USD');
        return $price;
    }


    /**
     * @param null        $price
     * @param null        $fixedPrice
     * @param $configPrice
     * @return float|null
     */
    public function forFixPrice($price = null, $fixedPrice = null, $configPrice=null)
    {
        if (is_numeric($fixedPrice) && ($fixedPrice != '')) {
            $fixedPrice = (float)$fixedPrice;
            if ($fixedPrice > 0) {
                $price = $configPrice == 'plus_fixed' ? (float)($price + $fixedPrice)
                    : (float)($price - $fixedPrice);
            }
        }
        return $price;
    }

    /**
     * @param null        $price
     * @param null        $percentPrice
     * @param $configPrice
     * @return float|null
     */
    public function forPerPrice($price = null, $percentPrice = null, $configPrice=null)
    {
        if (is_numeric($percentPrice)) {
            $percentPrice = (float)$percentPrice;
            if ($percentPrice > 0) {
                $price = $configPrice == 'plus_per' ?
                    (float)($price + (($price / 100) * $percentPrice))
                    : (float)($price - (($price / 100) * $percentPrice));
            }
        }
        return $price;
    }

//    /**
//     * @param $prodId
//     * @param $call
//     * @return array|bool|string|void
//     */
//    public function deleteProduct($prodId,$call)
//    {
//        if (!empty($prodId)) {
//            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
//            $collection = $this
//                ->productCollectionFactory
//                ->create()
//                ->setStoreId($this
//                    ->config
//                    ->getStoreId())
//                ->addAttributeToSelect('*')
//                ->addAttributeToFilter('entity_id', ['in' => $prodId])/*->addFieldToFilter('goodmarket_status', '1')*/
//                ->addMediaGalleryData();
//            $count = 0;
//            $productArray = [];
//            $deleteProdResponse=[];
//            if ($collection->getSize() > 0) {
//                /** @var \Magento\Catalog\Model\Product $product */
//                foreach ($collection->getItems() as $products) {
//                    if($product->getType)
//                        $product=$products->getData();
//                    if(!isset($product['goodmarket_product_id'])) {
//                        return "Selected Product with ID ".$products->getId()." is Not Yet Uploaded";
//                    }
//                    if(empty($product['goodmarket_product_id'])) {
//                        return "Selected Product with ID ".$products->getId()." is Not Yet Uploaded";
//                    }
//                    $deleteProdResponse=$this->data->deleteProduct($products,$call);
//                    if($deleteProdResponse==true) {
//                        $deleteProdResponse=true;
//                    } else {
//                        $deleteProdResponse="Something went wrong ,Please check the Feed Section";
//                    }
//                }
//                return $deleteProdResponse;
//            }
//        }
//    }

    /**
     * @param $product
     * @param $request
     * @return bool|string
     */
    public function validateProduct($product,$request,$profile)
    {
        if($profile['profile_status']!=1) {
            return "It Seems Like The Profile is Disabled.Please Enable It,To Use Mass Action ";
        }
        switch ($request) {

            case "UploadProduct":
                if(!isset($product['goodmarket_product_id']) || empty($product['goodmarket_product_id'])) {
                    return true;
                }else {
                    return "Selected Product With SKU ".$product['sku']." Already Been Uploaded";
                }
                break;

            case "EditProduct":
            case "SyncInvProduct":
            case "DeleteProduct":
                if(isset($product['goodmarket_product_id'])) {
                    if(!empty($product['goodmarket_product_id'])) {
                        return true;
                    }else {
                        return "Product with SKU ".$product['sku']." Yet Not Uploaded";
                    }
                }else {
                    return  "Product with SKU ".$product['sku']." Yet Not Uploaded";
                }
                break;
        }
        return "Something went wrong";
    }

    /**
     * @param $product
     * @param $profileData
     * @return int
     */
    public function getQuantityForUpload($product,$profileData)
    {
        $useMSI = $this->config->getUseMsi();
        $quantity = 0;
        if ($product != null) {
            if ($profileData['id'] > 0 && !$useMSI) {
                $reqOptAttr = $profileData['profile_req_opt_attribute'];
                $reqOptAttr = $attributes = json_decode($reqOptAttr, true);
                $attributes = isset($reqOptAttr['optional_attributes']) ? array_column($reqOptAttr['optional_attributes'], 'goodmarket_attribute_name') : [];
                $invIndex = array_search('quantity_and_stock_status', $attributes);
                if (is_array($attributes) && $invIndex
                    && isset($reqOptAttr['optional_attributes'][$invIndex]['magento_attribute_code'])
                    && $reqOptAttr['optional_attributes'][$invIndex]['magento_attribute_code'] != 'quantity_and_stock_status'
                ) {
                    $quantity = ($reqOptAttr['optional_attributes'][$invIndex]['magento_attribute_code'] == "default") ? $reqOptAttr['optional_attributes'][$invIndex]['default'] : $product->getData($reqOptAttr['optional_attributes'][$invIndex]['magento_attribute_code']);
                    $quantity = isset($quantity['qty']) ? (int)$quantity['qty'] : (int)$quantity;
                } else {
                    $stockItem = $this->objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');
                    $stock = $stockItem->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
                    if($stock->getManageStock()) {
                        $quantity = (int)$stock->getQty();
                    }
                }
            } else {
                $stockItem = $this->objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');
                $stock = $stockItem->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
                if($stock->getManageStock()) {
                    $msiQty = $this->getMsiQuantity($product);
                    $quantity = ($useMSI) ? (int)$msiQty : (int)$stock->getQty();
                }
            }
        }
        $quantity = ($quantity < 0) ? 0 : $quantity;
        return (int) $quantity;
    }

    /**
     * @param $product
     * @return false|int|mixed
     */
    public function getMsiQuantity($product) {
        $quantity = false;
        $useMSI = $this->config->getUseMsi();
        if($useMSI) {
            $useSalableQty = $this->config->getUseSalableQty();
            if($useSalableQty) {
                $msiStockName = $this->config->getSalableStockName();
                $getSalableQuantityDataBySku = $this->objectManager->create('Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku');
                $invSourceData = $getSalableQuantityDataBySku->execute($product->getSku());
                if ($invSourceData && is_array($invSourceData) && count($invSourceData) > 0) {
                    $invSourceData = array_column($invSourceData, 'qty', 'stock_name');
                    $quantity = isset($invSourceData[$msiStockName]) ? $invSourceData[$msiStockName] : 0;
                }
            } else {
                $msiSourceCode = $this->config->getMsiSourceCode();
                $msiSourceDataModel = $this->objectManager->create('\Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku');
                $invSourceData = $msiSourceDataModel->execute($product->getSku());
                if ($invSourceData && is_array($invSourceData) && count($invSourceData) > 0) {
                    $invSourceData = array_column($invSourceData, 'quantity', 'source_code');
                    $quantity = isset($invSourceData[$msiSourceCode]) ? $invSourceData[$msiSourceCode] : 0;
                }
            }
        }
        return $quantity;
    }

    public function profileCategory()
    {
        $folderPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('ced/goodmarket');
        if (!file_exists($folderPath)) {
            $this->file->mkdir($folderPath, 0777, true);
        }
        if( file_exists($folderPath.'/categoryLevel-1.json')) {
            return true;
        }
        // fetch category
        try {
            $taxonomy =$this->data->fetchCategories();
            $path = $folderPath . '/categoryLevel.json';
            $file = fopen($path, "w");
            fwrite($file, json_encode($taxonomy['data']['category']['children']));
            fclose($file);
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
            $response['data'] = "<span style='color:green'>Categories Fetched Successfully !!</span>";
            $response['msg'] = "success";
        } catch (\Exception $e) {
            $response['data'] = "<span style='color:red'>Exception : " . $e->getMessage() . "</span>";
            $response['msg'] = "error";
        }

        return true;
    }

    public function convertPrice($itemAmount,$currencyCodeFrom)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceCurrencyFactory = $objectManager->get('Magento\Directory\Model\CurrencyFactory');
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $currencyCodeTo = $storeManager->getStore()->getCurrentCurrency()->getCode();
        $rate = $priceCurrencyFactory->create()->load($currencyCodeTo)->getAnyRate($currencyCodeFrom);
        $itemAmount = $itemAmount * $rate;
        return $itemAmount;
    }
}
