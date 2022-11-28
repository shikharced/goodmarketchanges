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
 * @package   Ced_MPCatch
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Ui\Component\Listing\Columns\Scheduler;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class FeedFile
 * @package Ced\EbayMultiAccount\Ui\Component\Listing\Columns\JobScheduler
 */
class SchedulerResponse extends Column
{
    /**
     * @var UrlInterface
     */
    public $urlBuilder;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    public $file;
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    public $dl;

    const URL_PATH_RESEND = 'ebaymultiaccount/product/index';

    /**
     * FeedFile constructor.
     * @param ContextInterface $context
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $fileIo
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        $components = [],
        $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->dl = $directoryList;
        $this->urlBuilder = $urlBuilder;
        $this->file = $fileIo;
        $this->currentStore = $storeManager->getStore();
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
                $feedFile = $item[$name];
//                echo "<pre>";
//                print_r($dataSource);
//                echo "<pre>";
//                print_r($feedFile);
//                die(__FILE__);
                $item[$name] = [];
                if (isset($item['scheduler_id'])) {
                    $feedId = $item['scheduler_id'];
                    $item[$name]['view'] = [
                        'label' => __('View'),
                        'class' => 'cedcommerce actions view',
                        'popup' => [
                            'title' => __("Feed #{$feedId}"),
                            'message' => $feedFile,
                            'type' => 'json',
                            'render' => 'html'
                        ],
                    ];
                }
            }
        }
        return $dataSource;
    }
}
