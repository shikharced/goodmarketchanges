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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Ui\Component\Listing\Columns\Product;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Json\Helper\Data;

/**
 * Class Validation for listing
 */
class Status extends Column
{
    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @var Data
     */
    public $json;

    /**
     * @var ProductFactory
     */
    public $product;

    /**
     * Validation constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Data $json
     * @param ProductFactory $productFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Data $json,
        ProductFactory $productFactory,
        $components = [],
        $data = []
    ) {
        $this->product = $productFactory->create();
        $this->urlBuilder = $urlBuilder;
        $this->json = $json;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = 'goodmarket_product_status';
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName]) && !empty($item[$fieldName]) && $item[$fieldName]=='Uploaded') {
                    $item[$fieldName . '_html'] = "<div class='grid-severity-notice'><span>".$item[$fieldName]."</span></div>";
                    $item[$fieldName . '_title'] = __('GoodMarket Product Details');
                    $item[$fieldName . '_productid'] = $item['entity_id'];
                } else {
                    $item[$fieldName . '_html'] = '<div class="grid-severity-notice"><span>Not-Uploaded</span></div>';
                    $item[$fieldName . '_title'] = __('GoodMarket Product Details');
                    $item[$fieldName . '_productid'] = $item['entity_id'];
                }
            }
        }
        return $dataSource;
    }
}
