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
 * @category  Ced
 * @package   Ced_GoodMarket
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;

class MassDelete extends \Magento\Backend\App\Action
{
    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\Product\ActionFactory $productActionFactory
    ) {
        parent::__construct($context);
        $this->productCollection = $productCollection;
        $this->productActionFactory = $productActionFactory;
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $proIds = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded', false);
        if (!is_array($proIds) && !$excluded) {
            $this->messageManager->addErrorMessage(__('Please select Profile(s).'));
        } else if ($excluded == "false") {
            $proIds = $this->_objectManager->create('Ced\GoodMarket\Model\Profile')->getCollection()->getAllIds();
        }

        if (!empty($proIds)) {
            try {
                foreach ($proIds as $profileId) {
                    $profile = $this->_objectManager->create('Ced\GoodMarket\Model\Profile')->load($profileId);
                    $profile->delete();
                    $this->unLinkProduct($profileId);
                }
                $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been deleted.', count($proIds)));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    private function unLinkProduct($profileId)
    {
        $oldIds = $this->productCollection->create()
            ->addAttributeToFilter('goodmarket_profile_id', ['eq' => $profileId])
            ->getAllIds();
        if (!empty($oldIds)) {
            $this->productActionFactory->create()
                ->updateAttributes($oldIds, ['goodmarket_profile_id' => ''], 0);
        }
    }
}
