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
namespace Ced\GoodMarket\Block\Adminhtml\Profile\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

/**
 * Class OtherDetails
 * @package Ced\GoodMarket\Block\Adminhtml\Profile\Edit\Tab
 */
class OtherDetails extends Generic
{
    /**
     * @var Status
     */
    public $status;

    /**
     * Info constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Recipient $recipient
     * @param Recipient $recipient
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $profile = $this->_coreRegistry->registry('current_profile');
      
        $fieldset = $form->addFieldset(
            'other_details',
            [
                'legend'=>__('Other Details')
            ]
        );

        $fieldset->addField(
            'personalization_instructions',
            'textarea',
            [
                'name'      => "personalization_instructions",
                'label'     => __('Personalization Instructions'),
                'note'      => __('Enter the personalisation instructions you want buyers to see.'),
                'value'     => $profile->getData('personalization_instructions')
            ]
        );

        $fieldset->addField(
            'styles',
            'text',
            [
                'name'      => "styles",
                'label'     => __('Styles'),
                'note'      => __('Specify The Styles With "," Separation'),
                'value'     => $profile->getData('styles'),
            ]
        );

        $fieldset->addField(
            'tags',
            'text',
            [
                'name'      => "tags",
                'label'     => __('Tags'),
                'note'      => __('Specify The Tags With "," Separation'),
                'value'     => $profile->getData('tags'),
            ]
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
