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
namespace Ced\GoodMarket\Block\Adminhtml\Profile\Edit\Tab;

use Magento\Framework\Data\Form as DataForm;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\AttributeSet\Options;

class Products extends Extended
{
    /**
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * @var ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var Type
     */
    public $type;

    /**
     * @var Status
     */
    public $status;

    /**
     * @var Options
     */
    public $option;

    /**
     * @var Profileproducts
     */
    public $profileproducts;

    protected $_massactionBlockName = 'Magento\Backend\Block\Widget\Grid\Massaction\Extended';

    /**
     * Products constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param ObjectManagerInterface $objectInterface
     * @param Registry $registry
     * @param Type $type
     * @param Status $status
     * @param Options $options
     * @param Profileproducts $profileproducts
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        ObjectManagerInterface $objectInterface,
        Registry $registry,
        Type $type,
        Status $status,
        Options $options
    )
    {
        $this->_coreRegistry = $registry;
        $this->objectManager = $objectInterface;
        $this->type = $type;
        $this->status = $status;
        $this->option = $options;
        parent::__construct($context, $backendHelper);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('asc');
        $this->setId('groupVendorPpcode');
        $this->_massactionBlockName = 'Ced\GoodMarket\Block\Adminhtml\Profile\Widget\Grid\Massaction\Extended';
        $this->setDefaultFilter(['massaction' => 1]);
        $this->setUseAjax(true);

    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    public function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'massaction') {
            $inProfileIds = $this->getProducts();
            $inProfileIds = array_filter($inProfileIds);
            if (empty($inProfileIds)) {
                $inProfileIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $inProfileIds]);
            } else {
                if ($inProfileIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $inProfileIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $profileCode = $this->getRequest()->getParam('id');

        $this->_coreRegistry->register('PCODE', $profileCode);

        $collection = $this->objectManager->create('Magento\Catalog\Model\Product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('visibility', ['neq' => 1])
            ->addAttributeToFilter('type_id', ['simple', 'configurable']);
//        ->addAttributeToFilter('goodmarket_profile_id', $profileCode);
//        echo "<pre>";
//        print_r($collection->getData());
//        die(__FILE__);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            ['header' => __('Product Id'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'entity_id',
                'filter_index' => 'entity_id',
                'type' => 'number',]
        );

        $this->addColumn(
            'image',
            array(
                'header' => __('Image'),
                'index' => 'image',
                'renderer'  => 'Ced\GoodMarket\Block\Adminhtml\Profile\Renderer\Image',
                'filter' => false,
                'sortable'  => false
            )
        );

        $this->addColumn(
            'name',
            ['header' => __('Product Name'),
                'align' => 'left',
                'type' => 'text',
                'index' => 'name',
                'filter_index' => 'name',]
        );
        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'align' => 'left',
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->type->getOptionArray(),
                'header_css_class' => 'col-group',
                'column_css_class' => 'col-group'
            ]
        );

        $this->addColumn(
            'category',
            [
                'header'    => __('Category'),
                'index'     => 'category',
                'sortable'  => false,
                'type'  => 'options',
                'options'   => $this->objectManager->create('Ced\GoodMarket\Model\Source\Category')->getAllOptions(),
                'renderer'  => 'Ced\GoodMarket\Block\Adminhtml\Profile\Renderer\Category',
                'filter_condition_callback' => [$this, 'filterCategory'],
            ]
        );

        $this->addColumn(
            'status',
            ['header' => __('Product Status'),
                'align' => 'left',
                'index' => 'status',
                'filter_index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray(),]
        );

        $attributeSet = $this->option->toOptionArray();
        $values = [];
        foreach ($attributeSet as $val) {
            $values[$val['value']] = $val['label'];
        }

        $this->addColumn(
            'set_name',
            ['header' => __('Attrib. Set Name'),
                'align' => 'left',
                'index' => 'attribute_set_id',
                'filter_index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $values,]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'align' => 'left',
                'type' => 'text',
                'index' => 'sku',
                'filter_index' => 'sku',]
        );

        $store = $this->_storeManager->getStore();
        $this->addColumn(
            'price',
            ['header' => __('Price'),
                'align' => 'left',
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'filter_index' => 'price',]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/editProfileProductGrid', ['_secure' => true, '_current' => true]);

    }

    /**
     * @param bool $json
     * @return array|string
     */
    public function getProducts($json = false)
    {
        if ( $this->getRequest()->getPost('in_profile_products') != "" ) {
            return $this->getRequest()->getPost('in_profile_products');
        }
        $this->getRequest()->getParam('pcode');
        $profileId = false;
        $profile = $this->_coreRegistry->registry('current_profile');

        if ($profile && $profile->getId()) {
            $profileId = $profile->getId();
        }

        $products = [];//$this->profileproducts->getProfileProducts($profileId);
        if (sizeof($products) > 0) {
            if ($json) {
                $jsonProducts = [];
                foreach ($products as $productId) {
                    $jsonProducts[$productId] = 0;
                }
                return json_encode($jsonProducts);
            } else {
                return array_values($products);
            }
        } else {
            if ($json) {
                return '{}';
            } else {
                return [];
            }
        }
    }

    /**
     * @param $string
     * @return bool
     */
    public function isPartUppercase($string)
    {
        return (bool)preg_match('/[A-Z]/', $string);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if ($products === null) {
            //$products = $this->getCategory()->getProductsPosition();
            //return array_keys($products);
        }
        return $products;
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id[]');

        $this->getMassactionBlock()->addItem(
            'addproduct', array(
                'label' => __('Add Products'),
                'url' => $this->getUrl('goodmarket/profile/save'),
            )
        );
        return $this;
    }

    public function filterCategory($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $_category = $this->objectManager->create(\Magento\Catalog\Model\Category::class)->load($value);
        $collection->addCategoryFilter($_category);
        return $collection;
    }
}