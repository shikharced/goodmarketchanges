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

namespace Ced\GoodMarket\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Product Index
 */
class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /** @var \Ced\GoodMarket\Helper\Product $product */
    public $product;

    /** @var \Ced\GoodMarket\Helper\MultiAccount $multiAccountHelper */
    public $multiAccountHelper;

    /**
     * Index constructor.
     *
     * @param Action\Context $context
     * @param \Ced\GoodMarket\Helper\Product $product
     * @param \Magento\Framework\AuthorizationInterface $_authorization
     * @param \Ced\GoodMarket\Helper\MultiAccount $multiAccountHelper
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\AuthorizationInterface $_authorization,
        \Ced\GoodMarket\Helper\MultiAccount $multiAccountHelper
    ) {
        parent::__construct($context);
        $this->_authorization = $_authorization;
        $this->resultPageFactory = $resultPageFactory;
        $this->multiAccountHelper = $multiAccountHelper;
    }

    /**
     * Product list page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_GoodMarket::listing');
        $resultPage->getConfig()->getTitle()->prepend(__('GoodMarket Product Listing'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_GoodMarket::listing');
    }
}
