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
 * @package   Ced_GoodMarket
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Model\Source\Profile\Category;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\ObjectManagerInterface;
use Ced\GoodMarket\Helper\Data;
use \Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Rootlevel for root categories
 */
class Rootlevel implements ArrayInterface
{
    /**
     * @var ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var Filesystem
     */
    public $filesystem;

    /**
     * @var Data
     */
    public $helper;

    /**
     * Rootlevel constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Data                   $helper
     * @param Filesystem             $filesystem
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Data $helper,
        Filesystem $filesystem
    ) {
        $this->objectManager = $objectManager;
        $this->helper = $helper;
        $this->filesystem = $filesystem;
    }

    /**
     * To Array
     *
     * @return string|[]
     */
    public function toOptionArray()
    {
        $options = [];
        $folderPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('ced/goodmarket/');
        $path = $folderPath .'/categoryLevel-1.json';
        $rootLevel = $this->helper->loadFile($path);
        
        foreach ($rootLevel as $value) {
            $options[] = [
                'value' => $value['id'],
                'label' => $value['name']
            ];
        }
        return $options;
    }
}
