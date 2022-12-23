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
use Magento\Framework\FlagManager;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class GoodMarketSource used to get all carriers of vidaxl
 */
class GoodMarketSource extends Select
{
    public const FLAG_CODE = 'CED_GOODMARKET_SOURCE';

    /**
     * GoodMarketSource Constructor
     *
     * @param Context $context
     * @param FlagManager $flagManager
     */
    public function __construct(
        Context $context,
        FlagManager $flagManager
    ) {
        $this->flagManager = $flagManager;
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
     * Returns All carrier names
     *
     * @return array|\string[][]
     */
    private function getSourceOptions(): array
    {
        $goodMarket = $this->flagManager->getFlagData(self::FLAG_CODE);
        $sourceList = json_decode($goodMarket, true);
        $arrar = [];
//        echo '<pre>'; print_r($goodMarket); exit;
        if (!empty($sourceList)) {
            foreach ($sourceList as $source) {
                $arrar[] = ["label" => $source['name'], "value" => $source['source_code']."+".$source['name']];
            }
        }
        return $arrar;
    }
}
