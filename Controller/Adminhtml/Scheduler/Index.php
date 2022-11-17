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

namespace Ced\GoodMarket\Controller\Adminhtml\Scheduler;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Product
 * @package Ced\GoodMarket\Controller\Adminhtml\Product
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
    public $multiAccountHelper ;

    /**
     * Index constructor.
     * @param Action\Context $context
     * @param \Ced\GoodMarket\Helper\Product $product
     * @param PageFactory $resultPageFactory
     * @param \Ced\GoodMarket\Helper\MultiAccount $multiAccountHelper
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
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
        // $resultPage->setActiveMenu('Ced_GoodMarket::product');
        $resultPage->getConfig()->getTitle()->prepend(__('Bulk Scheduler Grid'));
        return $resultPage;
    }
}
