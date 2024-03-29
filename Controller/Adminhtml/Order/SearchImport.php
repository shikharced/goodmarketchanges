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

namespace Ced\GoodMarket\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class SearchImport import by id
 */
class SearchImport extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    public $redirectFactory;

    /**
     * SearchImport constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        parent::__construct($context);
        $this->redirectFactory = $redirectFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute method.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_GoodMarket::Order');
        $resultPage->getConfig()->getTitle()->prepend(__('Search and Import'));
        return $resultPage;
    }
}
