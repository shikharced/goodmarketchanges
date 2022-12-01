<?php

namespace Ced\GoodMarket\Controller\Adminhtml\Config;

/**
 * Class Save
 * @package Ced\Mlibre\Controller\Adminhtml\Config
 */
class Save extends \Magento\Backend\App\Action
{
    /** @var \Magento\Config\Model\ResourceModel\Config */
    public $configWriter;

    /** @var \Ced\Mlibre\Helper\Config */
    public $config;

    public $cacheTypeList;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Config\Model\ResourceModel\Config $configWriter,
        \Ced\GoodMarket\Helper\Config $config,
        \Ced\GoodMarket\Helper\Data $data,
        \Ced\GoodMarket\Helper\Product $helper
    ) {
        parent::__construct($context);
        $this->cacheTypeList = $cacheTypeList;
        $this->configWriter = $configWriter;
        $this->config = $config;
        $this->data=$data;
        $this->helper=$helper;
    }

    public function execute()
    {
        $response = [
            'success' => false,
            'message' => __('Token Was Not Fetched ,Please check the details.'),
            'redirect_uri' => null
        ];
//        $this->configWriter->saveConfig('goodmarket/settings/vendor_id', ' ', 'default', 0);
        $this->configWriter->saveConfig('goodmarket/settings/hash_token', ' ', 'default', 0);
        $this->configWriter->saveConfig('goodmarket/settings/token', ' ', 'default', 0);
        $this->cacheTypeList->cleanType('config');
        $username = $this->getRequest()->getParam('username');
        $vendor = $this->getRequest()->getParam('vendorId');
        if (isset($username) && isset($vendor) && !empty($username)  && !empty($vendor)) {
            $this->configWriter->saveConfig(\Ced\GoodMarket\Helper\Config::CONFIG_PATH_CONFIG_USERNAME, $username, 'default', 0);
            $this->configWriter->saveConfig(\Ced\GoodMarket\Helper\Config::CONFIG_PATH_CONFIG_VENDOR_ID, $vendor, 'default', 0);
           $this->cacheTypeList->cleanType('config');

           $vendor_data=$this->data->getAuthorisation($username,$vendor);
           if(isset($vendor_data) && !empty($vendor_data) && !empty($vendor_data['hash_token']) && !empty($vendor_data['token'])) {
               //$this->configWriter->saveConfig('goodmarket/settings/vendor_id', $vendor_data['vendor_id'], 'default', 0);
               //$this->configWriter->saveConfig('goodmarket/settings/hash_token', $vendor_data['hash_token'], 'default', 0);
               $this->configWriter->saveConfig('goodmarket/settings/token', json_encode($vendor_data), 'default', 0);
               $this->cacheTypeList->cleanType('config');
               $response['success'] = true;
               $response['message'] =
                   __('Token Has Been Fetched Successfully!!');
               $this->helper->profileCategory();
           }

        }
//        $this->_redirect('adminhtml/system_config/edit/section/ebay_product_import_config');
//        $this->_redirect('goodmarket/product/index');
        //$this->_redirect('adminhtml/system_config/edit/section/goodmarket');
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $result->setData($response);

        return $result;
    }
}
