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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright ? 2018 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Helper;

use Magento\Framework\App\ResourceConnection;
use Magento\Tax\Model\TaxClass\Source\Product as ProductTaxClassSource;

/**
 * Class Order Helper
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const ERROR_OUT_OF_STOCK = "'%s' SKU out of stock";
    public const ERROR_NOT_ENABLED = "'%s' SKU not enabled on store '%s'";
    public const ERROR_DOES_NOT_EXISTS = "'%s' SKU not exists on store '%s'";
    public const ERROR_ITEM_DATA_NOT_AVAILABLE = "'%s' SKU not available in order items '%s'";

    /**
     * Order Constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\DataObjectFactory $dataFactory
     * @param \Magento\Framework\Notification\NotifierInterface $notifier
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Model\Service\OrderService $orderService
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoaderFactory $creditmemoLoaderFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Ced\Integrator\Model\MailFactory $mailFactory
     * @param \Ced\GoodMarket\Model\OrderFactory $order
     * @param Config $config
     * @param Data $sdk
     * @param Logger $logger
     * @param \Magento\AdminNotification\Model\Inbox $inbox
     * @param \Magento\Directory\Model\RegionFactory $region
     * @param \Magento\Catalog\Model\Session $session
     * @param ProductTaxClassSource $productTaxClassSource
     * @param \Magento\Tax\Api\Data\TaxClassInterfaceFactory $taxClassDataObjectFactory
     * @param \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassService
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\DataObjectFactory $dataFactory,
        \Magento\Framework\Notification\NotifierInterface $notifier,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoaderFactory $creditmemoLoaderFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Ced\Integrator\Model\MailFactory $mailFactory,
        \Ced\GoodMarket\Model\OrderFactory $order,
        \Ced\GoodMarket\Helper\Config $config,
        \Ced\GoodMarket\Helper\Data $sdk,
        \Ced\GoodMarket\Helper\Logger $logger,
        \Magento\AdminNotification\Model\Inbox $inbox,
        \Magento\Directory\Model\RegionFactory $region,
        \Magento\Catalog\Model\Session $session,
        ProductTaxClassSource $productTaxClassSource,
        \Magento\Tax\Api\Data\TaxClassInterfaceFactory $taxClassDataObjectFactory,
        \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassService,
        ResourceConnection $resourceConnection,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
        parent::__construct($context);
        $this->dataFactory = $dataFactory;
        $this->notifier = $notifier;
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->transactionFactory = $transactionFactory;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->inbox = $inbox;
        $this->orderService = $orderService;
        $this->invoiceService = $invoiceService;
        $this->creditmemoLoaderFactory = $creditmemoLoaderFactory;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;

        $this->productRepository = $productRepository;
        $this->productFactory = $product;
        $this->stockRegistry = $stockRegistry;
        $this->orderFactory = $order;
        $this->mailFactory = $mailFactory;
        $this->config = $config;
        $this->sdk = $sdk;
        $this->logger = $logger;
        $this->region = $region;
        $this->session = $session;
        $this->currencyFactory = $currencyFactory;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->taxClassDataObjectFactory = $taxClassDataObjectFactory;
        $this->taxClassRepository = $taxClassService;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * import orders
     *
     * @return false|int|mixed|void
     */
    public function import()
    {
        $orderD = [];
        $days = null;
        $this->result = 0;
        $storeId = $store = $this->scopeConfig->getvalue(
            'goodmarket/settings/goodmarket_store'
        );
        /*$this
              ->config
              ->getStoreId();*/
        $websiteId = $this
            ->storeManager
            ->getStore($storeId)->getWebsiteId();
        /** @var \Magento\Store\Model\Store $store */

        $store = $this
            ->storeManager
            ->getStore($storeId);
        $store->setCurrencyCode($store->getDefaultCurrencyCode());
//        echo '<pre>';print_r($store->getDefaultCurrencyCode()); exit;

        $order_Days ='60';// $this->config->getOrderImport();
        $startDate = date('Y-m-d', strtotime('-' . $order_Days . ' days'));
        $endDate=date('Y-m-d', strtotime(' +1 day'));
        $response = $this->sdk->getOrderIDs($startDate, $endDate);
        $count = 0;
        if (isset($response['data']['vendorOrdersList']['vendor_orders']) && !empty(isset($response['data']['vendorOrdersList']['vendor_orders']))) {
            foreach ($response['data']['vendorOrdersList']['vendor_orders'] as $orders) {
                $orderData = $this->sdk->getOrdersById($orders['order_id']);
                $orderData['vorder_id']=$orders['order_id'];
                $goodmarketOrderId = $orderData['order_increment_id'];
                $goodmarketOrder = $this
                    ->orderFactory
                    ->create()
                    ->load($goodmarketOrderId, 'goodmarket_order_id');
                if (!$goodmarketOrder || !$goodmarketOrder->getId()) {
                    $customer = $this->getCustomer($orderData, $websiteId);
//                $customer=true;
                    if ($customer !== false) {
                        $count = $this->quote($store, $customer, $orderData, $count);
                    } else {
                        continue;
                    }
                }
            }
        }
        if ($count > 0) {
            $this->notificationSuccess($count);
            return $count;
        }
    }

    /**
     * notificationSuccess
     *
     * @param $count
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function notificationSuccess($count)
    {
        $model = $this->inbox;
        $date = date("Y-m-d H:i:s");
        $model->setData('severity', 4);
        $model->setData('date_added', $date);
        $model->setData('title', "New GoodMarket Orders");
        $model->setData('description', "Congratulation! You have received " . $count . " new orders from GoodMarket");
        $model->setData('url', "#");
        $model->setData('is_read', 0);
        $model->setData('is_remove', 0);
        $model->getResource()
            ->save($model);
    }

    /**
     * quote
     *
     * @param $store
     * @param $customer
     * @param $order
     * @param $count
     * @return false|int|mixed
     */
    public function quote($store, $customer, $order, $count)
    {
        $goodmarketOrderId = '';
        $reason = [];
        try {
            $goodmarketOrderId = $this->getValue('order_increment_id', $order, '');
            $items = $this->getValue('items_ordered', $order, []);
            $items=$items['rows'];

//            Customisation
            $taxClassess = $this->productTaxClassSource->getAllOptions();
            $flag = 1;
            foreach ($taxClassess as $taxClass1) {
                if ($taxClass1['label'] == 'Good-Market Tax') {
                    $taxClassId = $taxClass1['value'];
                    $flag = 1;
                    break;
                } else {
                    $flag = 0;
                }
            }
            if ($flag == 0) {
                try {
                    $connection = $this->resourceConnection->getConnection();
                    // $table is table name
                    $table = $connection->getTableName('tax_class');
                    $query = "INSERT INTO `" . $table . "`(`class_name`, `class_type`) VALUES ('Good-Market Tax','PRODUCT')";
                    $connection->query($query);
                } catch (Exception $e) {
                }
                foreach ($taxClassess as $taxClass1) {
                    if ($taxClass1['label'] == 'Good-Market Tax') {
                        $taxClassId = $taxClass1['value'];
                        break;
                    }
                }
            }

//            Create tax rates
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//            $taxCalculationRate1 = $objectManager->create('\Magento\Tax\Model\Calculation\Rate')->getCollection();//->loadByCode('CODE');
            $taxCalculationRate1 = $objectManager->create('\Magento\Tax\Model\Calculation\Rate')->loadByCode('goodmarket');
//            echo '<pre>'; print_r($taxCalculationRate1->getData()); exit;
            if (empty($taxCalculationRate1->getData())) {
                $taxCalculationRate1->setCode("goodmarket");
                $taxCalculationRate1->setTaxCountryId("IN");
                $taxCalculationRate1->setTaxRegionId("REGION");
                $taxCalculationRate1->setZipIsRange("0");
                $taxCalculationRate1->setTaxPostcode("*");
                $taxCalculationRate1->setRate("2.5");
                $taxCalculationRate1->save();

                $fixtureTaxRule1 = $objectManager->create('\Magento\Tax\Model\Calculation\Rule');//->loadByCode(1);
//                echo '<pre>'; print_R($fixtureTaxRule1->getData()); exit;
                $fixtureTaxRule1->setCode("goodmarket");
                $fixtureTaxRule1->setPriority(0);
                $fixtureTaxRule1->setCustomerTaxClassIds([3]);
                $fixtureTaxRule1->setProductTaxClassIds([$taxClassId]);
                $fixtureTaxRule1->setTaxRateIds([$taxCalculationRate1->getId()]);
                $fixtureTaxRule1->save();
            }

//            Customisation End

            /** @var int $cartId */
            $cartId = $this
                ->cartManagementInterface
                ->createEmptyCart();
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this
                ->cartRepositoryInterface
                ->get($cartId);
            $quote->setStore($store);

            $quote->setCurrency();

            $quote->setBaseCurrencyCode($store->getDefaultCurrencyCode());
            $quote->setGlobalCurrencyCode($store->getDefaultCurrencyCode());
            $quote->setQuoteCurrencyCode($store->getDefaultCurrencyCode());
            $quote->setStoreCurrencyCode($store->getDefaultCurrencyCode());

            // make change here.

            $customer = $this
                ->customerRepository
                ->getById($customer->getId());
            $quote->assignCustomer($customer);
            $itemAccepted = 0;
            foreach ($items as $index => $item) {
                $orderItemData=$item;
                if (isset($item)) {
                    $sku = $item['sku'];
                    $qty = $item['qty_ordered'];
                    $product = $this
                        ->productFactory
                        ->create()
                        ->loadByAttribute('sku', $sku);
                    if (isset($product) && !empty($product)) {
                        /** @var \Magento\Catalog\Model\Product $product */
                        $product = $this
                            ->productFactory
                            ->create()
                            ->load($product->getEntityId());

                        if ($product->getStatus() == '1') {
                            $sku = $product->getSku();
                            /* Get stock item */
                            $stock = $this
                                ->stockRegistry
                                ->getStockItem($product->getId(), $product->getStore()
                                    ->getWebsiteId());
                            $stockStatus = ($stock->getQty() > 0) ? ($stock->getIsInStock() == '1' ? ($stock->getQty() >= $qty ? true : false) : false) : false;
                            if ($stockStatus) {
                                $discountPercent[$sku]=$item['discount_percent'];
                                $discountAmount[$sku]=$item['base_discount_amount'];
                                $taxAmount[$sku]=$item['base_tax_amount'];
                                $taxPercent[$sku]=$item['tax_percent'];

//                                Customisation
                                $taxCalculationRate1->setRate($item['tax_percent']);
                                $taxCalculationRate1->save();
//                                $oldTaxClass = $product->getTaxClassId();
                                $product->setTaxClassId($taxClassId);
                                $product->save();
//                        echo $product->getTaxClassId(); exit;

//                                Customisation End

//                                $this->createCatalogPriceRule($sku,$discountPercent);
//                                if(isset($taxPercent) && !empty($taxPercent))
//                                {
                                ////                                    $this->editTaxClass($taxPercent);
//                                    $product->setTaxClassId(8);
//                                }
                                $itemAccepted++;
                                $currencyConvert = $this->scopeConfig->getvalue('goodmarket/goodmarket_product/conversion_rate');
                                $price = $item['base_price']/$currencyConvert;
                                $basePrice = $qty * (int)$price;
                                $rowTotal = $price * $qty;

                                $product->setIsSuperMode(true);
                                $product->setHasOptions(false);
                                $product->setData('salable', true);
                                $product->setPrice((int)$price)
                                    ->setSpecialPrice((int)$price)
                                    ->setTierPrice([])
                                    ->setBasePrice($basePrice)
                                    ->setOriginalCustomPrice((int)$price)
                                    ->setBaseOriginalCustomPrice((int)$price)
                                    ->setRowTotal($rowTotal)
                                    ->setBaseRowTotal($rowTotal);
                                $product->unsSkipCheckRequiredOption();
                                $product->setSkipSaleableCheck(true);
                                $quote->setIsSuperMode(true);
                                $quote->setIgnoreOldQty(true);
                                $quote->addProduct($product, (int)$qty);
                            } else {
                                $reason[] = sprintf(self::ERROR_OUT_OF_STOCK, $sku);
                            }
                        } else {
                            $reason[] = sprintf(self::ERROR_NOT_ENABLED, $sku, $store->getName());
                        }
                    } else {
                        $reason[] = sprintf(self::ERROR_DOES_NOT_EXISTS, $sku, $store->getName());
                    }
                } else {
                    $reason[] = sprintf(self::ERROR_ITEM_DATA_NOT_AVAILABLE);
                }
            }
            if ($itemAccepted != count($items)) {
                $this->reject($order, $reason);
            }

            if ($itemAccepted == count($items)) {
                $shipname=explode(' ', $order['address_information']['s_name'], '2');
                $shippingAddressStreet=isset($order['address_information']['s_street']) ? $order['address_information']['s_street'] : 'street';
                $shipAddress = [
                    'firstname' => isset($shipname['0']) ? $shipname['0'] : 'firstname',
                    'lastname' => isset($shipname['1']) ? $shipname['1'] : 'lastname',
                    'street' => $shippingAddressStreet,
                    'city' => isset($order['address_information']['s_city']) ? $order['address_information']['s_city'] : 'Bareilly',
                    'country_id' => 'US',//isset($order['address_information']['s_country'])?$order['address_information']['s_country']:'', //$this->getValue('country', $order,'US'),
                    'region' => isset($order['address_information']['s_region']) ? $order['address_information']['s_region'] : 'UttarPradesh', //$this->getValue('name', $this->getValue('state', $address, []), ''),*/
                    'postcode' => isset($order['address_information']['s_postcode']) ? $order['address_information']['s_postcode'] : '243001',
                    'telephone' =>isset($order['address_information']['s_telephone']) ? $order['address_information']['s_telephone'] : '00000000',
                    'fax' => '', 'save_in_address_book' => 1];
                // $billname=explode(' ',$order['address_information']['b_name'],'2');
                $billname=explode(' ', $order['address_information']['b_name'], '2');
                //$billingAddressStreet=isset($order['address_information']['b_street'])?$order['address_information']['b_street']:'street';
                $billingAddressStreet=isset($order['address_information']['b_street']) ? $order['address_information']['b_street'] : 'street';
                $billAddress = [
                    'firstname' => isset($billname['0']) ? $billname['0'] : 'firstname',
                    'lastname' => isset($billname['1']) ? $billname['1'] : 'lastname',
                    'street' => $billingAddressStreet,
                    'city' => isset($order['address_information']['b_city']) ? $order['address_information']['b_city'] : 'Bareilly',
                    'country_id' => 'US',//isset($order['address_information']['b_country'])?$order['address_information']['b_country']:'', //$this->getValue('country', $order,'US'),
                    'region' => isset($order['address_information']['b_region']) ? $order['address_information']['b_region'] : 'Uttar Pradesh', //$this->getValue('name', $this->getValue('state', $address, []), ''),*/
                    'postcode' => isset($order['address_information']['b_postcode']) ? $order['address_information']['b_postcode'] : '243001',
                    'telephone' =>isset($order['address_information']['b_telephone']) ? $order['address_information']['b_telephone'] : '00000000',
                    'fax' => '', 'save_in_address_book' => 1];
//                $shipAddress = [
//                    'firstname' => isset($billname['0'])?$billname['0']:'firstname',
//                    'lastname' => isset($billname['1'])?$billname['1']:'lastname',
//                    'street' => $billingAddressStreet,
//                    'city' => isset($order['address_information']['b_city'])?$order['address_information']['b_city']:'Bareilly',
//                    'country_id' => 'US',//isset($order['address_information']['b_country'])?$order['address_information']['b_country']:'', //$this->getValue('country', $order,'US'),
//                    'region' => isset($order['address_information']['b_region'])?$order['address_information']['b_region']:'Uttar Pradesh', //$this->getValue('name', $this->getValue('state', $address, []), ''),*/
//                    'postcode' => isset($order['address_information']['b_postcode'])?$order['address_information']['b_postcode']:'243001',
//                    'telephone' =>isset($order['address_information']['b_telephone'])?$order['address_information']['b_telephone']:'00000000',
//                    'fax' => '', 'save_in_address_book' => 1];

                $quote->getBillingAddress()
                    ->addData($billAddress);
                $shippingAddress = $quote->getShippingAddress()
                    ->addData($shipAddress);
                $shippingAddress->setCollectShippingRates(true)
                    ->collectShippingRates()
                    ->setShippingMethod('shipbygoodmarket_shipbygoodmarket');
                $quote->setPaymentMethod('paybygoodmarket');
                $quote->setInventoryProcessed(false);
                $quote->getPayment()
                    ->importData(['method' => 'paybygoodmarket']);

                $quote->collectTotals()
                    ->save();
                $quote->setCustomerIsGuest(0);
                foreach ($quote->getAllItems() as $item) {
//                    echo '<pre>'; print_r($item->getTaxAmount());exit;
                    $sku = $item->getProduct()->getSku();
                    $item->setDiscountAmount($discountAmount[$sku]);
                    $item->setBaseDiscountAmount($discountAmount[$sku]);
//                    Customisation comment
//                    $item->setTaxPercent($taxPercent[$sku]);
//                    $item->setBaseTaxPercent($taxPercent[$sku]);
//                    $item->setTaxAmount($taxAmount[$sku]);
//                    $item->setBaseTaxAmount($taxAmount[$sku]);
                    //END
                    $item->setOriginalCustomPrice($item->getPrice())
                        ->setOriginalPrice($item->getPrice())
                        ->save();
                }
                $quote->reserveOrderId();
                $reservedOrderId = $quote->getReservedOrderId();
                $GoodMarketPrefix = $this->config->getOrderIdPrefix();
                if ($reservedOrderId) {
                    if (strlen($GoodMarketPrefix) > 0) {
                        $magentoOrderId = (string)$GoodMarketPrefix . $reservedOrderId;
                        $quote->setReservedOrderId($magentoOrderId);
                    }
                }
                $quote->collectTotals()->save();
                /** @var \Magento\Sales\Model\Order $magentoOrder */
                $magentoOrder = $this
                    ->cartManagementInterface
                    ->submit($quote);
                $orderSubTotal=$order['order_total']['grand_total_earned'];
                $orderSubTotal=(int)substr($orderSubTotal, 21);

                /*$magentoOrder->setSubTotal($orderSubTotal);
                $magentoOrder->setBaseSubTotal($orderSubTotal);
                $magentoOrder->setGrandTotal($orderSubTotal);
                $magentoOrder->setBaseGrandTotal($orderSubTotal);*/

                $magentoOrder->addStatusHistoryComment("GoodMarket Order Id: " . $goodmarketOrderId);
                $magentoOrder->save();
                foreach ($magentoOrder->getAllItems() as $item) {
                    $item->setOriginalPrice($item->getPrice())
                        ->setBaseOriginalPrice($item->getPrice())
                        ->save();
                }
                if (isset($magentoOrder)) {
                    $count = isset($magentoOrder) ? $count + 1 : $count;

                    // after save order
                    $orderPlace = date("Y-m-d");
                    $status = 'Success';//$this->getstatus($this->getValue('status', $order));
                    $orderData = [\Ced\GoodMarket\Model\Order::COLUMN_MARKETPLACE_ORDER_ID => $goodmarketOrderId,
                        \Ced\GoodMarket\Model\Order::COLUMN_MARKETPLACE_DATE_CREATED => $orderPlace,
                        \Ced\GoodMarket\Model\Order::COLUMN_MAGENTO_INCREMENT_ID => $magentoOrder->getIncrementId(),
                        \Ced\GoodMarket\Model\Order::COLUMN_MAGENTO_ORDER_ID=>$magentoOrder->getId(),
                        'status' => $status,
                        'order_data' => $this
                            ->serializer
                            ->serialize($order) , \Ced\GoodMarket\Model\Order::COLUMN_FAILURE_REASON => ""];

                    $mporder = $this
                        ->orderFactory
                        ->create()
                        ->load($goodmarketOrderId)->addData($orderData);
                    $mporder->save();

//                    Customisation
                    /*$product->setTaxClassId($oldTaxClass);
                    $product->save();*/
//                    Customisation end
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Failed to create order in Magento.'));
                }
            }
        } catch (\Exception $exception) {
            $reason[] = $exception->getMessage() . $exception->getline();
            /*echo "<pre>";
            print_r($order);
            echo "<pre>";
            print_r($exception->getMessage());
            die(__FILE__);*/
            $this->reject($order, $reason);

            $this
                ->logger
                ->addError("Order #{$goodmarketOrderId} import failed." . $exception->getMessage(), ['path' => __METHOD__]);
            return false;
        }

        return $count;
    }

    /**
     * @param $name
     * @param $countryId
     * @return mixed|string
     */
    public function getRegionId($name, $countryId)
    {
//        $region = $this->region->create()->loadByName($name, $countryId);
//        if ($region && $region->getId()) {
//            return $region->getId();
//        }
//        return '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $region = $objectManager->create('Magento\Directory\Model\Region')
            ->loadByCode('Alaska', 'US');
        return $region->getRegionId();
    }

    public function getstatus($status)
    {
        switch ($status) {
            case 'U':
                $status = 'Unfulfilled';
                break;
            case 'PF':
                $status = 'Part-Fulfilled';
                break;
            case 'F':
                $status = 'Fulfilled';
                break;
            case 'C':
                $status = 'Cancelled';
                break;
        }
        return $status;
    }

    public function getValue($index, $haystack = [], $default = null)
    {
        $value = $default;
        if (isset($index, $haystack[$index]) && !empty($haystack[$index])) {
            $value = $haystack[$index];
        }
        return $value;
    }

    public function getCustomer($order, $websiteId)
    {
        $customerId = $this->config->getDefaultCustomer();
        $groupId=$this->scopeConfig->getvalue('goodmarket/order/customer_group');
        if ($customerId !== false) {
            /** case 1: Use default customer.*/
            $customer = $this->customerFactory->create()
                ->setWebsiteId($websiteId)
                ->load($customerId);
            if (!isset($customer) or empty($customer)) {
                $this->logger->log(
                    'ERROR',
                    "Default Customer does not exists. Customer Id: #{$customerId}."
                );
                return false;
            }
        } else {
            /** Case 2: Use Customer from Order.*/
            $email = $order['order_account_information']['account_information']['email'];
            /** Case 2.1 Get Customer if already exists. */
            $customer = $this->customerFactory->create()
                ->setWebsiteId($websiteId)
                ->loadByEmail($email);
            if (!isset($customer) || empty($customer) || empty($customer->getData())) {
                // Case 2.1 : Create customer if does not exists.
                try {
                    /** @var \Magento\Customer\Model\Customer $customer */
                    $customer = $this->customerFactory->create();
                    $customer->setWebsiteId($websiteId);
                    $customer->setEmail($email);
                    if (isset($order['order_account_information']['account_information'])) {
                        $customerName=explode(' ', $order['order_account_information']['account_information']['customer_name']);
                        $firstName = isset($customerName['0']) ? $customerName['0'] : 'FirstName';
                        $lastName= isset($customerName['1']) ? $customerName['1'] : 'SecondName';

                        $customer->setFirstname($firstName);
                        $customer->setLastname($lastName);
                    } else {
                        $customer->setFirstname($this->config->getSellerName());
                        $customer->setLastname('.');
                    }
                    $customer->setGroupId($groupId);
                    $customer->setPassword(uniqid());
                    $customer->save();
                } catch (\Exception $e) {
                    $this->logger->log(
                        'ERROR',
                        'Customer create failed. Order Id: #' . $order['order_id'],
                        [
                            'message' => $e->getMessage(),
                            'order_id' => $order['order_id']
                        ]
                    );
                    return false;
                }
            }
        }

        return $customer;
    }

    private function getEmail(array $order)
    {
        if (isset($order['billingDetails']) && !empty($order['billingDetails']['email'])) {
            return $order['billingDetails']['email'];
        }
        return [];
    }

    public function invoice($order)
    {
        try {
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }

            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order does not allow an invoice to be created.'));
            }

            /** @var \Magento\Sales\Model\Order\Invoice $invoice */
            $invoice = $this
                ->invoiceService
                ->prepareInvoice($order);

            if (!$invoice) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save the invoice right now.'));
            }
            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('You can\'t create an invoice without products.'));
            }
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $invoice->getOrder()
                ->setCustomerNoteNotify(false);
            $invoice->getOrder()
                ->setIsInProcess(true);
            $order->addStatusHistoryComment(__('Automatically invoiced via cedcommerce goodmarket.'), false);
            /** @var \Magento\Framework\DB\Transaction $transaction */
            $transaction = $this
                ->transactionFactory
                ->create()
                ->addObject($invoice)->addObject($invoice->getOrder());
            $transaction->save();
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage(), ['order_id' => $order->getId()]);
        }
    }

    /**
     * reject
     *
     * @param $order
     * @param $reason
     * @return bool
     */
    public function reject($order, $reason = [])
    {
        $response = false;
        $orderId = $this->getValue('order_increment_id', $order);
        /** @var \Ced\Mlibre\Model\Order $mporder */
        $mporder = $this
            ->orderFactory
            ->create()
            ->load($orderId, \Ced\GoodMarket\Model\Order::COLUMN_MARKETPLACE_ORDER_ID);

        $mporder->setData(\Ced\GoodMarket\Model\Order::COLUMN_STATUS, \Ced\GoodMarket\Model\Source\Order\Status::FAILED);
        $mporder->setData(\Ced\GoodMarket\Model\Order::COLUMN_MARKETPLACE_ORDER_ID, $orderId);
        $mporder->setData(\Ced\GoodMarket\Model\Order::COLUMN_FAILURE_REASON, $this
            ->serializer
            ->serialize($reason));
        $mporder->setData('order_data', $this
            ->serializer
            ->serialize($order));
        if ($response !== false) {
            $mporder->setData(
                \Ced\GoodMarket\Model\Order::COLUMN_STATUS,
                \Ced\GoodMarket\Model\Source\Order\Status::CANCELLED
            );
        }
        $mporder->save();
        $this->logger
            ->addNotice(
                'Order import failed. Order Id: #' . $orderId,
                ['cancelled' => $response, 'reason' => $reason]
            );
        return true;
    }

    /**
     * createTaxRuleAndClass
     *
     * @return void
     */
    public function createTaxRuleAndClass()
    {
        $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
        $objectManager = $bootstrap->getObjectManager();
        $taxCalculationRate1 = $objectManager->create(\Magento\Tax\Model\Calculation\Rate::class);//->load(1);
        $taxCalculationRate1->setCode("GOODMARKET");
        $taxCalculationRate1->setTaxCountryId("US");
        $taxCalculationRate1->setTaxRegionId("REGION");
        $taxCalculationRate1->setZipIsRange("0");
        $taxCalculationRate1->setTaxPostcode("*");
        $taxCalculationRate1->setRate("10.75");
        $taxCalculationRate1->save();
        $fixtureTaxRule1 = $objectManager->create(\Magento\Tax\Model\Calculation\Rule::class);//->load(1);
        $fixtureTaxRule1->setCode("GOODMARKET");
        $fixtureTaxRule1->setPriority(0);
        $fixtureTaxRule1->setCustomerTaxClassIds([3]);
        $fixtureTaxRule1->setProductTaxClassIds([8]);
        $fixtureTaxRule1->setTaxRateIds([$taxCalculationRate1->getId()]);
        $fixtureTaxRule1->save();
    }

    /**
     * editTaxClass
     *
     * @param $tax
     * @return bool
     */
    public function editTaxClass($tax)
    {
        $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
        $objectManager = $bootstrap->getObjectManager();
        $taxCalculationRate1 = $objectManager->create(\Magento\Tax\Model\Calculation\Rate::class)
            ->loadByCode('GOODMARKET');
        $taxCalculationRate1->setRate($tax);
        $taxCalculationRate1->save();
        return true;
    }

    /**
     * createCatalogPriceRule
     *
     * @param $sku
     * @param $discountPercentage
     * @return void
     */
    public function createCatalogPriceRule($sku, $discountPercentage)
    {
        $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
        $objectManager = $bootstrap->getObjectManager();
        $customerId = $this->config->getDefaultCustomer();
        $model = $objectManager->create(\Magento\CatalogRule\Model\Rule::class);
        $model->setName('GOODMARKET' . $sku)
            ->setDescription('description')
            ->setIsActive(1)
            ->setCustomerGroupIds([$customerId])
            ->setWebsiteIds([1])
            ->setFromDate('')
            ->setToDate('')
            ->setSimpleAction('by_percent')
            ->setDiscountAmount($discountPercentage)
            ->setStopRulesProcessing(0);

        $conditions = [];
        $conditions["1"] = [
            "type" => "Magento\CatalogRule\Model\Rule\Condition\Combine",
            "aggregator" => "all",
            "value" => 1,
            "new_child" => ""
        ];
        $conditions["1--1"] = [
            "type" => "Magento\CatalogRule\Model\Rule\Condition\Product",
            "attribute" => "sku",
            "operator" => "==",
            "value" => $sku
        ];

        $model->setData('conditions', $conditions);
        // Validating rule data before Saving
        $validateResult = $model->validateData(new \Magento\Framework\DataObject($model->getData()));
        if ($validateResult !== true) {
            foreach ($validateResult as $errorMessage) {
                echo $errorMessage;
            }
            return;
        }
        try {
            $model->loadPost($model->getData());
            $model->save();

            $ruleJob = $objectManager->get('Magento\CatalogRule\Model\Rule\Job');
            $ruleJob->applyAll();
            echo "rule created";
        } catch (Exception $e) {
            /*echo $e->getMessage();
            die(__FILE__);*/
            $this->logger->addError($e->getMessage(), ['path' => __METHOD__]);
        }
    }
}
