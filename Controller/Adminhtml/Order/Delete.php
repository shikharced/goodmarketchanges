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
 * Class Delete Order
 */
class Delete extends \Magento\Backend\App\Action
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
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Ced\GoodMarket\Model\Order $collection
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Ced\GoodMarket\Model\Order $collection)
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->orders = $collection;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
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
            if (isset($id) && !empty($id)) {
                $collection = $this
                    ->orders
                    ->getCollection()
                    ->addFieldToFilter('id', ['eq' => $id]);
            }
        }

        $response = false;
        $message = 'Order(s) deleted successfully.';
        if (isset($collection) && $collection->getSize() > 0) {
            $response = $collection->walk('delete');
        }

        if ($response) {
            $this
                ->messageManager
                ->addSuccessMessage($message);
        } else {
            $this
                ->messageManager
                ->addErrorMessage('Order(s) delete failed.');
        }

        return $this->_redirect('goodmarket/order/index');
    }
}
