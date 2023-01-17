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

use Ced\GoodMarket\Model\Data;

/**
 * MassEnable Constructor
 */
class MassEnable extends \Magento\Backend\App\Action
{
    /**
     * MassENable Index
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $profIds = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded', false);
        if (!is_array($profIds) && !$excluded) {
            $this->messageManager->addErrorMessage(__('Please select Profile(s).'));
        } elseif ($excluded == "false") {
            $profIds  = $this->_objectManager->create(\Ced\GoodMarket\Model\Profile::class)
                ->getCollection()->getAllIds();
        }
        if (!empty($profIds)) {
            try {
                foreach ($profIds as $profileId) {
                    $profile = $this->_objectManager->create(\Ced\GoodMarket\Model\Profile::class)
                        ->load($profileId);
                    $profile->setProfileStatus(1);
                    $profile->save();
                }
                $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been enabled.', count($profIds)));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
