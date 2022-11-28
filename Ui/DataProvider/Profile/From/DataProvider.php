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

namespace Ced\GoodMarket\Ui\DataProvider\Profile\From;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Registry;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var \Ced\GoodMarket\Model\ResourceModel\Profile\Collection
     */
    public $collection;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Ced\GoodMarket\Model\ResourceModel\Profile\CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Ced\GoodMarket\Model\ResourceModel\Profile\CollectionFactory $collectionFactory,
        Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->registry = $registry;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
//        if (isset($this->loadedData)) {
//            return $this->loadedData;
//        }
//        $this->loadedData = [];
//        $profile = $this->registry->registry('goodmarket_profile');
//        if ($profile && $profile->getId()) {
//            $info = [
//                'id' => $profile->getId(),
//                'profile_code' => $profile->getProfileCode(),
//                'profile_status' => $profile->getProfileStatus(),
//                'profile_name' => $profile->getProfileName()
//            ];
//
//            $this->loadedData[$profile->getId()] = ['general_information' => $info,
//                'products'=>json_decode($profile->getProductPrice(),true)];
//        }
//        return $this->loadedData;
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $items = $this->getCollection()->getData();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),

        ];
    }
}
