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

namespace Ced\GoodMarket\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class Category for menus
 */
class Category implements OptionSourceInterface
{
    /**
     * public param $category
     *
     * @var CollectionFactory
     */
    public $category;
    
    /**
     * public param $storeManager
     *
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var array
     */
    public $allowedLevels = [1, 2, 3, 4, 5];

    /**
     * Category constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->category = $collectionFactory;
    }

    /**
     * Public function toOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        $categories = $this->category->create()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('level')
            ->addAttributeToFilter('level', ['in', $this->allowedLevels])
            ->setStore($this->storeManager->getStore());
        $options = [
            [
                'label' => '',
                'value' => '',
            ]
        ];

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($categories as $category) {
            $space = str_repeat('&nbsp;', $category->getLevel() * 3);
            $option['label'] = $space.$category->getName() . " [{$category->getEntityId()}]";
            $option['value'] = $category->getEntityId();
            $options[] = $option;
        }

        return $options;
    }

    /**
     * To getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->getOptionArray();
    }

    /**
     * To getOptionArray
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        foreach ($this->toOptionArray() as $option) {
            $options[$option['value']] = (string)$option['label'];
        }
        return $options;
    }
}
