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

namespace Ced\GoodMarket\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Truncate Feed
 */
class Truncate extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $logs
     */
    public $logs;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    public $fileIo;

    /**
     * Delete constructor.
     *
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $logs
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        \Ced\GoodMarket\Model\ResourceModel\Feed\CollectionFactory $logs
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->logs = $logs;
    }

    /**
     * Truncate Execute method
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $status = false;
        $collection = $this->logs->create();
        if (isset($collection) and $collection->getSize() > 0) {
            $status = true;
            $collection->walk('delete');
        }
        if ($status) {
            $this->messageManager->addSuccessMessage('Entries deleted successfully.');
        } else {
            $this->messageManager->addNoticeMessage('No Entry to delete.');
        }
        $this->_redirect('goodmarket/feed/index');
    }

    /**
     * IsALLowed
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_GoodMarket::GoodMarket');
    }
}
