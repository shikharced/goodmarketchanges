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
namespace Ced\GoodMarket\Block\Adminhtml\Config;

use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class FetchCategory to fetch cats
 */
class FetchCategory extends Field
{
    /**
     * _prepareLayout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('config/fetchcategory.phtml');
        }
        return $this;
    }

    /**
     * render
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
     * _getElementHtml
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = $originalData['button_label'];
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('goodmarket/config/fetchCategory'),
            ]
        );
        return $this->_toHtml();
    }
}
