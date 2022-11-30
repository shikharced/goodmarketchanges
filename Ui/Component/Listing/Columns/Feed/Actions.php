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
 * @package     Ced_EbayMultiAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Ui\Component\Listing\Columns\Feed;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Actions
 * @package Ced\EbayMultiAccount\Ui\Component\Listing\Columns\Profile
 */
class Actions extends Column
{
    const URL_FEED_SYNC = 'goodmarket/scheduler/sync';
    const URL_PRODUCTDETAIL_SYNC = 'goodmarket/scheduler/productsync';
    const URL_FEED_DELETE='goodmarket/scheduler/delete';

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * Actions constructor.
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
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                        $item[$name]['sync'] = [
                            'href' => $this->urlBuilder->getUrl(self::URL_FEED_SYNC, ['id' => $item['id']]),
                            'label' => __('Sync Feed'),
                            'class' => 'cedcommerce actions download'
                        ];
                        $item[$name]['import_item_ids'] = [
                            'href' => $this->urlBuilder->getUrl(self::URL_PRODUCTDETAIL_SYNC, ['id' => $item['id']]),
                            'label' => __('Sync Product Details'),
                            'class' => 'cedcommerce actions sync'
                        ];
                $item[$name]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(self::URL_FEED_DELETE, ['id' => $item['id']]),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete feed'). ' #'.$item['id'],
                        'message' => __('Are you sure you wan\'t to delete the feed?')
                    ],
                    'class' => 'cedcommerce actions delete'
                ];
            }
        }
        return $dataSource;
    }
}