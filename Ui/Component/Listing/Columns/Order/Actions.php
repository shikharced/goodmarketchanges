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
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Ui\Component\Listing\Columns\Order;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Action Columns listing
 */
class Actions extends Column
{
    /** Url path */
    public const URL_PATH_EDIT = 'sales/order/view';
    public const URL_PATH_VIEW = 'goodmarket/order/view';
    public const URL_PATH_SYNC = 'goodmarket/order/sync';
    public const URL_PATH_DELETE = 'goodmarket/order/delete';

    /** @var UrlInterface */
    public $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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

            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    $editUrl = 'javascript:void(0);';
                    $editClass = 'cedcommerce actions edit disable';
                    if (!empty($item['magento_order_id'])) {
                        $editUrl = $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            [
                                'order_id' => $item['magento_order_id']
                            ]
                        );
                        $editClass = 'cedcommerce actions edit';
                    }
                    $item[$name]['view'] = [
                        'label' => __('View Order'),
                        'class' => 'cedcommerce actions view',
                        'popup' => [
                            'title' => __("GoodMarket Order #{$item['magento_increment_id']}"),
                            'file' =>  $this->urlBuilder->getUrl(
                                self::URL_PATH_VIEW,
                                ['id' => $item['id']]
                            ),
                            'type' => 'json',
                            'render' => 'html',
                        ],
                    ];

                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            [
                                'order_id' => $item['magento_order_id']
                            ]
                        ),
                        'label' => __('Edit'),
                        'class' => 'cedcommerce actions edit'
                    ];
                    /*$item[$name]['sync'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_SYNC, ['id' => $item['id']]),
                        'label' => __('Sync'),
                        'class' => 'cedcommerce actions sync'
                    ];*/
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_DELETE, ['id' => $item['id']]),
                        'label' => __('Delete'),
                        'class' => 'cedcommerce actions delete'
                    ];
                }
            }
        }
        return $dataSource;
    }
}
