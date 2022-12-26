<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsGroup
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\GoodMarket\Controller\Adminhtml\Profile;

use Ced\GoodMarket\Helper\Data;
use Magento\Framework\View\Result\PageFactory;

/**
 * To Update category attributes
 */
class Updatecategoryattribute extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_coreRegistry;
    /**
     * @var Data
     */
    public $helper;

    /**
     * Update Category Attr Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory,
        Data $helper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
    }

    /**
     * Execute methode
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $c_id = $this->getRequest()->getParam('c_id');
        if (!empty($c_id)) {
            $this->_session->setCategoryValue($c_id);
            $response = $this->helper->getCategoryAttributes($c_id);
            if (isset($response)) {
                $result = $this->resultPageFactory->create(true)->getLayout()->createBlock(\Ced\GoodMarket\Block\Adminhtml\Profile\Edit\Tab\Attribute\Requiredattribute::class)
                    ->setAttributeResponse($response)->toHtml();
            }

            $this->getResponse()->setBody($result);
        }
    }
}
