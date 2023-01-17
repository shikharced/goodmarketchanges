<?php

namespace Ced\GoodMarket\Block\Adminhtml\System\Config;

/**
 * Install class Block
 */
class Install extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'Ced_GoodMarket::system/config/install.phtml';

    /**
     * Render function
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get Ajax Url.
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('goodmarket/config/save', ['form_key' => $this->getFormKey()]);
    }

    /**
     * Get Button Html
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id' => 'goodmarket-install',
                'label' => __('Fetch Token'),
                'class' => 'action-secondary scalable',
                'style' => 'float: right'
            ]
        );

        return $button->toHtml();
    }

    /**
     * Get Html Element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
