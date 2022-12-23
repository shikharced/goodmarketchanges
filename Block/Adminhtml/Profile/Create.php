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
 * @package   Ced_GoodMarket
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\GoodMarket\Block\Adminhtml\Profile;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template;
use Ced\GoodMarket\Model\ProfileFactory;

/**
 * Class Create template
 */
class Create extends Template
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $category;

    /**
     * Create Constructor.
     *
     * @param Template\Context $context
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param ProfileFactory $profileFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $category
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        ProfileFactory $profileFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $category
    ) {
        parent::__construct($context);
        $this->fileDriver = $fileDriver;
        $this->_dir = $dir;
        $this->category = $category;
        $this->profileFactory = $profileFactory;
    }

    /**
     * Get Media Path
     *
     * @return string
     */
    public function getMediaPath()
    {
        return $this->_dir->getPath('media');
    }

    /**
     * Get Goodmarket Categories.
     *
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getGoodMarketCategories()
    {
        $txtFileName = 'categoryLevel.json';
        $filePath = $this->getMediaPath().'/ced/goodmarket/';
        $file = $filePath . $txtFileName;
        $readFile = $this->fileDriver->fileOpen($file, 'r');
        $categories = $this->fileDriver->fileRead($readFile, filesize($file));
        $category = json_decode($categories, true);
        // echo '<pre>'; print_r($category[1]); exit;
        $catName = [];
        $i=0;
        foreach ($category as $catl2) {
            if (!empty($catl2['children'])) {
                foreach ($catl2['children'] as $catl3) {
                    $name = $catl2['name'] . ' -> '. $catl3['name'];
                    if (!empty($catl3['children'])) {
                        foreach ($catl3['children'] as $catl4) {
                            $name = $catl2['name'] . ' -> '. $catl3['name'] . ' -> ' . $catl4['name'];
                            $catName['162,'.$catl2['id'].','.$catl3['id'].','.$catl4['id'].',,,'] = 'Default -> '.$name;
                        }
                    } else {
                        $catName['162,'.$catl2['id'].','.$catl3['id'].',,,,'] = 'Default -> '.$name;
                    }
                }
            } else {
                $catName['162,'.$catl2['id'].',,,,,'] = 'Default -> '.$name;
            }

        }
        return $catName;
        // echo '<pre>'; print_r($catName);
    }

    /**
     * Get Categories.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategories()
    {
        $categories = $this->category->create()->addAttributeToSelect('*');
        $category = [];
        foreach ($categories as $cat) {
            if ($cat->getId() > 2) {
                $category[$cat->getId()] = $cat->getName();
            }
        }
        return $category;
    }

    /**
     * Check Category Id.
     *
     * @param $catId
     * @return array|mixed|string|null
     */
    public function checkCatId($catId){
        $profile = $this->profileFactory->create()->load($catId, 'magento_category');
        if ($profile->getData()) {
            return $profile->getData('profile_category');
        } else {
            return '';
        }
    }
}
