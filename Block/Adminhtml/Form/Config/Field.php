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

namespace Ced\GoodMarket\Block\Adminhtml\Form\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Ced\GoodMarket\Block\Adminhtml\Form\Config\GoodmarketSource;
use Ced\GoodMarket\Block\Adminhtml\Form\Config\LocalSource;

/**
 * Class Field to get all shipping carriers to show in frontend
 */
class Field extends AbstractFieldArray
{
    /**
     * @var GoodmarketSource
     */
    private $goodMarketSource;

    /**
     * @var LocalSource
     */
    private $localSource;

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {

        $this->addColumn('local_inventory_code', [
            'label' => __('Local Inventory Code'),
            'renderer' => $this->getLocalSource()
        ]);
        $this->addColumn('good_market_source', [
            'label' => __('GoodMarket Inventory Code'),
            'renderer' => $this->goodmarketSource()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Inventory Mapping');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $gdMarketSrc = $row->getGoodMarketSource();
        if ($gdMarketSrc !== null) {
            $options['option_' . $this->goodmarketSource()->calcOptionHash($gdMarketSrc)] = 'selected="selected"';
        }

        $localsource = $row->getLocalInventoryCode();
        if ($localsource !== null) {
            $options[
            'option_' . $this->getLocalSource()->calcOptionHash($localsource)
            ] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Get Magento shipping caarriers
     *
     * @return GoodmarketSource
     * @throws LocalizedException
     */
    private function goodmarketSource()
    {
        if (!$this->goodMarketSource) {
            $this->goodMarketSource = $this->getLayout()->createBlock(
                GoodmarketSource::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->goodMarketSource;
    }

    /**
     * Get Magento shipping caarriers
     *
     * @return LocalSource
     * @throws LocalizedException
     */
    private function getLocalSource()
    {
        if (!$this->localSource) {
            $this->localSource = $this->getLayout()->createBlock(
                LocalSource::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->localSource;
    }
}
