<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txte
 *
 * @category  Ced
 * @package   Ced_EbayMultiAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\GoodMarket\Helper;

use Magento\Framework\FlagManager;

/**
 * Class Data
 * @package Ced\EbayMultiAccount\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const GET_ORDER_SUB_URL = 'orders';
    const GET_ORDER_SHIPMENT_URL = '/shipment';
    const GET_ORDER_CANCELLATION_URL = '/cancellation';
    const POST_PRODUCT_OFFER = 'offers';
    const POST_PRODUCT_UPLOAD = '/product';
    const GET_PRODUCT_VALIDATION_REPORT='/validation-report/';
    const POST_PRODUCT_UPLOAD_CONTENT = 'content';
    const POST_EXPORT_PRODUCT_OFFER = '/export';
    const API_ROOT_URL = 'https://staging.goodmarket.info/graphql';   // Staging
//    const API_ROOT_URL = 'https://www.goodmarket.global/graphql';  // Production
    const API_LOGIN_URL = "https://login.goodmarket.com/";
    const FETCH_TOKEN = "token?grant_type=client_credentials";
    const FLAG_CODE = 'CED_GOODMARKET_SOURCE';
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl                 $curl,
        \Ced\GoodMarket\Helper\Config                $config,
        \Ced\GoodMarket\Helper\Logger                $logger,
        \Ced\GoodMarket\Model\FeedFactory $feed,
        \Ced\GoodMarket\Model\SchedulerFactory $scheduler,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Json\Helper\Data $json,
        \Magento\Catalog\Model\Product $product,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
        FlagManager $flagManager
    )
    {
        $this->logger = $logger;
        $this->_curl = $curl;
        $this->config = $config;
        $this->feed=$feed;
        $this->scopeConfig=$scopeConfig;
        $this->json = $json;
        $this->product=$product;
        $this->stockRegistry=$stockRegistry;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->scheduler=$scheduler;
        $this->vendorId=$this->vendorId();
        $this->hashToken=$this->hashToken();
        $this->flagManager = $flagManager;
        parent::__construct($context);
    }

    public function vendorId()
    {
        $collectData=json_decode($this->scopeConfig->getValue('goodmarket/settings/token'),true);
        if(isset($collectData['vendor_id'])) {
            return $collectData['vendor_id'];
        }else{
            return [];
        }

    }

    public function hashToken()
    {
        $collectData=json_decode($this->scopeConfig->getValue('goodmarket/settings/token'),true);
        if(isset($collectData['hash_token'])) {
            return $collectData['hash_token'];
        }else{
            return [];
        }
    }


    /**
     * @param $url
     * @param $body
     * @param $call
     * @return array|mixed|void
     */
    public function postRequest($url, $body,$call)
    {
        try {
            $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->_curl->setOption(CURLOPT_ENCODING, "");
            $this->_curl->setOption(CURLOPT_MAXREDIRS, "10");
            $this->_curl->setOption(CURLOPT_TIMEOUT, "30");
            $this->_curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $this->_curl->addHeader("Content-Type", 'application/json');
            $this->_curl->post($url, $body);
            $response = $this->_curl->getBody();
            $status=$this->_curl->getStatus();
            if(!empty($call)) {
                $this->feedData($url,$body,$status,$response,$call,'');
            }
            if($call=='UploadProduct' || $call=='EditProduct') {
                $this->schedulerData($response);
            }
            if (!empty($response)) {
                $response = json_decode($response, true);
                return $response;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            echo "<pre>";
            print_r($e->getMessage());
            die(__METHOD__);
            $this->logger->addError($e->getMessage(), ['path' => __METHOD__]);
        } catch (\Error $e) {
            echo "<pre>";
            print_r($e->getMessage());
            die(__METHOD__);
            $this->logger->addError($e->getMessage(), ['path' => __METHOD__]);
        }
    }

    /**
     * @param $productData
     * @return void
     */
    public function createConfigurableProduct($productData,$product,$type)
    {
        $postfield['query']='mutation saveProduct($vendor_id: Int!,$product_data: String!, $hash_token: String) {saveProduct(vendor_id: $vendor_id, product_data:$product_data , hash_token:$hash_token){success message product_id child_product { product_id sku }}}';
        $postfield['variables']['product_data']=json_encode($productData);
        $postfield['variables']['vendor_id']=$this->vendorId;
        $postfield['variables']['hash_token']=$this->hashToken;
        $productUpload = $this->postRequest(self::API_ROOT_URL, json_encode($postfield),$type);
        if(isset($productUpload['data']['saveProduct'])) {
            if($productUpload['data']['saveProduct']['success']=='1') {
                if($type=='EditProduct') {
                    $product->setData('goodmarket_product_status', 'Uploaded');
                    $product->setData('goodmarket_product_error','["valid"]');
                    $product->save();
                    return $productUpload;
                }
                if(isset($productUpload['data']['saveProduct']['child_product'])) {
                    foreach ($productUpload['data']['saveProduct']['child_product'] as $varients) {
                        $productData=$this->product->loadByAttribute('sku',$varients['sku']);
                        $productData->setData('goodmarket_product_error','');
                        $productData->setData('goodmarket_product_id',$varients['product_id']);
                        $productData->save();
                    }
                }
                $product->setData('goodmarket_product_status', 'Uploaded');
                $product->setData('goodmarket_product_error','["valid"]');
                $product->setData('goodmarket_product_id',$productUpload['data']['saveProduct']['product_id']);
                $product->save();
                return $productUpload;
            } else {
                if($type=='EditProduct') {
                    $product->setData('goodmarket_product_error',$productUpload['data']['saveProduct']['message']);
                    $product->save();
                    return $productUpload;
                }
                $product->setData('goodmarket_product_status', 'Not-Uploaded');
                $product->setData('goodmarket_product_id','');
                $product->setData('goodmarket_product_error',$productUpload['data']['saveProduct']['message']);
                $product->save();
                return $productUpload;
            }
        }
    }

    public function bulkProductUpload($productData,$type)
    {
        $postfield['query']='mutation saveBulkProduct($vendor_id: Int!,$product_data: String!, $hash_token: String) {
    saveBulkProduct(vendor_id: $vendor_id, product_data:$product_data , hash_token:$hash_token){
        success
        message
         job_id
    }
}';
        $postfield['variables']['product_data']=json_encode($productData);
        $postfield['variables']['vendor_id']=$this->vendorId;
        $postfield['variables']['hash_token']=$this->hashToken;
      //  $postfield= json_decode($postfield['variables']['product_data']['0'],true);
//        echo "<pre>";
//        print_r($postfield);
//        die(__FILE__);
        $productUpload = $this->postRequest(self::API_ROOT_URL, json_encode($postfield),$type);
        return $productUpload;
    }

    public function bulkProductResponse($schedulerId)
    {
        $postfield['query']='query pendingBulkresponse($job_id: String!) {
			    pendingBulkresponse(job_id: $job_id) {
			        job_id
			        success
			        job_status
			        error_result{product_sku
			            message
			        }
			        product_ids{product_sku
			            product_id
			        }
			    }
			}';
        $postfield['variables']['job_id']=$schedulerId;
        $postfield['variables']['vendor_id']=$this->vendorId;
        $postfield['variables']['hash_token']=$this->hashToken;
        $productUpload = $this->postRequest(self::API_ROOT_URL, json_encode($postfield),'');
        return $productUpload;
    }
    /**
     * @param $productData
     * @return void
     */
    public function createProduct($productData,$product,$type)
    {
        echo '<pre>'; print_r($productData); exit;
        $postfield['query']='mutation saveProduct($vendor_id: Int!,$product_data: String!, $hash_token: String) {saveProduct(vendor_id: $vendor_id, product_data:$product_data , hash_token:$hash_token){success message product_id}}';
        $postfield['variables']['product_data']=json_encode($productData);
        $postfield['variables']['vendor_id']=$this->vendorId;
        $postfield['variables']['hash_token']=$this->hashToken;
        $productUpload = $this->postRequest(self::API_ROOT_URL, json_encode($postfield),$type);
        if(isset($productUpload['data']['saveProduct'])) {
            if($productUpload['data']['saveProduct']['success']=='1') {
                if($type=='EditProduct') {
                    $product->setData('goodmarket_product_status', 'Uploaded');
                    $product->setData('goodmarket_product_error','["valid"]');
                    $product->save();
                    return $productUpload;
                }
                $product->setData('goodmarket_product_status', 'Uploaded');
                $product->setData('goodmarket_product_error','["valid"]');
                $product->setData('goodmarket_product_id',$productUpload['data']['saveProduct']['product_id']);
                $product->save();
                return $productUpload;
            } else {
                if($type=='EditProduct') {
                    $product->setData('goodmarket_product_error',$productUpload['data']['saveProduct']['message']);
                    $product->save();
                    return $productUpload;
                }
                $message=$this->returnErrorMessage($productUpload['data']['saveProduct']['message']);
                $productUpload['data']['saveProduct']['message']=$message;
                $product->setData('goodmarket_product_status', 'Not-Uploaded');
                $product->setData('goodmarket_product_id','');
                $product->setData('goodmarket_product_error',$productUpload['data']['saveProduct']['message']);
                $product->save();
                return $productUpload;
            }
        }
    }

    /**
     * @param $product
     * @param $qty
     * @param $type
     * @return string[]|void
     */
    public function getProductInventorySync($product,$qty,$price,$type,$allSources,$category)
    {
        $postfield['query']='mutation saveProduct($vendor_id: Int!,$product_data: String!, $hash_token: String) {saveProduct(vendor_id: $vendor_id, product_data:$product_data , hash_token:$hash_token){success message product_id}}';
        $productData['id']=$product['goodmarket_product_id'];
        $quantity['sources']=json_encode($allSources);
        $quantity['product_has_weight']='1';
        $quantity['price']=$price;
        $quantity['sku']=$product['sku'];
        $quantity['category_ids'] = [$category];
        $productData['product']=json_encode($quantity);
        $postfield['variables']['product_data']=json_encode($productData);
        $postfield['variables']['vendor_id']=$this->vendorId;
        $postfield['variables']['hash_token']=$this->hashToken;
        $productUpload = $this->postRequest(self::API_ROOT_URL, json_encode($postfield),$type);
        if(isset($productUpload['data']['saveProduct'])) {
            if($productUpload['data']['saveProduct']['success']=='1') {
                return ["success"=>"1","message"=>"Product Inventory Synced for Product SKU ".$product['sku']];
            }else{
                return ["success"=>"0","message"=>"Product Inventory Not Synced for Product SKU ".$product['sku']];
            }
        }
    }

    /**
     * @param $username
     * @param $password
     * @return array|mixed|void
     */
    public function getAuthorisation($username,$vendor)
    {
        try {
            $url = self::API_ROOT_URL;
            $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->_curl->setOption(CURLOPT_ENCODING, "");
            $this->_curl->setOption(CURLOPT_MAXREDIRS, "10");
            $this->_curl->setOption(CURLOPT_TIMEOUT, "30");
            $this->_curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $this->_curl->addHeader("Content-Type", 'application/json');
            $postfield='{"query":"mutation login($email: String!, $password: String!,$vendorId: Int!) {\\n    generateVendorToken(email: $email, password: $password,vendorId: $vendorId){\\n        token\\n        name\\n        shop_url\\n        vendor_id\\n        customer_id\\n        approval_required\\n        status\\n        success\\n        message\\n        vendor_panel_logo\\n       profile_picture\\n        hash_token\\n    }\\n}\\n","variables":{"email":"'.$username.'","password":"FB6B7D12F4F6EB415D71818C484F3","vendorId":"'.$vendor.'"}}';    // Staging

            // $postfield='{"query":"mutation login($email: String!, $password: String!,$vendorId: Int!) {\\n    generateVendorToken(email: $email, password: $password,vendorId: $vendorId){\\n        token\\n        name\\n        shop_url\\n        vendor_id\\n        customer_id\\n        approval_required\\n        status\\n        success\\n        message\\n        vendor_panel_logo\\n       profile_picture\\n        hash_token\\n    }\\n}\\n","variables":{"email":"'.$username.'","password":"78B12AC3F27959C42CBE26DAD7DAD","vendorId":"'.$vendor.'"}}'; // Production

            $this->_curl->post($url, $postfield);
            $response = $this->_curl->getBody();
            if (!empty($response)) {
                $access_token = json_decode($response, true);
                $location_post_data['query'] = 'query getSources($vendor_id: Int!, $hash_token: String!) {
			    getSources(vendor_id: $vendor_id, hash_token: $hash_token){
			       success
			       sources {
			            source_code
			            name
			            description
			            latitude
			            longitude
			            country_id
			            region_id
			            region
			            city
			            street
			            postcode
			            contact_name
			            email
			            phone
			            fax
			        }
			    }
			}';
                $location_post_data['variables']['vendor_id'] = $access_token['data']['generateVendorToken']['vendor_id'];
                $location_post_data['variables']['hash_token'] = $access_token['data']['generateVendorToken']['hash_token'];;
                $locationResponse=$this->postRequest(self::API_ROOT_URL, json_encode($location_post_data),'');
//                echo "<pre>";
//                print_r($locationResponse);
//                die(__FILE__);
                $this->flagManager->saveFlag(self::FLAG_CODE, json_encode($locationResponse['data']['getSources']['sources']));
                return $access_token['data']['generateVendorToken'];
            } else {
                return [];
            }
        } catch (\Exception $e) {
//            echo "<pre>";
//            print_r($e->getMessage());
//            die(__FILE__);
            $this->logger->addError($e->getMessage(), ['path' => __METHOD__]);
        } catch (\Error $e) {
//            echo "<pre>";
//            print_r($e->getMessage());
//            die(__FILE__);
            $this->logger->addError($e->getMessage(), ['path' => __METHOD__]);
        }
    }


    /**
     * @param $startDate
     * @param $endDate
     * @return array|mixed|null
     */
    public function getOrderIDs($startDate, $endDate)
    {
        $endDate = date("Y-m-d", strtotime("+1 day"));
        $postfield='{"query":"query vendorOrders($vendor_id: Int!, $page_setting: ordersListPageSettingInput!, $filter: ordersListFilterInput! , $hash_token : String ) {\n    vendorOrdersList(vendor_id: $vendor_id, page_setting: $page_setting, filter: $filter , hash_token:$hash_token){\n        success\n        count\n        vendor_orders {\n            increment_id\n            order_id\n            created_at\n            billing_name\n            order_total\n            shop_commission_fee\n            net_vendor_earn\n            payment_state\n            order_payment_state\n        }\n    }\n}\n","variables":{"filter":{"from_purchased_date":"'.$startDate.'","to_purchased_date":"'.$endDate.'","vendor_payment_state":"0"},"page_setting":{"count":"10","activePage":1},"vendor_id":'.$this->vendorId.',"hash_token":"'.$this->hashToken.'"}}';
        /*GetOrder*/
        $orderData = $this->postRequest(self::API_ROOT_URL,$postfield,'');
        return $orderData;

    }

    /**
     * @param $orderId
     * @return mixed|string
     */
    public function getOrdersById($orderId)
    {
        $postfield='{"query":"query getOrderData($vendor_id: Int! , $vorder_id: Int!,$hash_token:String) {\n    getOrderDetails(vendor_id: $vendor_id, vorder_id: $vorder_id, hash_token:$hash_token) {\n        order_data\n        success\n    }\n}\n","variables":{"vendor_id":'. $this->vendorId.',"vorder_id":'.$orderId.',"hash_token":"'.$this->hashToken.'"}}';
        /* GetOrderByID*/
        $orderData = $this->postRequest(self::API_ROOT_URL,$postfield,'');
        $orderData=isset($orderData['data']['getOrderDetails']['order_data'])?json_decode($orderData['data']['getOrderDetails']['order_data'],true):'';
        return $orderData;

    }

    /**
     * @param $invoiceOrder
     * @return bool
     */
    public function createOrderInvoice($invoiceOrder)
    {
        $itemArray=$invoiceOrder['items_ordered']['rows'];
        $vendorOrderID=$invoiceOrder['vorder_id'];
        foreach ($itemArray as $itemDetails) {
            $itemID=$itemDetails['item_id'];
            $itemQty=$itemDetails['qty_ordered'];
        }
        $postfield='{"query":"mutation createInvoice($vorder_id: Int!, \n                    $items: String!,\n                    $comment_text:String,\n                    $comment_customer_notify:Int,\n                    $is_visible_on_front:Int,\n                    $send_email:Int,\n                    $do_shipment: Int,\n                    $vendor_id: Int!,\n                    $hash_token : String) {\n    createInvoice(vorder_id: $vorder_id, \n                items: $items,\n                comment_text:$comment_text,\n                comment_customer_notify:$comment_customer_notify,\n                is_visible_on_front:$is_visible_on_front,\n                send_email:$send_email,\n                do_shipment:$do_shipment,\n                vendor_id:$vendor_id,\n                hash_token:$hash_token) {\n        message\n        success\n    }\n}\n  ","variables":{"comment_customer_notify":true,"comment_text":"Order Invoice","items":"{\"'.$itemID.'\":{\"qty\":\"'.$itemQty.'\"}}","send_email":true,"vorder_id":"'.$vendorOrderID.'","vendor_id":'.$this->vendorId.',"hash_token":"'.$this->hashToken.'"}}';
        $invoiceResponse = $this->postRequest(self::API_ROOT_URL,$postfield,'CreateOrderInvoice');
        if(isset($invoiceResponse['data']['createInvoice'])) {
            if($invoiceResponse['data']['createInvoice']['success']=='1') {
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    /**
     * @param $shipmentData
     * @return bool
     */
    public function createOrderShipment($shipmentData)
    {
        $itemArray=$shipmentData['items_ordered']['rows'];
        $vendorOrderID=$shipmentData['vorder_id'];
        foreach ($itemArray as $itemDetails) {
            $itemID=$itemDetails['item_id'];
            $itemQty=$itemDetails['qty_ordered'];
        }
        $postfield='{"query":"mutation createShipment($vorder_id: Int!,\n                    $items: String!,\n                    $comment_text:String,\n                    $comment_customer_notify:Int,\n                    $is_visible_on_front:Int,\n                    $send_email:Int,\n                    $vendor_id: Int!,\n                    $hash_token : String) {\n    createShipment(vorder_id: $vorder_id,\n                items: $items,\n                comment_text:$comment_text,\n                comment_customer_notify:$comment_customer_notify,\n                is_visible_on_front:$is_visible_on_front,\n                send_email:$send_email,\n                vendor_id:$vendor_id,\n                hash_token:$hash_token) {\n        message\n        success\n    }\n}\n","variables":{"comment_customer_notify":true,"comment_text":"check shipment","items":"[{\"item_id\":\"'.$itemID.'\",\"qty\":\"'.$itemQty.'\"}]","send_email":true,"vorder_id":"'.$vendorOrderID.'","vendor_id":'.$this->vendorId.',"hash_token":"'.$this->hashToken.'"}}';
        $shipmentResponse = $this->postRequest(self::API_ROOT_URL, $postfield,'CreateOrderShipment');
        if(isset($shipmentResponse['data']['createShipment'])) {
            if($shipmentResponse['data']['createShipment']['success']=='1') {
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    /**
     * @param $shipmentData
     * @param $trackArray
     * @return bool
     */
    public function createTrackOrderShipment($shipmentData,$trackArray)
    {
        $itemArray=$shipmentData['items_ordered']['rows'];
        $vendorOrderID=$shipmentData['vorder_id'];
        foreach ($itemArray as $itemDetails) {
            $order_ship['item_id'] =  $itemDetails['item_id'];
            $order_ship['qty']     = $itemDetails['qty_ordered'];
            $order_ship_invocie[]  = $order_ship;
        }
        $postfield['query']='mutation createShipment($vorder_id: Int!,
				$source_code: String!,
				$items: String!,
				$tracking: String!,
				$comment_text:String,
				$comment_customer_notify:Int,
				$is_visible_on_front:Int,
				$send_email:Int,
				$vendor_id: Int!,
				$hash_token : String) {
					createShipment(vorder_id: $vorder_id,
					source_code: $source_code,
					items: $items,
					tracking: $tracking,
					comment_text:$comment_text,
					comment_customer_notify:$comment_customer_notify,
					is_visible_on_front:$is_visible_on_front,
					send_email:$send_email,
					vendor_id:$vendor_id,
					hash_token:$hash_token) {
						message
						success
					}
				}';
        $postfield['variables']['comment_customer_notify'] = true;
        $postfield['variables']['comment_text'] = 'check shipment';
        $postfield['variables']['items'] = json_encode($order_ship_invocie);
        $track_array['number'] = $trackArray['track_number'];
        $track_array['title'] = $trackArray['title'];
        $track_array['carrier_code'] = $trackArray['carrier_code'];
        $track_to_send[] = $track_array;
        $postfield['variables']['tracking']=json_encode($track_to_send);
        $location_saved_data=json_decode($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'),true);
        $postfield['variables']['source_code'] = $location_saved_data[0]['source_code'];
        $postfield['variables']['send_email'] = true;
        $postfield['variables']['vorder_id'] = $vendorOrderID;
        $postfield['variables']['vendor_id']=$this->vendorId;
        $postfield['variables']['hash_token']=$this->hashToken;
        // $postfield='{"query":"mutation createShipment($vorder_id: Int!,\n\t\t\t\t$items: String!,\n\t\t\t\t$tracking: String!,\n\t\t\t\t$comment_text:String,\n\t\t\t\t$comment_customer_notify:Int,\n\t\t\t\t$is_visible_on_front:Int,\n\t\t\t\t$send_email:Int,\n\t\t\t\t$vendor_id: Int!,\n\t\t\t\t$hash_token : String) {\n\t\t\t\t\tcreateShipment(vorder_id: $vorder_id,\n\t\t\t\t\titems: $items,\n\t\t\t\t\ttracking: $tracking,\n\t\t\t\t\tcomment_text:$comment_text,\n\t\t\t\t\tcomment_customer_notify:$comment_customer_notify,\n\t\t\t\t\tis_visible_on_front:$is_visible_on_front,\n\t\t\t\t\tsend_email:$send_email,\n\t\t\t\t\tvendor_id:$vendor_id,\n\t\t\t\t\thash_token:$hash_token) {\n\t\t\t\t\t\tmessage\n\t\t\t\t\t\tsuccess\n\t\t\t\t\t}\n\t\t\t\t}","variables":{"comment_customer_notify":true,"comment_text":"test","items":"[{\"item_id\":\"'.$itemID.'\",\"qty\":'.$itemQty.'}]","tracking":"[{\"number\":\"'.$trackArray['track_number'].'\",\"title\":\"'.$trackArray['title'].'\",\"carrier_code\":\"'.$trackArray['carrier_code'].'\"}]","send_email":true,"vorder_id":'.$vendorOrderID.',"vendor_id":'.$this->vendorId.',"hash_token":"'.$this->hashToken.'"}}';
        $shipmentResponse = $this->postRequest(self::API_ROOT_URL, json_encode($postfield),'CreateOrderTrackShipment');
        if(isset($shipmentResponse['data']['createShipment'])) {
            if($shipmentResponse['data']['createShipment']['success']=='1') {
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    /**
     * @return array|mixed|null
     */
    public function fetchCategories()
    {
        $postfield='{"query":"query category($id: Int!) {\\n    category(id: $id) {\\n        products {\\n            total_count\\n            page_info {\\n                current_page\\n                page_size\\n            }\\n        }\\n        children_count\\n            children {\\n                id\\n                level\\n                name\\n                path\\n                children {\\n                    id\\n                    level\\n                    name\\n                    path\\n                    children {\\n                        id\\n                        level\\n                        name\\n                        path\\n                        children {\\n                            id\\n                            level\\n                            name\\n                            path\\n                            children {\\n                                id\\n                                level\\n                                name\\n                                path\\n                            }\\n                        }\\n                    }\\n                }\\n            }\\n        }\\n    }","variables":{"id":2}}';
        /*FetchCategories*/
        $fetchCategories = $this->postRequest(self::API_ROOT_URL, $postfield,'');
        return $fetchCategories;

    }

    /**
     * @param $categoryId
     * @return array|mixed
     */
    public function getCategoryAttributes($categoryId)
    {
        $postfield='{"query":"query getProductFormAttributes($category_id: Int!, $product_type: String!, $product_id: Int, $vendor_id: Int!, $hash_token:String) {\\n    productAllowedAttributes(category_id: $category_id, product_type: $product_type, product_id:$product_id ,vendor_id:$vendor_id, hash_token : $hash_token) {\\n        groupwise_attributes\\n        configurable_attributes {\\n            label\\n            title\\n            attribute_code\\n            name\\n            class\\n            value\\n            options\\n        }\\n        configurable_variants {\\n            name\\n            sku\\n            qty\\n            price\\n            weight\\n            attributes\\n            image\\n        }\\n        success\\n        message\\n    }\\n}\\n","variables":{"category_id":'.$categoryId.',"product_id":0,"product_type":"configurable","vendor_id":'.$this->vendorId.',"hash_token":"'.$this->hashToken.'"}}';
        /* GetCategoryAttribute*/
        $categoryAttribute = $this->postRequest(self::API_ROOT_URL, $postfield,'');
        if(isset($categoryAttribute['data']['productAllowedAttributes'])) {
            if($categoryAttribute['data']['productAllowedAttributes']['success']=='1') {
                return $categoryAttribute['data']['productAllowedAttributes'];
            }else{
                return [];
            }
        }
        return [];
    }

    /**
     * @param $categoryId
     * @return array|mixed
     */
    public function getConfigurableAttributes($categoryId)
    {
        $postfield='{"query":"query getProductFormAttributes($category_id: Int!, $product_type: String!, $product_id: Int, $vendor_id: Int!, $hash_token:String) {\\n    productAllowedAttributes(category_id: $category_id, product_type: $product_type, product_id:$product_id ,vendor_id:$vendor_id, hash_token : $hash_token) {\\n        groupwise_attributes\\n        configurable_attributes {\\n            label\\n            title\\n            attribute_code\\n            name\\n            class\\n            value\\n            options\\n        }\\n        configurable_variants {\\n            name\\n            sku\\n            qty\\n            price\\n            weight\\n            attributes\\n            image\\n        }\\n        success\\n        message\\n    }\\n}\\n","variables":{"category_id":'.$categoryId.',"product_id":0,"product_type":"configurable","vendor_id":'.$this->vendorId.',"hash_token":"'.$this->hashToken.'"}}';
        /* GetCategoryAttribute*/
        $categoryAttribute = $this->postRequest(self::API_ROOT_URL, $postfield,'');
        if(isset($categoryAttribute['data']['productAllowedAttributes'])) {
            if($categoryAttribute['data']['productAllowedAttributes']['success']=='1') {
                return $categoryAttribute['data']['productAllowedAttributes']['configurable_attributes'];
            }else{
                return [];
            }
        }
        return [];
    }

    /**
     * @param $product
     * @param $productData
     * @param $call
     * @return false|string[]
     */
    public function deleteProduct($product,$productData,$call)
    {
        $postfield['query']='mutation massDelete($vendor_id: Int!, $products: String!, $hash_token: String) { productMassDelete(vendor_id: $vendor_id, products: $products, hash_token: $hash_token) { count } }';
        $postfield['variables']['products']=json_encode($productData);
        $postfield['variables']['vendor_id']=$this->vendorId;
        $postfield['variables']['hash_token']=$this->hashToken;
        $categoryAttribute = $this->postRequest(self::API_ROOT_URL, json_encode($postfield),'DeleteProduct');
        if(isset($categoryAttribute['data']['productMassDelete'])) {
            if($categoryAttribute['data']['productMassDelete']['count']=='0') {
                return ["success"=>"0","message"=>"Product with SKU ".$product->getSku() ." has not been deleted"];
            }else{
                $product->setData('goodmarket_product_status', 'Not-Uploaded');
                $product->setData('goodmarket_product_error','["valid"]');
                $product->setData('goodmarket_product_id','');
                $product->save();
                return ["success"=>"1","message"=>"Product with SKU ".$product->getSku() ." has been deleted"];
            }
        }
        return false;
    }
    /**
     * @param $url
     * @param $body
     * @param $status
     * @param $response
     * @param $call
     * @param $productId
     * @return void
     */
    public function feedData($url,$body,$status,$response,$call,$productId)
    {
        $feedData['call']=$call;
        $feedData['endpoint']=$url;
        $feedData['parameter']=$body;
        $feedData['status_code']=$status;
        $feedData['response']=$response;
        $feedData['product_ids']=$productId;
        $responseEntry = $this->feed->create();
        $responseEntry->addData($feedData);
        $responseEntry->save();

    }

    public function schedulerData($response)
    {
        $schedulerData=json_decode($response,true);
        $scheduler=[];
        if(isset($schedulerData['data']['saveBulkProduct']['job_id']) && !empty($schedulerData['data']['saveBulkProduct']['job_id']))
        {
            $scheduler['scheduler_id']=$schedulerData['data']['saveBulkProduct']['job_id'];
            $scheduler['scheduler_status']=$schedulerData['data']['saveBulkProduct']['message'];
            $scheduler['scheduler_product_sync']='Pending';
        }
        $responseEntry = $this->scheduler->create();
        $responseEntry->addData($scheduler);
        $responseEntry->save();

    }
    /**
     * @param $path
     * @return array
     */
    public function loadFile($path)
    {
        $data = [];
        if (file_exists($path)) {
            $pathInfo = pathinfo($path);
            if ($pathInfo['extension'] == 'json') {
                $myfile = fopen($path, "r");
                $data = fread($myfile, filesize($path));
                fclose($myfile);
                $data = $this->json->jsonDecode($data);
            }
        }
        return $data;
    }

    /**
     * @return bool|string
     */
    public function checkAccountSetup()
    {
        $hash_token=$this->scopeConfig->getValue('goodmarket/settings/token');
        $vendor_id=$this->scopeConfig->getValue('goodmarket/settings/vendor_id');
        $email=$this->scopeConfig->getValue('goodmarket/settings/username');
//        $password=$this->scopeConfig->getValue('goodmarket/settings/password');
        $status=$this->scopeConfig->getValue('goodmarket/settings/enable');

        if($status=='1') {
            if(!empty($email) && !empty(trim($hash_token)) && !empty(trim($vendor_id))) {
                if(!empty($this->flagManager->getFlagData('CED_GOODMARKET_SOURCE'))) {
                    return true;
                }else{
                    return "No Source Found for the Vendor";
                }
            }else {
                return "Please Check the Login Credentials in the Configuration Section!!";
            }
        } else {
            return "Please Enable the GoodMarket Extension From Configuration Section!!";
        }
    }

    public function returnErrorMessage($response)
    {
        if(trim($response)=='URL key for specified store already exists.')
        {
            return "The Product Already Exist with Similar Sku and Name on the Marketplace";
        }else{
            return $response;
        }

    }

}
