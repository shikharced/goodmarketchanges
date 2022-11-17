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
 * @copyright   Copyright © 2018 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Ui\Component\Listing\Columns\Product;

class Errors extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $urlBuilder;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public $serializer;

    /**
     * ProductValidation constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Serialize\SerializerInterface  $json
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        $components = [],
        $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->serializer = $serializer;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
//            $fieldNames = 'goodmarket_product_error';
//            foreach ($dataSource['data']['items'] as &$item) {
//                if (empty($item[$fieldNames])) {
//                    $item[$fieldNames . '_html'] =
//                        "<div class='grid-severity-notice'><span>Valid</span></div>";
//                    $item[$fieldNames . '_title'] = __('Errors');
//                    $item[$fieldNames . '_productid'] = $item['entity_id'];
//                } else {
//                    $item[$fieldNames . '_html'] =
//                        "<div class='grid-severity-critical'><span>Invalid</span></div>";
//                    $item[$fieldNames . '_title'] = __('Errors');
//                    $item[$fieldNames . '_productid'] = $item['entity_id'];
//                    $item[$fieldNames . '_productvalidation'] = isset($item[$fieldNames])?$item[$fieldNames]:'';
//                }
//            }
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName])) {
                    if ($item[$fieldName] == '["valid"]') {
                        $item[$fieldName . '_html'] =
                            "<div class='grid-severity-notice'><span>Valid</span></div>";
                        $item[$fieldName . '_title'] = __('Errors');
                        $item[$fieldName . '_productid'] = $item['entity_id'];
                    } else {
                        $item[$fieldName . '_html'] =
                            "<div class='grid-severity-critical'><span>Invalid</span></div>";
                        $item[$fieldName . '_title'] = __('Errors');
                        $item[$fieldName . '_productid'] = $item['entity_id'];
                        $item[$fieldName . '_productvalidation'] ='{"AddItems":{"id":"'.$item['entity_id'].'","sku":"'.$item['sku'].'","url":"#","errors":["'.$item[$fieldName].'"]}}';//json_encode([$item[$fieldName]]);
                    }
                } else {
                    $item[$fieldName . '_html'] =
                        "<div class='grid-severity-notice'><span>Not-Validated</span></div>";
                    $item[$fieldName . '_title'] = __('Errors');
                    $item[$fieldName . '_productid'] = $item['entity_id'];
                }
            }
        }

        return $dataSource;
    }
}
