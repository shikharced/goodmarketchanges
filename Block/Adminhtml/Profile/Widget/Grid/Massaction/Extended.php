<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\GoodMarket\Block\Adminhtml\Profile\Widget\Grid\Massaction;

/**
 * Class Extended for massaction
 */
class Extended extends \Magento\Backend\Block\Widget\Grid\Massaction\Extended
{

    protected $_objectManager;
    protected $_template = 'Ced_GoodMarket::widget/grid/massaction.phtml';

    /**
     * Get Selected Json
     *
     * @return string
     */
    public function getSelectedJson()
    {
        return join(",", $this->_getProducts());
    }

    /**
     * Get Products
     *
     * @param $isJson
     * @return array|string|string[]
     */
    public function _getProducts($isJson = false)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($this->getRequest()->getPost('in_profile_products') != "") {
            return explode(",", $this->getRequest()->getParam('in_profile_products'));
        }

        $profileId = $this->getRequest()->getParam('pcode');
        $profile = $this->_objectManager->get(\Magento\Framework\Registry::class)->registry('current_profile');

        if ($profile && $profile->getId()) {
            $profileId = $profile->getId();
        }
       // $productIds  = $this->_objectManager->create('\Ced\GoodMarket\Model\Profileproducts')
        //->getProfileProducts($profileId);

//        if (sizeof($productIds) > 0) {
        $products = $this->_objectManager->create('\Magento\Catalog\Model\Product')
            ->getCollection()
            ->addAttributeToFilter('visibility', ['neq' => 1])
            ->addAttributeToFilter('type_id', ['simple', 'configurable'])
            ->addFieldToFilter('goodmarket_profile_id', ['in' => $profileId]);
        if ($isJson) {
            $jsonProducts = [];
            foreach ($products as $product)  {
                $jsonProducts[$product->getEntityId()] = 0;
            }
            return $this->_jsonEncoder->encode((object)$jsonProducts);
        } else {
            $jsonProducts = [];
            foreach ($products as $product)  {
                $jsonProducts[$product->getEntityId()] = $product->getEntityId();
            }
            return $jsonProducts;
        }
//        } else {
//            if ($isJson) {
//                return '{}';
//            } else {
//                return array();
//            }
       // }
    }

    /**
     * Get Custom Grid Id Json
     *
     * @return string
     */
    public function getCustomGridIdsJson()
    {
        if (!$this->getUseSelectAll()) {
            return '';
        }

        /** @var \Magento\Framework\Data\Collection $allIdsCollection **/
        $allIdsCollection = clone $this->getParentBlock()->getCollection();
        $gridIds = $allIdsCollection->clear()->setPageSize(0)->getAllIds();

        if (!empty($gridIds)) {
            return join(",", $gridIds);
        }
        return '';
    }
}
