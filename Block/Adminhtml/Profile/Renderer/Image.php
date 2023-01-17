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

namespace Ced\GoodMarket\Block\Adminhtml\Profile\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * Image COnstructor.
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
    }

    /**
     * Public function Render.
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($this->_getValue($row) != '') {
            $path = $this->_getValue($row);
            $imageUrl = $mediaDirectory .'catalog/product'.$path;
//            echo "<pre>";
//            print_r($path);
//            echo "<pre>";
//            print_r($imageUrl);
//            die(__FILE__);
            $img = '<img src="'.$imageUrl.'" width="65" height="60" alt = "'.$path.'"/>';

        } else {
            $img = '<img src="#" width="65" height="60" alt="no-image"/>';
        }
        return $img;
    }
}
