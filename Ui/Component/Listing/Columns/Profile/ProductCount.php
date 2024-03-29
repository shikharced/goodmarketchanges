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

namespace Ced\GoodMarket\Ui\Component\Listing\Columns\Profile;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Product Validation count
 */
class ProductCount extends Column
{
    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    public $productModel;

    /**
     * @var \Ced\GoodMarket\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    /**
     * Product Count constuctor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magento\Catalog\Model\Product $productModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Catalog\Model\Product $productModel,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->productModel = $productModel;
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
            foreach ($dataSource['data']['items'] as & $item) {
                $value = 0;
                    $products = $this->productModel->getCollection()
                    ->addFieldToFilter('goodmarket_profile_id', $item['id']);
                    $products->addFieldToFilter('type_id', ['simple', 'configurable'])
                        ->addAttributeToFilter('visibility', ['neq' => 1]);
                    $value = count($products);
                $item['product_count'] = $value;
            }
        }

        return $dataSource;
    }
}
