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

use Magento\Backend\Block\Widget;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Configattribute template use
 */
class Configattribute extends Widget implements RendererInterface
{
    protected $_template = 'Ced_GoodMarket::profile/attribute/config_attribute.phtml';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var mixed|null
     */
    protected $_profile;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $json;

    /**
     * ConfigAttribute Constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\Helper\Data $json
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\Helper\Data $json,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->json = $json;
        $this->_profile = $this->_coreRegistry->registry('current_profile');
        parent::__construct($context, $data);
    }

    /**
     * Prepare Layout.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            ['label' => __('Add Attribute'), 'onclick' => 'return configAttributeControl.addItem()', 'class' => 'add']
        );
        $button->setName('add_required_item_button');
        $button->setId('config_attr');
        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve 'add group price item' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Retrieve goodmarket attributes
     *
     * @param array $subcatattribute
     * @return array
     */
    public function getGoodMarketConfigAttributes($subcatattribute = null)
    {
        $configAttribute = [];
        if (isset($subcatattribute['results']) && isset($subcatattribute['results'][0])) {
            foreach ($subcatattribute['results'] as $key => $value) {
                $enum = [];
                if (isset($value['supports_variations']) && $value['supports_variations'] == '1') {
                    // Only the Variation Attributes
                    if (isset($value['possible_values']) && !empty($value['possible_values'])) {
                        foreach ($value['possible_values'] as $key2 => $enumvalue) {
                            $enum[] = (str_replace("'", "", $enumvalue['value_id']) . ':' . str_replace("'", "", $enumvalue['name']));
                        }
                        $type = 'select';
                        $enumjson = json_encode($enum);
                    } else {
                        $type = 'text';
                        $enumjson = '';
                    }
                    $this->_goodmarketAttribute[str_replace("'", "", $value['name'])] =
                        str_replace("'", "", $value['name']);
                    $temp = [];
                    $temp['goodmarket_attribute_name'] = str_replace("'", "", $value['name']) ;
                    $temp['magento_attribute_code'] = '';
                    $temp['goodmarket_attribute_type'] = $type;
                    $temp['property_id'] = $value['property_id'];
                    $temp['goodmarket_enum'] = $enumjson;
                    $temp['default'] = isset($value['default']) ? $value['default']: '';
                    $temp['option_values'] = '';
                    $temp['required'] = 0;
                    $configAttribute[str_replace("'", "", $value['name'])] = $temp;
                }
            }
        }
        if ($configAttribute) {
            $this->_goodmarketAttribute = $configAttribute;
        } else {
            $this->_goodmarketAttribute = $this->customFun();
        }
        return $this->_goodmarketAttribute;
    }

    /**
     * Retrieve magento attributes
     *
     * @param int|null $groupId  return name by customer group id
     * @return array|string
     */
    public function getMagentoAttributes()
    {
        $attributes = $this->_objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection::class)
            ->addFieldToFilter('frontend_input', ['in' => ['select', 'multiselect']]);

