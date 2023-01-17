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

/**
 * Class Import orders
 */
class Import extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    public $resultRedirectFactory;

    /**
     * @var \Ced\GoodMarket\Helper\Order
     */
    public $order;

    /**
     * Fetch constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Ced\GoodMarket\Helper\Order $orderHelper
     * @param \Ced\GoodMarket\Helper\Data $data
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Ced\GoodMarket\Helper\Order $orderHelper,
        \Ced\GoodMarket\Helper\Data $data
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->order = $orderHelper;
        $this->data=$data;
        parent::__construct($context);
    }

    /**
     * Execute method
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this
            ->resultRedirectFactory
            ->create();
        $credentials=$this->data->checkAccountSetup();
        if ($credentials!=1) {
            $this
                ->messageManager
                ->addNoticeMessage($credentials);
            $result->setPath('goodmarket/order/index');
            return $result;
        }
        $status = $this
            ->order
            ->import();
        if ($status) {
            $this
                ->messageManager
                ->addSuccessMessage((string)$status . ' New orders imported from GoodMarket.');
        } else {
            $this
                ->messageManager
                ->addNoticeMessage('No new orders are imported.');
        }

        $result->setPath('goodmarket/order/index');
        return $result;
    }
}
