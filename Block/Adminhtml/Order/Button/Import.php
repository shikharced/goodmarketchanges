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
 * @category    Ced
 * @package     Ced_GoodMarket
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Block\Adminhtml\Order\Button;

use Magento\Backend\Block\Widget\Container;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Import extends Container implements ButtonProviderInterface
{
    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Import'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'add',
            'sort_order' => 10
        ];
    }

    public function getBackUrl()
    {
        return $this->getUrl('goodmarket/order/import');
    }
}
