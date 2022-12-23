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
 * @package     Ced_ZalandoRetail
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Helper;

/**
 * Directory separator shorthand
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * class Config extends \Magento\Framework\App\Helper\AbstractHelper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const PRODUCT_ACTION_TYPE = 'goodmarket_product_action_type';
    public const PRODUCT_ACTION_KEY = 'goodmarket_product_action_key';
    public const ACTION_PRODUCT_UPLOAD = 'goodmarket_product_upload';
    public const ACTION_PRODUCT_UPDATE = 'goodmarket_product_update';
    public const ACTION_PRODUCT_UPDATE_DESCRIPTION = 'goodmarket_product_update_description';
    public const ACTION_PRODUCT_DELETE = 'goodmarket_product_delete';
    public const ACTION_PRODUCT_PAUSE = 'goodmarket_product_pause';
    public const ACTION_PRODUCT_REACTIVATE = 'goodmarket_product_reactivate';

    public const CONFIG_PATH_PRODUCT_CHUNK_UPLOAD_SIZE = 'goodmarket/product/chunk_settings/product_upload';
    public const CONFIG_PATH_PRODUCT_CHUNK_UPDATE_DESCRIPTION_SIZE =
        'goodmarket/product/chunk_settings/product_description_update';
    public const CONFIG_PATH_PRODUCT_CHUNK_DELETE_SIZE = 'goodmarket/product/chunk_settings/product_delete';

    public const CONFIG_PATH_STORE_ID = "goodmarket/settings/store_id";
    public const CONFIG_PATH_ENABLED = "goodmarket/settings/enable";
    public const CONFIG_PATH_CONFIG_USERNAME = "goodmarket/settings/username";
    public const CONFIG_PATH_CONFIG_PASSWORD = "goodmarket/settings/password";
    public const CONFIG_PATH_CONFIG_VENDOR_ID = "goodmarket/settings/vendor_id";

    public const CONFIG_PATH_DEBUG = "goodmarket/developer/debug";
    public const CONFIG_PATH_LOGGING_LEVEL = "goodmarket/developer/logging_level";

    public const CONFIG_PATH_INVENTORY_SYNC = "goodmarket/goodmarket_cron/inventory_cron";
    public const CONFIG_PATH_ORDER = "goodmarket/goodmarket_cron/order_cron";

    public const CONFIG_PATH_PRICE_TYPE = "goodmarket/product/price_settings/price";
    public const CONFIG_PATH_PRICE_TYPE_FIXED = "goodmarket/product/price_settings/fix_price";
    public const CONFIG_PATH_PRICE_TYPE_PERCENTAGE = "goodmarket/product/price_settings/percentage_price";
    public const CONFIG_PATH_PRICE_TYPE_ATTRIBUTE = "goodmarket/product/price_settings/different_price";
    public CONST CONFIG_PATH_SHIPMENT_MAPPING="goodmarket/order/carrier_mapping";
    public const CONFIG_PATH_INVENTORY_ZERO_CONDITION = "goodmarket/product/inventory_settings/zero_inventory_condition";

    public const CONFIG_PATH_ORDER_ID_PREFIX = "goodmarket/order/order_id_prefix";
    public const CONFIG_PATH_NOTIFICATION_EMAIL = "goodmarket/order/order_notify_email";
    public const CONFIG_PATH_ENABLE_DEFAULT_CUSTOMER = "goodmarket/order/enable_default_customer";
    public const CONFIG_PATH_IMPORT_ORDER = "goodmarket/order/fetchFrom";
    public const CONFIG_PATH_IMPORT_ORDER_STATUS = "goodmarket/order/order_status";
    public const CONFIG_PATH_IMPORT_ORDER_METHOD = "goodmarket/order/order_method";
    public const CONFIG_PATH_IMPORT_ORDER_LATEST = "goodmarket/order/latest_delivery";
    public const CONFIG_PATH_DEFAULT_CUSTOMER = "goodmarket/order/default_customer";
    public const CONFIG_PATH_AUTO_ACKNOWLEDGEMENT = "goodmarket/order/auto_acknowledge";
    public const CONFIG_PATH_AUTO_DESPATCH = "goodmarket/order/auto_despatch";
    public const CONFIG_PATH_AUTO_INVOICE = "goodmarket/order/auto_invoice";
    public const CONFIG_PATH_IMPORT_ORDER_CANCELLATION="goodmarket/order/cancellation_code";

    public const CONFIG_PATH_PRODUCT_AUTOSYNC="goodmarket/goodmarket_product/product_inventory";
    public const CONFIG_PATH_APP_ID = "goodmarket/settings/app_id";
    public const CONFIG_PATH_SECRET_KEY = "goodmarket/settings/secret_key";
    public const CONFIG_PATH_SITE_ID = "goodmarket/settings/site";
    public const CONFIG_PATH_CURRENCY_ID = "goodmarket/settings/currency";

    public const FLAG_KEY_SELLER_ID = "goodmarket_seller_id";
    public const FLAG_KEY_SELLER_ADDRESS = "goodmarket_seller_address";
    public const FLAG_KEY_ACCOUNT = "goodmarket_account_detail";
    public const FLAG_KEY_SELLER_EMAIL = "goodmarket_seller_email";
    public const FLAG_KEY_SELLER_COUNTRY_ID = "goodmarket_seller_country_id";
    public const FLAG_KEY_SELLER_NAME = "goodmarket_seller_name";
    public const FLAG_KEY_SELLER_PHONE = "goodmarket_seller_phone";

    public const FLAG_KEY_ACCESS_TOKEN = "goodmarket_access_token";
    public const FLAG_KEY_REFRESH_TOKEN = "goodmarket_refresh_token";
    public const FLAG_KEY_EXPIRY = "goodmarket_token_expiry";
    public const FLAG_KEY_CONFIG_VALID = "goodmarket_config_status";

    public const TYPE_DEFAULT = 'default';
    public const TYPE_FIXED_INCREASE = 'plus_fixed';
    public const TYPE_FIXED_DECREASE = 'min_fixed';
    public const TYPE_PERCENTAGE_INCREASE = 'plus_per';
    public const TYPE_PERCENTAGE_DECREASE = 'min_per';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfigManager;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    public $dl;

    /**
     * @var $appId
     */
    public $appId;

    /**
     * @var $secretKey
     */
    public $secretKey;

    /**
     * Debug Log Mode
     * @var boolean
     */
    public $debugMode = true;

    /** @var \Magento\Framework\FlagFactory  */
    public $flagFactory;

    /** @var \Magento\Framework\Flag\FlagResource  */
    public $flagResource;

    public $accessToken = null;

    public $refreshToken = null;

    public $expiry = null;

    public $sellerName = null;
    public $sellerId = null;
    public $sellerAddress = null;
    public $sellerPhone = null;
    public $sellerCountryId = null;
    public $account = null;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Flag\FlagResource $flagResource
     * @param \Magento\Framework\FlagFactory $flagFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Flag\FlagResource $flagResource,
        \Magento\Framework\FlagFactory $flagFactory
    ) {
        parent::__construct($context);
        $this->scopeConfigManager = $context->getScopeConfig();
        $this->flagFactory = $flagFactory;
        $this->flagResource = $flagResource;
    }

    /**
     * setSellerEmail
     *
     * @param $email
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSellerEmail($email)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_EMAIL]]);
        $flag->loadSelf();
        $flag->setFlagData($email);
        $this->flagResource->save($flag);
        $this->sellerEmail = $email;
    }

    /**
     * getSellerEmail
     *
     * @return mixed
     */
    public function getSellerEmail()
    {
        if (empty($this->sellerEmail)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_EMAIL]]);
            $this->flagResource->load($flag, self::FLAG_KEY_SELLER_EMAIL, 'flag_code');
            $this->sellerEmail = $flag->getFlagData();
        }
        return $this->sellerEmail;
    }

    /**
     * setSellerName
     *
     * @param $name
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSellerName($name)
    {
        /**
         * @var \Magento\Framework\Flag $flag
         */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_NAME]]);
        $flag->loadSelf();
        $flag->setFlagData($name);
        $this->flagResource->save($flag);
        $this->sellerName = $name;
    }

    /**
     * getSellerName
     *
     * @return mixed
     */
    public function getSellerName()
    {
        if (empty($this->sellerName)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_NAME]]);
            $this->flagResource->load($flag, self::FLAG_KEY_SELLER_NAME, 'flag_code');
            $this->sellerName = $flag->getFlagData();
        }
        return $this->sellerName;
    }

    /**
     * setSellerId
     *
     * @param $id
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSellerId($id)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_ID]]);
        $flag->loadSelf();
        $flag->setFlagData($id);
        $this->flagResource->save($flag);
        $this->sellerId = $id;
    }

    /**
     * getSellerId
     *
     * @return mixed
     */
    public function getSellerId()
    {
        if (empty($this->sellerId)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_ID]]);
            $this->flagResource->load($flag, self::FLAG_KEY_SELLER_ID, 'flag_code');
            $this->sellerId = $flag->getFlagData();
        }
        return $this->sellerId;
    }

    /**
     * setSellerCountryId
     *
     * @param $id
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSellerCountryId($id)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_COUNTRY_ID]]);
        $flag->loadSelf();
        $flag->setFlagData($id);
        $this->flagResource->save($flag);
        $this->sellerCountryId = $id;
    }

    /**
     * getSellerCountryId
     *
     * @return mixed
     */
    public function getSellerCountryId()
    {
        if (empty($this->sellerCountryId)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_COUNTRY_ID]]);
            $this->flagResource->load($flag, self::FLAG_KEY_SELLER_COUNTRY_ID, 'flag_code');
            $this->sellerCountryId = $flag->getFlagData();
        }
        return $this->sellerCountryId;
    }

    /**
     * setSellerPhone
     *
     * @param $phone
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSellerPhone($phone)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_PHONE]]);
        $flag->loadSelf();
        $flag->setFlagData($phone);
        $this->flagResource->save($flag);
        $this->sellerPhone = $phone;
    }

    /**
     * getSellerPhone
     *
     * @return mixed
     */
    public function getSellerPhone()
    {
        if (empty($this->sellerPhone)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_PHONE]]);
            $this->flagResource->load($flag, self::FLAG_KEY_SELLER_PHONE, 'flag_code');
            $this->sellerPhone = $flag->getFlagData();
        }
        return $this->sellerPhone;
    }

    /**
     * setSellerAddress
     *
     * @param $address
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSellerAddress($address)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_ADDRESS]]);
        $flag->loadSelf();
        $flag->setFlagData($address);
        $this->flagResource->save($flag);
        $this->sellerAddress = $address;
    }

    /**
     * getSellerAddress
     *
     * @return mixed
     */
    public function getSellerAddress()
    {
        if (empty($this->sellerAddress)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_SELLER_ADDRESS]]);
            $this->flagResource->load($flag, self::FLAG_KEY_SELLER_ADDRESS, 'flag_code');
            $this->sellerAddress = $flag->getFlagData();
        }
        return $this->sellerAddress;
    }

    /**
     * setAccount
     *
     * @param $accountDetail
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setAccount($accountDetail)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_ACCOUNT]]);
        $flag->loadSelf();
        $flag->setFlagData($accountDetail);
        $this->flagResource->save($flag);
        $this->account = $accountDetail;
    }

    /**
     * getAccount
     *
     * @return mixed
     */
    public function getAccount()
    {
        if (empty($this->account)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_ACCOUNT]]);
            $this->flagResource->load($flag, self::FLAG_KEY_ACCOUNT, 'flag_code');
            $this->account = $flag->getFlagData();
        }
        return $this->account;
    }

    /**
     * getAccessToken
     *
     * @return mixed
     */
    public function getAccessToken()
    {
        if (empty($this->accessToken)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_ACCESS_TOKEN]]);
            $this->flagResource->load($flag, self::FLAG_KEY_ACCESS_TOKEN, 'flag_code');
            $this->accessToken = $flag->getFlagData();
        }
        return $this->accessToken;
    }

    /**
     * getRefreshToken
     *
     * @return mixed
     */
    public function getRefreshToken()
    {
        if (empty($this->refreshToken)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_REFRESH_TOKEN]]);
            $this->flagResource->load($flag, self::FLAG_KEY_REFRESH_TOKEN, 'flag_code');
            $this->refreshToken = $flag->getFlagData();
        }
        return $this->refreshToken;
    }

    /**
     * getTokenExpiry
     *
     * @return mixed
     */
    public function getTokenExpiry()
    {
        if (empty($this->expiry)) {
            $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_EXPIRY]]);
            $this->flagResource->load($flag, self::FLAG_KEY_EXPIRY, 'flag_code');
            $this->expiry = $flag->getFlagData();
        }
        return $this->expiry;
    }

    /**
     * Set access Token
     *
     * @param $token
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setAccessToken($token)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_ACCESS_TOKEN]]);
        $flag->loadSelf();
        $flag->setFlagData($token);
        $this->flagResource->save($flag);
        $this->accessToken = $token;
    }

    /**
     * Set Refresh Token
     *
     * @param string $token
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function setRefreshToken($token)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_REFRESH_TOKEN]]);
        $flag->loadSelf();
        $flag->setFlagData($token);
        $this->flagResource->save($flag);
        $this->refreshToken = $token;
    }

    /**
     * Set Expiry Time
     *
     * @param string $expiry
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function setTokenExpiry($expiry)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_EXPIRY]]);
        $flag->loadSelf();
        $flag->setFlagData($expiry);
        $this->flagResource->save($flag);
        $this->expiry = $expiry;
    }

    /**
     * isValid
     *
     * @return bool
     */
    public function isValid()
    {
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_CONFIG_VALID]]);
        $this->flagResource->load($flag, self::FLAG_KEY_CONFIG_VALID, 'flag_code');
        $valid = $flag->getFlagData();
        return $valid;
    }

    /**
     * setValid
     *
     * @param $status
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException'
     */
    public function setValid($status)
    {
        /**
         * @var \Magento\Framework\Flag $flag
         */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_KEY_CONFIG_VALID]]);
        $flag->loadSelf();
        $flag->setFlagData($status);
        $this->flagResource->save($flag);
    }

    /**
     * Get Mock mode for config
     *
     * @return bool
     */
    public function isEnabled()
    {
        $enable = $this->scopeConfigManager->getValue(self::CONFIG_PATH_ENABLED);
        if ($enable === null) {
            $enable = false;
        }
        return $enable;
    }

    /**
     * getOrderIdPrefix
     *
     * @return string
     */
    public function getOrderIdPrefix()
    {
        $prefix = $this->scopeConfigManager->getValue(self::CONFIG_PATH_ORDER_ID_PREFIX);
        if (isset($prefix) && !empty($prefix)) {
            return $prefix . '-';
        }
        return '';
    }

    /**
     * getOrderMethod
     *
     * @return mixed
     */
    public function getOrderMethod()
    {
        $data = $this->scopeConfigManager->getValue(self::CONFIG_PATH_IMPORT_ORDER_METHOD);
        return $data;
    }

    /**
     * getOrderLatestTime
     *
     * @return string
     */
    public function getOrderLatestTime()
    {
        $prefix = $this->scopeConfigManager->getValue(self::CONFIG_PATH_IMPORT_ORDER_LATEST);
        if (isset($prefix) && !empty($prefix)) {
            $Date = Date('Y-m-d', strtotime('+' . $prefix . ' days'));
            return $Date;
        }

        return '';
    }

    /**
     * getDefaultStoreId
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        return $storeId;
    }

    /**
     * getStoreId
     *
     * @return int|mixed
     */
    public function getStoreId()
    {
        $storeId = $this->scopeConfig->getValue('goodmarket/settings/goodmarket_store');
        if (!isset($storeId) || empty($storeId)) {
            $storeId = $this->getDefaultStoreId();
        }

        return $storeId;
    }

    /**
     * getStore
     *
     * @return int|mixed
     */
    public function getStore()
    {
        return $this->getStoreId();
    }

    /**
     * getClientId
     *
     * @return mixed
     */
    public function getClientId()
    {
        $email = $this->scopeConfig->getValue(self::CONFIG_PATH_CONFIG_CLIENT_ID);
        return $email;
    }

    /**
     * getApiKey
     *
     * @return mixed
     */
    public function getApiKey()
    {
        $email = $this->scopeConfig->getValue(self::CONFIG_PATH_CONFIG_API_KEY);
        return $email;
    }

    /**
     * getOrderApiKey
     *
     * @return mixed
     */
    public function getOrderApiKey()
    {
        $email = $this->scopeConfig->getValue(self::CONFIG_PATH_CONFIG_ORDER_API_KEY);
        return $email;
    }

    /**
     * getNotificationEmail
     *
     * @return mixed
     */
    public function getNotificationEmail()
    {
        $email = $this->scopeConfig->getValue(self::CONFIG_PATH_NOTIFICATION_EMAIL);
        return $email;
    }

    /**
     * getLoggingLevel
     *
     * @return mixed
     */
    public function getLoggingLevel()
    {
        $level = $this->scopeConfig->getValue(self::CONFIG_PATH_LOGGING_LEVEL);
        return $level;
    }

    /**
     * getAppId
     *
     * @return mixed
     */
    public function getAppId()
    {
        $appId = $this->scopeConfig->getValue(self::CONFIG_PATH_APP_ID);
        return $appId;
    }

    /**
     * getSiteId
     *
     * @return mixed
     */
    public function getSiteId()
    {
        $siteId = $this->scopeConfig->getValue(self::CONFIG_PATH_SITE_ID);
        return $siteId;
    }

    /**
     * getSecretKey
     *
     * @return mixed
     */
    public function getSecretKey()
    {
        $key = $this->scopeConfig->getValue(self::CONFIG_PATH_SECRET_KEY);
        return $key;
    }

    /**
     * getDebug
     *
     * @return bool|mixed
     */
    public function getDebug()
    {
        $this->debugMode = $this->scopeConfigManager
            ->getValue(self::CONFIG_PATH_DEBUG);
        if (!isset($this->debugMode)) {
            $this->debugMode = true;
        }
        return $this->debugMode ;
    }

    /**
     * getPriceSync
     *
     * @return mixed
     */
    public function getPriceSync()
    {
        $sync = $this->scopeConfigManager->getValue(self::CONFIG_PATH_ORDER);
        return $sync;
    }

    /**
     * getInventorySync
     *
     * @return mixed
     */
    public function getInventorySync()
    {
        $sync = $this->scopeConfigManager->getValue(self::CONFIG_PATH_INVENTORY_SYNC);
        return $sync;
    }

    /**
     * getOrderCronStatus
     *
     * @return mixed
     */
    public function getOrderCronStatus()
    {
        $type = $this->scopeConfigManager->getValue(self::CONFIG_PATH_ORDER);
        return $type;
    }

    /**
     * getPriceFixed
     *
     * @return string
     */
    public function getPriceFixed()
    {
        $fixed = trim((string)$this->scopeConfigManager->getValue(self::CONFIG_PATH_PRICE_TYPE_FIXED));
        return $fixed;
    }

    /**
     * getPricePercentage
     *
     * @return string
     */
    public function getPricePercentage()
    {
        $percentage = trim((string)$this->scopeConfigManager->getValue(self::CONFIG_PATH_PRICE_TYPE_PERCENTAGE));
        return $percentage;
    }

    /**
     * getPriceAttribute
     *
     * @return string
     */
    public function getPriceAttribute()
    {
        //@Suggest: can be obtained from profile
        $attribute = trim((string)$this->scopeConfigManager->getValue(self::CONFIG_PATH_PRICE_TYPE_ATTRIBUTE));
        return $attribute;
    }

    /**
     * getRedirectUri
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->_getUrl('rest/all/V1/custom/custom-api', ['_nosid' => true, '_forced_secure' => true]);
    }

    /**
     * Get Chunk Size, default 25
     *
     * @param string $type
     * @return int
     */
    public function getChunkSize($type = self::ACTION_PRODUCT_UPLOAD)
    {
        switch ($type) {
            case self::ACTION_PRODUCT_UPLOAD:
            case self::ACTION_PRODUCT_UPDATE:
                $chunkSize = $this->scopeConfigManager
                    ->getValue(self::CONFIG_PATH_PRODUCT_CHUNK_UPLOAD_SIZE);
                break;
            case self::ACTION_PRODUCT_UPDATE_DESCRIPTION:
                $chunkSize = $this->scopeConfigManager
                    ->getValue(self::CONFIG_PATH_PRODUCT_CHUNK_UPDATE_DESCRIPTION_SIZE);
                break;
            case self::ACTION_PRODUCT_DELETE:
            case self::ACTION_PRODUCT_PAUSE:
            case self::ACTION_PRODUCT_REACTIVATE:
                $chunkSize = $this->scopeConfigManager
                    ->getValue(self::CONFIG_PATH_PRODUCT_CHUNK_DELETE_SIZE);
                break;
            default:
                $chunkSize = 30;
        }

        if (!isset($chunkSize) || empty($chunkSize)) {
            $chunkSize = 30;
        }

        return $chunkSize;
    }

    /**
     * getCurrency
     *
     * @return mixed
     */
    public function getCurrency()
    {
        $currency = $this->scopeConfigManager->getValue(self::CONFIG_PATH_CURRENCY_ID);
        return $currency;
    }

    /**
     * getFirstName
     *
     * @return mixed
     */
    public function getFirstName()
    {
        $currency = $this->scopeConfigManager->getValue('goodmarket/order/customer_name');
        return $currency;
    }

    /**
     * getLastName
     *
     * @return mixed
     */
    public function getLastName()
    {
        $currency = $this->scopeConfigManager->getValue('goodmarket/order/customer_lastname');
        return $currency;
    }

    /**
     * getGroupName
     *
     * @return mixed
     */
    public function getGroupName()
    {
        $currency = $this->scopeConfigManager->getValue('goodmarket/order/customer_group');
        return $currency;
    }

    /**
     * getCustomerEmail
     *
     * @return mixed
     */
    public function getCustomerEmail()
    {
        $currency = $this->scopeConfigManager->getValue('goodmarket/order/customer_email');
        return $currency;
    }

    /**
     * getZeroInventory
     *
     * @return mixed
     */
    public function getZeroInventory()
    {
        $condition = $this->scopeConfigManager->getValue(self::CONFIG_PATH_INVENTORY_ZERO_CONDITION);
        if (empty($condition)) {
            $condition = \Ced\ZalandoRetail\Model\Source\Config\Inventory\ZeroCondition::SKIP;
        }
        return $condition;
    }

    /**
     * Get default customer id
     *
     * @return bool|string
     */
    public function getDefaultCustomer()
    {
        $customer = false;
        $enabled = $this->scopeConfigManager->getValue(self::CONFIG_PATH_ENABLE_DEFAULT_CUSTOMER);
        if ($enabled == 1) {
            $customer = $this->scopeConfigManager->getValue(self::CONFIG_PATH_DEFAULT_CUSTOMER);
        }
        return $customer;
    }

    /**
     * Get auto invoice enable
     *
     * @return bool|mixed
     */
    public function getAutoInvoice()
    {
        $autoInvoice = $this->scopeConfigManager->getValue(self::CONFIG_PATH_AUTO_INVOICE);
        if (isset($autoInvoice) && empty($autoInvoice)) {
            $autoInvoice = false;
        }
        return $autoInvoice;
    }

    /**
     * getAutoDespatch
     *
     * @return false|mixed
     */
    public function getAutoDespatch()
    {
        $autoReject = $this->scopeConfigManager
            ->getValue(self::CONFIG_PATH_AUTO_DESPATCH);
        if (isset($autoReject) && empty($autoReject)) {
            $autoReject = false;
        }
        return $autoReject;
    }

    /**
     * getAutoAcknowledgement
     *
     * @return false|mixed
     */
    public function getAutoAcknowledgement()
    {
        $ack = $this->scopeConfigManager->getValue(self::CONFIG_PATH_AUTO_ACKNOWLEDGEMENT);
        if (isset($ack) && empty($ack)) {
            $ack = false;
        }
        return $ack;
    }

    /**
     * setOrderImport
     *
     * @param $address
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setOrderImport($address)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::CONFIG_PATH_IMPORT_ORDER]]);
        $flag->loadSelf();
        $flag->setFlagData($address);
        $this->flagResource->save($flag);
        $this->sellerAddress = $address;
    }

    /**
     * getOrderImport
     *
     * @return mixed
     */
    public function getOrderImport()
    {
        $from = $this->scopeConfigManager->getValue(self::CONFIG_PATH_IMPORT_ORDER);
        return $from;
    }

    /**
     * setOrderStatus
     *
     * @param $address
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setOrderStatus($address)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => self::CONFIG_PATH_IMPORT_ORDER_STATUS]]);
        $flag->loadSelf();
        $flag->setFlagData($address);
        $this->flagResource->save($flag);
        $this->sellerAddress = $address;
    }

    /**
     * getOrderStatus
     *
     * @return mixed
     */
    public function getOrderStatus()
    {
        $status = $this->scopeConfigManager->getValue(self::CONFIG_PATH_IMPORT_ORDER_STATUS);
        return $status;
    }

    /**
     * getShipmentMapping
     *
     * @return mixed
     */
    public function getShipmentMapping()
    {
        $sync = $this->scopeConfigManager->getValue(self::CONFIG_PATH_SHIPMENT_MAPPING);
        return json_decode($sync,true);
    }

    /**
     * getCancellationCode
     *
     * @return mixed
     */
    public function getCancellationCode()
    {
        $code = $this->scopeConfigManager->getValue(self::CONFIG_PATH_IMPORT_ORDER_CANCELLATION);
        return $code;
    }

    /**
     * getProductAutoUpdate
     *
     * @return mixed
     */
    public function getProductAutoUpdate()
    {
        $status = $this->scopeConfig->getValue(self::CONFIG_PATH_PRODUCT_AUTOSYNC);
        return $status;
    }

    /**
     * getUsername
     *
     * @return mixed
     */
    public function getUsername()
    {
        $data = $this->scopeConfig->getValue("goodmarket/settings/username");
        return $data;
    }

    /**
     * getPassword
     *
     * @return mixed
     */
    public function getPassword()
    {
        $data = $this->scopeConfig->getValue("goodmarket/settings/password");
        return $data;
    }

    public function getUseMsi()
    {
        $useMsi = $this->scopeConfigManager
            ->getValue("goodmarket/inventory_settings/use_msi");
        return $useMsi;
    }

    /**
     * getUseSalableQty
     *
     * @return mixed
     */
    public function getUseSalableQty() {
        return $this->scopeConfigManager
            ->getValue('goodmarket/inventory_settings/use_salable_qty');
    }

    /**
     * getSalableStockName
     *
     * @return mixed
     */
    public function getSalableStockName() {
        return $this->scopeConfigManager
            ->getValue('goodmarket/inventory_settings/salable_stock_name');
    }

    /**
     * getMsiSourceCode
     *
     * @return mixed
     */
    public function getMsiSourceCode()
    {
        $msiSourceCode = $this->scopeConfigManager
            ->getValue("goodmarket/inventory_settings/msi_source_code");
        return $msiSourceCode;
    }
}
