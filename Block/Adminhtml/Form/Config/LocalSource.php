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
 * @package   Ced_VidaxlDropshipping
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */


declare(strict_types=1);

namespace Ced\GoodMarket\Block\Adminhtml\Form\Config;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ShippingCarrier get shipping methods name
 */
class LocalSource extends Select
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * ShippingCarrier constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
    ) {
        $this->_storeManager = $storeManager;
        $this->sourceRepository = $sourceRepository;
        parent::__construct($context);
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    /**
     * Get all magento's carrier
     *
     * @return array
     */
    private function getSourceOptions(): array
    {
        $arrar = [];
        $sourceData = $this->sourceRepository->getList();
        $sourceList = $sourceData->getItems();
        foreach ($sourceList as $source) {
            $arrar[] = ["label" => $source->getData('name'), "value" => $source->getData('source_code')];
        }
        return $arrar;
    }
}
