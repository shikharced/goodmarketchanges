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

namespace Ced\GoodMarket\Block\Adminhtml\Product;

/**
 * Massdelete to delete product
 */
class MassDelete extends \Magento\Backend\Block\Widget\Container
{
    /**
     * MassUpload constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->setTemplate('Ced_GoodMarket::product/massdelete.phtml');
    }

    /**
     * totalcount
     *
     * @return int
     */
    public function totalcount()
    {
        $totalChunk = $this->_backendSession->getProductDeleteChunks();
        return count($totalChunk);
    }
}