        $magentoattributeCodeArray = [];
        foreach ($attributes as $attribute) {
            $type = "";
            $optionValues = "";
            $attributeOptions = $attribute->getSource()->getAllOptions(false);
            if (!empty($attributeOptions) && is_array($attributeOptions)) {
                $type = " [ select ]";
                foreach ($attributeOptions as &$option) {
                    if (isset($option['label']) && is_object($option['label'])) {
                        $option['label'] = $option['label']->getText();
                    }
                }
                $attributeOptions = str_replace('\'', '&#39;', $this->json->jsonEncode($attributeOptions));
                $optionValues = addslashes($attributeOptions);
            }
            $mattributecode = '--please select--';

            $magentoattributeCodeArray[''] =
                [
                    'attribute_code' => $mattributecode,
                    'attribute_type' => '',
                    'input_type' => '',
                    'option_values' => ''
                ];
            $magentoattributeCodeArray['default'] =
                [
                    'attribute_code' =>"-- Set Default Value --",
                    'attribute_type' => '',
                    'input_type' => 'select',
                    'option_values' => ''
                ];
            if ($attribute->getFrontendInput() =='select' && $optionValues) {
                $magentoattributeCodeArray[$attribute->getAttributecode()] =
                    [
                        'attribute_code' => $attribute->getAttributecode(),
                        'attribute_type' => $attribute->getFrontendInput(),
                        'input_type' => 'select',
                        'option_values' => $optionValues,
                    ];
            } else {
                $magentoattributeCodeArray[$attribute->getAttributecode()] =
                    [
                        'attribute_code' => $attribute->getAttributecode(),
                        'attribute_type' => $attribute->getFrontendInput(),
                        'input_type' => 'select',
                        'option_values' => $optionValues,
                    ];
            }
        }
        return $magentoattributeCodeArray;
    }

    /**
     * Custom Function.
     *
     * @return array
     */
    public function customFun()
    {
        if ($this->_profile->getData()) {
            $configAttribute = [];
            $profile_category = $this->_profile->getData('profile_category');
            $profile_category = json_decode($profile_category, true);
            $profile_category = array_filter($profile_category);
            try {
                $c_id = isset($profile_category[count($profile_category)-1]) ?$profile_category[count($profile_category)-1] :'';
                if (class_exists(\GoodMarket\GoodMarketClient::class)) {
                    $subcatattribute = $this->_objectManager->create(\Ced\GoodMarket\Helper\Data::class)
                        ->ApiObject()
                        ->getTaxonomyNodeProperties(['params' => ['taxonomy_id' => (int)$c_id]]);
                } else {
                    $subcatattribute = [];
                }
                $configAttribute = [];
                if (isset($subcatattribute['results']) && isset($subcatattribute['results'][0])) {
                    foreach ($subcatattribute['results'] as $key => $value) {
                        $enum = [];
                        if (isset($value['supports_variations']) && $value['supports_variations'] == '1') {
                            // Only the Variation Attributes
                            if (isset($value['possible_values']) && !empty($value['possible_values'])) {
                                foreach ($value['possible_values'] as $key2 => $enumvalue) {
                                    $enum[] = (str_replace("'", "", $enumvalue['value_id']) . ':' . str_replace("'", "", $enumvalue['name']));
                                }
                                $type = 'select';
                                $enumjson = json_encode($enum);
                            } else {
                                $type = 'text';
                                $enumjson = '';
                            }
                            $this->_goodmarketAttribute[str_replace("'", "", $value['name'])] =
                                str_replace("'", "", $value['name']);
                            $temp = [];
                            $temp['goodmarket_attribute_name'] =str_replace("'", "", $value['name']) ;
                            $temp['magento_attribute_code'] = '';
                            $temp['goodmarket_attribute_type'] = $type;
                            $temp['property_id'] = $value['property_id'];
                            $temp['goodmarket_enum'] = $enumjson;
                            $temp['default']=isset($value['default'])?$value['default']:'';
                            $temp['option_values'] = '';
                            $temp['required'] = 0;
                            $configAttribute[str_replace("'", "", $value['name'])] = $temp;
                        }
                    }
                }
                $this->_goodmarketAttribute = $configAttribute;

            } catch (\Exception $exception) {
                return [];
            }
        } else {
            return [];
        }
        return $this->_goodmarketAttribute;
    }

    /**
     * Good market attributes mapping
     *
     * @param $subcatattribute
     * @return array|array[]
     */
    public function getGoodMarketAttributeValuesMapping($subcatattribute = null)
    {

        $data = [];
        if ($this->_profile && $this->_profile->getId()>0) {
            $configdata = json_decode($this->_profile->getConfigAttributes(), true);
            if (is_array($configdata)) {
                foreach ($configdata as $key => $value) {
                    $temp = [];
                    $temp['goodmarket_attribute_name'] = $value['goodmarket_attribute_name'];
                    $temp['magento_attribute_code'] = $value['magento_attribute_code'];
                    $temp['goodmarket_attribute_type'] = $value['goodmarket_attribute_type'];
                    $temp['property_id'] = $value['goodmarket_property_id'];
                    $temp['goodmarket_enum'] = isset($value['goodmarket_enum_val'])?$value['goodmarket_enum_val']:'{}';
                    $temp['default']=isset($value['default'])?$value['default']:'';
                    $temp['option_values'] = $value['option_mapping'];
                    $temp['required'] = 0;
                    $configAttribute[$value['goodmarket_attribute_name']] = $temp;
                }
                $data=['profile_id'=>$this->_profile->getId(),'data'=>$configAttribute];

            }
        } else {
            if (!$this->_goodmarketAttribute) {
                if (isset($subcatattribute['results'])) {
                    $this->_goodmarketAttribute = $this->getGoodMarketConfigAttributes($subcatattribute);
                }
            }
            foreach ($this->_goodmarketAttribute as $key => $value) {
                if (isset($value['magento_attribute_code'])) {
                    $data[] = $value;
                }
            }
            $data=['data'=>$data];
        }
        return $data;
    }

    /**
     * Render function
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
}
