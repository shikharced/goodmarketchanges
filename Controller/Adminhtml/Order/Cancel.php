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
 * Class Delete Goodmarket Order
 */
class Cancel extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;

    /**
     * @var \Ced\GoodMarket\Model\Order
     */
    public $orders;

    /**
     * Cancel Construct.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Ced\GoodMarket\Model\Order $collection
     * @param \Ced\GoodMarket\Helper\Config $config
     * @param \Ced\GoodMarket\Model\ResourceModel\Order\CollectionFactory $orderfactory
     * @param \Ced\GoodMarket\Helper\Order $order
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Ced\GoodMarket\Model\Order $collection,
        \Ced\GoodMarket\Helper\Config $config,
        \Ced\GoodMarket\Model\ResourceModel\Order\CollectionFactory $orderfactory,
        \Ced\GoodMarket\Helper\Order $order
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->orders = $collection;
        $this->config = $config;
        $this->order = $order;
        $this->orderfactory = $orderfactory;
    }

    /**
     * Execute methode
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $dataPost = $this->getRequest()->getParam('filters');
        if ($dataPost) {
            $goodmarketOrdersModelIds = $this->filter->getCollection($this->orderfactory->create())->getAllIds();
        } else {
            $goodmarketOrdersModelIds[] = $this->getRequest()->getParam('id');
        }
        $isFilter = $this->getRequest()
            ->getParam('filters');
        if (isset($isFilter)) {
            $collection = $this
                ->filter
                ->getCollection($this
                    ->orders
                    ->getCollection());
        } else {
            $id = $this->getRequest()
                ->getParam('id');
            if (isset($id) and !empty($id)) {
                $collection = $this
                    ->orders
                    ->getCollection()
                    ->addFieldToFilter('id', ['eq' => $id]);
            }
        }
        $orders = $collection->getData();
        $count = 0;
        if (!empty($this
            ->config
            ->getCancellationCode())) {
            foreach ($orders as $order) {
                $orderItems=json_decode($order['order_data'], true);
                $count=0;
                foreach ($orderItems['orderItems'] as $value) {
                    $shipmentData['orderItems'][$count]['orderItemId'] = $value['orderItemId'];
                    $shipmentData['orderItems'][$count]['reasonCode']=$this->config->getCancellationCode();
                    $count++;
                }
                $cancelResponse=$this->_objectManager->create(\Ced\GoodMarket\Helper\Data::class)
                    ->cancelOrder($shipmentData);
                $goodmarketOrder = $this->objectManager->create(\Ced\GoodMarket\Model\Order::class)
                    ->load($order['magento_increment_id'], 'magento_increment_id');
                if ($cancelResponse['status']=='SUCCESS') {
                    $goodmarketOrder->setStatus('Cancelled');
                    $goodmarketOrder->save();
                }
            }
        } else {
            $this
                ->messageManager
                ->addErrorMessage('Please Select Order Cancellation Code');
        }
        return $this->_redirect('goodmarket/order/index');
    }
}
