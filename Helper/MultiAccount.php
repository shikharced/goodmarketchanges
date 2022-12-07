<?php
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
namespace Ced\GoodMarket\Helper;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Backend\Model\Session;

class MultiAccount extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Ced\GoodMarket\Model\Accounts
     */
    protected $accountModel;
    /**
     * @var \Ced\GoodMarket\Model\Profile
     */
    protected $profileModel;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    public $eavAttribute;
    /** @var EavSetup $eavSetup */
    public $eavSetup;
    /**
     * @var \Ced\GoodMarket\Model\ResourceModel\Accounts\Collection
     */
    protected $accountsCollectionFactory;

    public $adminSession;

    /**
     * MultiAccount constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Ced\GoodMarket\Model\AccountsFactory $accounts
     * @param \Ced\GoodMarket\Model\ProfileFactory $profile
     * @param \Ced\GoodMarket\Model\ResourceModel\Accounts\CollectionFactory $accountsCollectionFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
     * @param Session $session
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        Session $session
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->eavAttribute = $eavAttribute;
        $this->eavSetup = $eavSetupFactory->create(['setup' => $setup]);
        $this->adminSession = $session;
    }

//    public function unsAccountRegistry() {
//        if($this->_coreRegistry->registry('mlibre_account'))
//            $this->_coreRegistry->unregister('mlibre_account');
//    }

//    public function getAccountRegistry($accId = null) {
//        /** @var \Ced\GoodMarket\Model\Accounts $account */
//        $account = $this->accountModel->create();
//        if (isset($accId) && !empty($accId)) {
//            $account = $account->load($accId);
//        }
//        if(!$this->_coreRegistry->registry('mlibre_account'))
//            $this->_coreRegistry->register('mlibre_account', $account);
//        return $this->_coreRegistry->registry('mlibre_account');
//    }

    public function getProfileAttrForAcc($accId = null) {
        $attributeCode = '';
        if($accId > 0) {
            $attributeCode = 'goodmarket_profile_' . $accId;
        } else {
            $attributeCode = '';
        }
        return $attributeCode;
    }

    public function getItemIdAttrForAcc($accId = null) {
        $attributeCode = '';
        if($accId > 0) {
            $attributeCode = 'goodmarket_product_' . $accId;
        } else {
            $attributeCode = '';
        }
        return $attributeCode;
    }
    public function getItemIdAttrForVariantAcc($accId = null) {
        $attributeCode = '';
        if($accId > 0) {
            $attributeCode = 'goodmarket_product_variant_' . $accId;
        } else {
            $attributeCode = '';
        }
        return $attributeCode;
    }

    public function getProdStatusAttrForAcc($accId = null) {
        $attributeCode = '';
        if($accId > 0) {
            $attributeCode = 'goodmarket_prod_status_' . $accId;
        } else {
            $attributeCode = '';
        }
        return $attributeCode;
    }

    public function getProdListingErrorAttrForAcc($accId = null) {
        $attributeCode = '';
        if($accId > 0) {
            $attributeCode = 'goodmarket_error_' . $accId;
        } else {
            $attributeCode = '';
        }
        return $attributeCode;
    }

//    public function getAllAccounts($onlyActive = false) {
//        if($onlyActive)
//            $accountCollection = $this->accountsCollectionFactory->create()->addFieldToFilter('account_status', 1);
//        else
//            $accountCollection = $this->accountsCollectionFactory->create();
//        return $accountCollection;
//    }

//    public function getAllProfileAttr() {
//        $attributeCodes = array();
//        $accounts = $this->accountsCollectionFactory->create();
//        foreach ($accounts as $account) {
//            $accId = $account->getId();
//            if($accId > 0) {
//                $attributeCodes[] = 'goodmarket_profile_' . $accId;
//            }
//        }
//        return $attributeCodes;
//    }

//    public function getAccountFromLocation($location) {
//        $account = $this->accountsCollectionFactory->create()
//            ->addFieldToFilter('account_location', array('eq' => $location))
//            ->getFirstItem();
//        return $account->getId();
//    }

//    public function setAccountSession() {
//        $accountId = '';
//        $this->adminSession->unsAccountId();
//        $params = $this->_getRequest()->getParams();
//        if(isset($params['account_id']) && $params['account_id'] > 0) {
//            $accountId = $params['account_id'];
//        } else {
//            $accountId = $this->scopeConfig->getValue('goodmarket/settings/primary_account');
//            if(!$accountId) {
//                $accounts = $this->getAllAccounts();
//                if($accounts) {
//                    $accountId = $accounts->getFirstItem()->getId();
//                }
//            }
//        }
//        $this->adminSession->setAccountId($accountId);
//        return $accountId;
//    }

//    public function getAccountSession() {
//        $accountId = '';
//        $accountId = $this->adminSession->getAccountId();
//        if(!$accountId) {
//            $accountId = $this->setAccountSession();
//        }
//        return $accountId;
//    }


    public function getReason($reason)
    {
        switch($reason)
        {
            case 'Stock Not Available': $cancelCode='No available stock for an item(s) ordered';
                break;
            case 'Unable to Deliver to Address': $cancelCode='Courier unable to deliver ordered item(s) to specified delivery address';
                break;
            case 'Unable to Contact Customer to Argoodmarket Delivery': $cancelCode='You are unable to contact the customer to argoodmarket delivery of item(s)';
                break;
        }
        return $cancelCode;
    }
}