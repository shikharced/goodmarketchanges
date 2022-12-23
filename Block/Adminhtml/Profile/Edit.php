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
namespace Ced\GoodMarket\Block\Adminhtml\Profile;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class Edit block
 */
class Edit extends Container
{
    /**
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Edit Construct.
     *
     * @return void
     */
    protected function _construct() {
        $this->_objectId = 'profile_id';
        $this->_blockGroup = 'ced_goodMarket';
        $this->_controller = 'adminhtml_profile';

        parent::_construct();

        $this->updateButton('save', 'label', __('Save'));
        $this->updateButton(
            'save',
            'onclick',
            'saveAndContinueEdit(\''.$this->getSaveUrl().'\',false)'
        );

        $this->addButton(
            'delete',
            [
                'label' => __('Delete'),
                'class' => 'delete',
                'onclick' => 'deleteConfirm(\'' . __('Are you sure you want to delete this?')
                    . '\', \'' . $this->getDeleteUrl() . '\')',
                'area' => 'adminhtml'
            ],
            -1
        );

        $this->addButton(
            'save_and_edit_button',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'onclick' => 'saveAndContinueEdit(\'' . $this->getSaveAndContinueUrl('edit') . '\',true)',
            ]
        );

        $this->_formScripts[] = "
            function saveAndContinueEdit(urlTemplate,flag) {
                groupVendorPpcode_massaction = document.getElementById('groupVendorPpcode_massaction-form');
                if(groupVendorPpcode_massaction != null) {
                    groupVendorPpcode_massaction.parentElement.removeChild(groupVendorPpcode_massaction);
                     new Insertion.Bottom('edit_form',
                     groupVendorPpcode_massactionJsObject.fieldTemplate(
                     {name: 'in_profile_products',
                     value: groupVendorPpcode_massactionJsObject.checkedString}));
                 }
            if(flag) {
                    var editForm = jQuery('#edit_form');
                    editForm.attr('action', urlTemplate);
                    editForm.submit();
                }
            }
        ";
    }

    /**
     * Add Buttons.
     *
     * @param $buttonId
     * @param $data
     * @param $level
     * @param $sortOrder
     * @param $region
     * @return Edit|void
     */
    public function addButton($buttonId, $data, $level = 0, $sortOrder = 0, $region = 'toolbar')
    {
        if ($this->getRequest()->getParam('popup')) {
            $region = 'header';
        }
        parent::addButton($buttonId, $data, $level, $sortOrder, $region);
    }

    /**
     * Save And continue Url
     *
     * @param $back
     * @return string
     */
    public function getSaveAndContinueUrl($back)
    {
        $profile = $this->_coreRegistry->registry('current_profile');
        return $this->getUrl(
            '*/*/save',
            [
                '_current' => true,
                'back' => $back,
                'active_tab' => null,
                'pcode' =>  $this->getRequest()->getParam('pcode', false),
                'website' => $this->getRequest()->getParam('website', false),
            ]
        );

    }

    /**
     * Get Header Text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('profile_data') && $this->_coreRegistry->registry('profile_data')->getId()) {
            return __('Edit Profile "%s" ', $this->escapeHtml($this->_coreRegistry->registry('profile_data')->getName()));
        } else {
            return __('Add Profile');
        }
    }

    /**
     * Get Save Url function
     *
     * @return string
     */
    public function getSaveUrl()
    {
        $profile = $this->_coreRegistry->registry('current_profile');
        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => null, 'pcode' =>  $this->getRequest()->getParam('pcode', false)]
        );
    }

    /**
     * Get Delete Url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/delete',
            ['back' => null, 'pcode' => $this->getRequest()->getParam('pcode')]
        );
    }
}
