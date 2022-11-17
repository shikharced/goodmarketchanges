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
 * @package     Ced_GoodMarket
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\GoodMarket\Controller\Adminhtml\Profile;
use Magento\Framework\View\Result\PageFactory;
use Ced\GoodMarket\Helper\Data;

class Updateadditionalattribute extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_coreRegistry;
    /**
     * @var Data
     */
    protected $helper;

    /**
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $c_id = $this->getRequest()->getParam('c_id');
        if (!empty($c_id)) {
            if (class_exists(\GoodMarket\GoodMarketClient::class)) {
                $response = $this->helper->ApiObject()->getTaxonomyNodeProperties(['params' => ['taxonomy_id' => (int)$c_id]]);
                if (isset($response['results'])) {
                    $result = $this->resultPageFactory->create(true)->getLayout()->createBlock('Ced\GoodMarket\Block\Adminhtml\Profile\Edit\Tab\Attribute\Additionalattribute')->setAttributeResponse($response)->toHtml();
                }
            } else {
                $result = '';
            }
            $this->getResponse()->setBody($result);
        }
    }
}
