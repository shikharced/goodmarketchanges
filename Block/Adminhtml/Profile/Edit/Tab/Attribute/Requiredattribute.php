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

namespace Ced\GoodMarket\Block\Adminhtml\Profile\Edit\Tab\Attribute;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Requiredattributes in profile
 */
class Requiredattribute extends Widget implements RendererInterface
{
    /**
     * @var string
     */
    public $_template = 'Ced_GoodMarket::profile/attribute/required_attribute.phtml';

    /**
     * @var ObjectManagerInterface
     */
    public $_objectManager;

    /**
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * @var mixed|null
     */
    public $_profile;

    /**
     * ReqAttr constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param Registry $registry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->messageManager = $messageManager;
        $this->_profile = $this->_coreRegistry->registry('current_profile');
        parent::__construct($context, $data);
    }

    /**
     * Function _prepareLayout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData(
                [
                    'label' => __('Add Attribute'),
                    'onclick' => 'return requiredAttributeControl.addItem()',
                    'class' => 'add'
                ]
        );
        $button->setName('add_required_item_button');
        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

    /**
     * Function getAddButtonHtml
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Function getGoodMarketAttributes
     *
     * @return array
     */
    public function getGoodMarketAttributes()
    {
        $cid=$this->_backendSession->getCategoryValue();
        if (!empty($cid)) {
            $attributes=$this->_objectManager->create(\Ced\GoodMarket\Helper\Data::class)->getCategoryAttributes($cid);
            $allAttribute=json_decode($attributes['groupwise_attributes'], true);
            if (!isset($allAttribute['attribute_set_id'])) {
                $this->messageManager->addNotice(__("Please Try To Add New Profile After Sometime.."));
            }
            $this->_backendSession->setAttributeSet($allAttribute['attribute_set_id']);
            foreach ($allAttribute as $attribute) {
                if (isset($attribute['attributes'])) {
                    foreach ($attribute['attributes'] as $productattribute) {
                        $sourceData=[];
                        if (isset($productattribute['source_options']) && !empty($productattribute['source_options'])) {
                            foreach ($productattribute['source_options'] as $source_option) {
                                $sourceData[]=$source_option['label'];
                            }
                        }
                        if ($productattribute['is_required'] == '1') {
                            $requiredAttribute[$productattribute['attribute_code']] =
                                [
                                    'goodmarket_attribute_name' => $productattribute['attribute_code'],
                                    'goodmarket_attribute_type' => $productattribute['type'],
                                    'goodmarket_attribute_enum'=>!empty($sourceData)?implode(',', $sourceData):'',
                                    'required' => 1,
                                    'magento_attribute_code' => $productattribute['attribute_code']
                                ];
                        } else {
                            $optionalAttributes[$productattribute['attribute_code']] =
                                [
                                    'goodmarket_attribute_name' => $productattribute['attribute_code'],
                                    'goodmarket_attribute_type' => 'text'/*$attribute['type']*/,
                                    'goodmarket_attribute_enum'=>!empty($sourceData)?implode(',', $sourceData):'',
                                    'magento_attribute_code' => ''
                                ];
                        }
                    }
                }
            }
            $configurableAttribute=$attributes['configurable_attributes'];
//            foreach ($configurableAttribute as $conAttribute)
//            {
//                if(isset($conAttribute['options']) && !empty($conAttribute['options'])) {
//                    $options=json_decode($conAttribute['options'],true);
//                    foreach ($options as $source_option) {
//                        $sourceData[]=$source_option['label'];
//                    }
//                }
//                $requiredAttribute[$conAttribute['attribute_code']] = ['goodmarket_attribute_name' => $conAttribute['attribute_code'], 'goodmarket_attribute_type' => 'select', 'goodmarket_attribute_enum'=>!empty($sourceData)?implode(',',$sourceData):'',  'required' => 1, 'magento_attribute_code' => ''];
//            }
        } elseif (isset($this->_profile) && !empty($this->_profile)) {
            $profileData=$this->_profile->getData();
            if (isset($profileData) && !empty($profileData)) {
                $categoryLoadData = json_decode($this->_profile->getData('category_data'), true);
//                $categoryIds = array_filter($category);
//                $categoryId = end($categoryIds);
//                $attributes = $this->_objectManager->create(\Ced\GoodMarket\Helper\Data::class)->getCategoryAttributes($categoryId);
//                if(!isset($attributes['attribute_set_id'])) {
//                    $this->messageManager->addNotice(__("Please Try To Edit Profile After Sometime.."));
//                }
//                $this->_backendSession->setAttributeSet($attributes['attribute_set_id']);
//                foreach ($attributes as $attribute) {
//                    if (isset($attribute['attributes'])) {
//                        foreach ($attribute['attributes'] as $productattribute) {
//                            $sourceData=[];
//                            if(isset($productattribute['source_options']) && !empty($productattribute['source_options']))
//                            {
//                                foreach ($productattribute['source_options'] as $source_option)
//                                {
//                                    $sourceData[]=$source_option['label'];
//                                }
//                            }
//                            if ($productattribute['is_required'] == '1') {
//                                $requiredAttribute[$productattribute['attribute_code']] = ['goodmarket_attribute_name' => $productattribute['attribute_code'], 'goodmarket_attribute_type' => $productattribute['type'], 'goodmarket_attribute_enum'=>!empty($sourceData)?implode(',',$sourceData):'', 'required' => 1, 'magento_attribute_code' => $productattribute['attribute_code']];
//                            } else {
//                                $optionalAttributes[$productattribute['attribute_code']] = ['goodmarket_attribute_name' => $productattribute['attribute_code'], 'goodmarket_attribute_type' => 'text'/*$attribute['type']*/, 'goodmarket_attribute_enum'=>!empty($sourceData)?implode(',',$sourceData):'', 'magento_attribute_code' => ''];
//                            }
//                        }
//                    }
//                }
                $allAttribute=json_decode($categoryLoadData['groupwise_attributes'], true);
                if (!isset($allAttribute['attribute_set_id'])) {
                    $this->messageManager->addNotice(__("Please Try To Add New Profile After Sometime.."));
                }
                $this->_backendSession->setAttributeSet($allAttribute['attribute_set_id']);
                foreach ($allAttribute as $attribute) {
                    if (isset($attribute['attributes'])) {
                        foreach ($attribute['attributes'] as $productattribute) {
                            $sourceData=[];
                            if (isset($productattribute['source_options']) && !empty($productattribute['source_options'])) {
                                foreach ($productattribute['source_options'] as $source_option) {
                                    $sourceData[] = $source_option['label'];
                                }
                            }
                            if ($productattribute['is_required'] == '1') {
                                $requiredAttribute[$productattribute['attribute_code']] =
                                    [
                                        'goodmarket_attribute_name' => $productattribute['attribute_code'],
                                        'goodmarket_attribute_type' => $productattribute['type'],
                                        'goodmarket_attribute_enum'=>!empty($sourceData)?implode(',', $sourceData):'',
                                        'required' => 1,
                                        'magento_attribute_code' => $productattribute['attribute_code']
                                    ];
                            } else {
                                $optionalAttributes[$productattribute['attribute_code']] = ['goodmarket_attribute_name' => $productattribute['attribute_code'], 'goodmarket_attribute_type' => 'text'/*$attribute['type']*/, 'goodmarket_attribute_enum'=>!empty($sourceData)?implode(',', $sourceData):'','magento_attribute_code' => ''];
                            }
                        }
                    }
                }
                $configurableAttribute=$categoryLoadData['configurable_attributes'];
//                foreach ($configurableAttribute as $conAttribute)
//                {
//                    if(isset($conAttribute['options']) && !empty($conAttribute['options'])) {
//                        $options=json_decode($conAttribute['options'],true);
//                        foreach ($options as $source_option) {
//                            $sourceData[]=$source_option['label'];
//                        }
//                    }
//                    $requiredAttribute[$conAttribute['attribute_code']] = ['goodmarket_attribute_name' => $conAttribute['attribute_code'], 'goodmarket_attribute_type' => 'select', 'goodmarket_attribute_enum'=>!empty($sourceData)?implode(',',$sourceData):'',  'required' => 1, 'magento_attribute_code' => ''];
//                }
            } else {
                $requiredAttribute=[];
                $optionalAttributes=[];
            }
        } else {
            $requiredAttribute=[];
            $optionalAttributes=[];
        }
        $this->_backendSession->setCategoryValue('');

        $this->_goodmarketAttribute[] = [
            'label' => __('Required Attributes'),
            'value' => $requiredAttribute
        ];
        $this->_goodmarketAttribute[] = [
            'label' => __('Optional Attributes'),
            'value' => $optionalAttributes
        ];
        return $this->_goodmarketAttribute;
    }

    /**
     * Get magento Attributes.
     *
     * @return mixed
     */
    public function getMagentoAttributes()
    {
        $attributes = $this->_objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection::class)
            ->getItems();

        $mattributecode = '--please select--';
        $magentoattributeCodeArray[''] = $mattributecode;
        $magentoattributeCodeArray['default'] = "--Set Default Value--";
        foreach ($attributes as $attribute) {
            $magentoattributeCodeArray[$attribute->getAttributecode()] = $attribute->getFrontendLabel();
        }
        return $magentoattributeCodeArray;
    }

    /**
     * Get Mapped attributes
     *
     * @return array|mixed
     */
    public function getMappedAttribute()
    {
        $data = $this->_goodmarketAttribute[0]['value'];
        if ($this->_profile && $this->_profile->getId() > 0) {
            $data = json_decode($this->_profile->getProfileReqOptAttribute(), true);
            if (isset($data['required_attributes']) && isset($data['optional_attributes'])) {
                $data = array_merge($data['required_attributes'], $data['optional_attributes']);
            }
        }
        return $data;
    }

    /**
     * Render function.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
}
