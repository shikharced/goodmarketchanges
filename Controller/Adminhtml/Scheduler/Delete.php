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

/**
 * Class Delete Scheduler
 */
class Delete extends \Magento\Customer\Controller\Adminhtml\Group
{
    /**
     * Execute method
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($code) {
            $model = $this->_objectManager->create(\Ced\GoodMarket\Model\Scheduler::class)
                ->getCollection()->addFieldToFilter('id', $code)
                ->addFieldToFilter('scheduler_product_sync', 'Finished');
            // entity type check
            if (empty($model->getData())) {
                $this->messageManager->addErrorMessage(__('Feed Status Not Finished Yet!!'));
            }
            foreach ($model as $value) {
                if ($code == $value->getData('id')) {
                    $value->delete();
                    $this->messageManager->addSuccessMessage(__('You deleted the Feed.'));
                }
            }
        }
        $this->_redirect('goodmarket/scheduler/index');
    }
}
