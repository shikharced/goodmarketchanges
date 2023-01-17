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
 * @package   Ced_EbayMultiAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Block\Adminhtml\Config\Field;

/**
 * Class CarrierMapping Field
 */
class CarrierMapping extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * _prepareToRender
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'magento_carrier',
            [
                'label' => __('Magento Carrier'),
                // 'renderer' => /*$this->_getMagentoCarrierRenderer()*/'',
            ]
        );
        $this->addColumn(
            'goodmarket_carrier',
            [
                'label' => __('GoodMarket Transporter Code'),
                // 'renderer' => /*$this->_getEbayMultiAccountCarrierRenderer()*/'',
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Carrier');
    }
}
