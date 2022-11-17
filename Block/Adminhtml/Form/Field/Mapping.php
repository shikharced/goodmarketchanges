<?php
namespace Ced\GoodMarket\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Ced\GoodMarket\Block\Adminhtml\Form\Field\GoodMarketAttibute;
use Ced\GoodMarket\Block\Adminhtml\Form\Field\MagentoAttribute;

/**
 * Class Ranges
 */
class Mapping extends AbstractFieldArray
{
    /**
     * @var TaxColumn
     */
    private $goodMarketAttibute;
    private $magentoAttribute;

    protected $_template = 'Ced_GoodMarket::system/config/form/field/array.phtml';
    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
//        $this->addColumn('from_qty', ['label' => __('From'), 'class' => 'required-entry']);
//        $this->addColumn('to_qty', ['label' => __('To'), 'class' => 'required-entry']);
//        $this->addColumn('price', ['label' => __('Price'), 'class' => 'required-entry']);
        $this->addColumn('GoodMarket', [
            'label' => __('GoodMarket Varient Attribute'),
            'renderer' => $this->getGoodMarketAttribute()
        ]);
        $this->addColumn('Magento', [
            'label' => __('Magento Attribute'),
            'renderer' => $this->getMagentoAttribute()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $tax = $row->getTax();
        if ($tax !== null) {
            $options['option_' . $this->getTaxRenderer()->calcOptionHash($tax)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return TaxColumn
     * @throws LocalizedException
     */
    private function getGoodMarketAttribute()
    {
        if (!$this->goodMarketAttibute) {
            $this->goodMarketAttibute = $this->getLayout()->createBlock(
                GoodMarketAttibute::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->goodMarketAttibute;
    }

    private function getMagentoAttribute()
    {
        if (!$this->magentoAttribute) {
            $this->magentoAttribute = $this->getLayout()->createBlock(
                MagentoAttribute::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->magentoAttribute;
    }
}
