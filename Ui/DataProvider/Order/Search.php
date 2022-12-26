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
 * @package   Ced_Range
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;

/**
 * Class DataProvider of search
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    public $addFieldStrategies;

    /**
     * @var array
     */
    public $addFilterStrategies;

    /**
     * @var FilterBuilder
     */
    public $filterBuilder;

    /**
     * Search Constructor
     *
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Ced\GoodMarket\Helper\Config $config
     * @param FilterBuilder $filterBuilder
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Ced\GoodMarket\Helper\Config $config,
        FilterBuilder $filterBuilder,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->filterBuilder = $filterBuilder;
        $this->orderRepository = $orderRepository;
        $this->collection = $collectionFactory->create();
        $this->collection
            ->setStoreId($config->getStoreId())
            ->joinField(
                'qty',
                $this->collection->getTable('cataloginventory_stock_item'),
                'qty',
                'product_id = entity_id',
                '{{table}}.stock_id=1',
                null
            );
        $this->addField('goodmarket_product_status');
        $this->addField('goodmarket_product_error');
        $this->addField('goodmarket_profile_id');
        $this->addFilter(
            $this->filterBuilder->setField('goodmarket_profile_id')
                ->setConditionType('notnull')
                ->setValue('true')
                ->create()
        );
//        echo '<pre>'; print_r($this->collection->getData());exit;


//        $this->addFilter(
//            $this->filterBuilder->setField('zalandoRetail_status')
//                ->setConditionType('in')
//                ->setValue('1')
//                ->create()
//        );

        $this->addFilter(
            $this->filterBuilder->setField('type_id')->setConditionType('in')
                ->setValue(['simple', 'configurable', 'virtual'])
                ->create()
        );

        $this->addFilter(
            $this->filterBuilder->setField('visibility')->setConditionType('nin')
                ->setValue([1])
                ->create()
        );
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * Function addFilter
     *
     * @param \Magento\Framework\Api\Filter $filter
     * @return void
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }

    /**
     * Get Data function
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();
//        foreach($items as $key=>$item)
//        {
//            $items[$key]['product_offer_status']=isset($item['product_offer_id'])?$item['product_offer_id']:'';
//        }
        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }

    /**
     * Add Field Function
     *
     * @param $field
     * @param $alias
     * @throws Exception
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }
}
