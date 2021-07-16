<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ReportsPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LazySpeaker\Block\Adminhtml;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Mageplaza\LazySpeaker\Helper\Data;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\PostFactory;
use Mageplaza\LazySpeaker\Model\PostPackageFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PostPackage\CollectionFactory as PostPackgeCollectionFactory;
use Mageplaza\LazySpeaker\Model\LikeFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Like\CollectionFactory as LikeCollectionFactory;
/**
 * Class ChartDetail
 * @package Mageplaza\ReportsPro\Block\Adminhtml\Details\SalesByProduct
 */
class Manage extends Template
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    protected $helperData;

    protected $wordFactory;

    protected $wordCollectionFactory;

    /**
     * Customer session
     *
     * @var Session
     */
    protected $_customerSession;

    protected $packageWordCollection;

    protected $postPackageCollection;

    protected $packageFactory;

    protected $postCollectionFactory;

    protected $postFactory;

    protected $postPackageFactory;

    protected $likeFactory;

    protected $likeCollectionFactory;

    /**
     * ChartDetail constructor.
     *
     * @param Template\Context $context
     * @param ProductFactory $productFactory
     * @param Data $helperData
     * @param WordFactory $wordFactory
     * @param Session $_customerSession
     * @param WordCollectionFactory $wordCollectionFactory
     * @param PackageWordCollection $packageWordCollection
     * @param PackageFactory $packageFactory
     * @param PostFactory $postFactory
     * @param PostPackageFactory $postPackageFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param PostPackgeCollectionFactory $postPackageCollection
     * @param LikeFactory $likeFactory
     * @param LikeCollectionFactory $likeCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ProductFactory $productFactory,
        Data $helperData,
        WordFactory $wordFactory,
        Session $_customerSession,
        WordCollectionFactory $wordCollectionFactory,
        PackageWordCollection $packageWordCollection,
        PackageFactory $packageFactory,
        PostFactory $postFactory,
        PostPackageFactory $postPackageFactory,
        PostCollectionFactory $postCollectionFactory,
        PostPackgeCollectionFactory $postPackageCollection,
        LikeFactory $likeFactory,
        LikeCollectionFactory $likeCollectionFactory,
        array $data = []
    ) {
        $this->helperData               = $helperData;
        $this->wordCollectionFactory    = $wordCollectionFactory;
        $this->_customerSession         = $_customerSession;
        $this->wordFactory              = $wordFactory;
        $this->packageWordCollection    = $packageWordCollection;
        $this->packageFactory           = $packageFactory;
        $this->postFactory              = $postFactory;
        $this->postPackageFactory       = $postPackageFactory;
        $this->postCollectionFactory    = $postCollectionFactory;
        $this->postPackageCollection    = $postPackageCollection;
        $this->likeFactory              = $likeFactory;
        $this->likeCollectionFactory    = $likeCollectionFactory;
        $this->request                  = $context->getRequest();
        $this->productFactory           = $productFactory;

        parent::__construct($context, $data);
    }

    public function getPostId() {
        return $this->getRequest()->getParam('id');
    }

    public function getDataBelongPost(): array
    {
        $postId = $this->getRequest()->getParam('id');
        return $this->helperData->getPostsDataById($postId);
    }
}
