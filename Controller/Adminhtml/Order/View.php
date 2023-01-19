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
 * Class View Orders
 */
class View extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;

    /**
     * @var \Ced\GoodMarket\Helper\Product
     */
    public $goodmarketHelper;

    /**
     * @var \Ced\GoodMarket\Model\Order
     */
    public $orders;

    /**
     * Json Factory
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * View constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Ced\GoodMarket\Model\Order $orders
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Ced\GoodMarket\Model\Order $orders
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->filter = $filter;
        $this->orders = $orders;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $order = [
            'order' => [],
            'order_items' => []
        ];

        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {
            $goodmarketOrder = $this->orders->load($id);
            if ($goodmarketOrder && $goodmarketOrder->getId()) {
                $orderData = $goodmarketOrder->getOrderData();
                if ($orderData != '') {
                    $order['order'] = json_decode($orderData);
                }
                $orderItems = $goodmarketOrder->getOrderItems();
                if ($orderItems != '') {
                    $order['order_items'] = json_decode($orderItems);
                }
            }
        }
        return $this->resultJsonFactory
            ->create()
            ->setData($order);
    }
}
