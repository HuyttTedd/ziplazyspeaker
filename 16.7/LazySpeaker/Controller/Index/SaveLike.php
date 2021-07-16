<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Mageplaza\LazySpeaker\Helper\Data;
use Mageplaza\LazySpeaker\Model\Like;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Like\CollectionFactory as LikeCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Package\CollectionFactory as PackageCollectionFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\LikeFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
use Mageplaza\LazySpeaker\Model\Package\WordFactory as PackageWordFactory;
/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class SaveLike extends Action
{
    protected $wordFactory;

    protected $likeFactory;

    protected $wordCollectionFactory;

    protected $likeCollectionFactory;

    protected $packageCollectionFactory;

    protected $packageWordFactory;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected $packageWordCollection;

    protected $packageFactory;

    protected $helperData;


    protected $packageName;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param WordFactory $wordFactory
     * @param \Magento\Customer\Model\Session $_customerSession
     * @param WordCollectionFactory $wordCollectionFactory
     * @param LikeCollectionFactory $likeCollectionFactory
     * @param PackageWordCollection $packageWordCollection
     * @param Data $helperData
     * @param PackageFactory $packageFactory
     * @param PackageCollectionFactory $packageCollectionFactory
     * @param PackageWordFactory $packageWordFactory
     * @param LikeFactory $likeFactory
     */
    public function __construct(
        Context $context,
        WordFactory $wordFactory,
        \Magento\Customer\Model\Session $_customerSession,
        WordCollectionFactory $wordCollectionFactory,
        PackageWordCollection $packageWordCollection,
        Data $helperData,
        PackageFactory $packageFactory,
        PackageCollectionFactory $packageCollectionFactory,
        PackageWordFactory $packageWordFactory,
        LikeFactory $likeFactory,
        LikeCollectionFactory $likeCollectionFactory

    ) {
        $this->packageFactory           = $packageFactory;
        $this->packageWordCollection    = $packageWordCollection;
        $this->wordCollectionFactory    = $wordCollectionFactory;
        $this->_customerSession         = $_customerSession;
        $this->wordFactory              = $wordFactory;
        $this->helperData               = $helperData;
        $this->packageWordFactory       = $packageWordFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->likeFactory              = $likeFactory;
        $this->likeCollectionFactory    = $likeCollectionFactory;
        parent::__construct($context);
    }

    public static function getJsonHelper()
    {
        return ObjectManager::getInstance()->get(JsonHelper::class);
    }

    public static function jsonEncode($valueToEncode)
    {
        try {
            $encodeValue = self::getJsonHelper()->jsonEncode($valueToEncode);
        } catch (Exception $e) {
            $encodeValue = '{}';
        }
        return $encodeValue;
    }

    //get all words do not belong package

    public function execute()
    {
//        $array_config = [
//            0 => 'bỏ like',
//            1 => 'like',
//            2 => 'co loi'
//        ];

//        $array_config_like = [
//            null => 'loi khong load duoc'
//        ];


        if ($this->getRequest()->isAjax()) {
            $isLike = 0;
            $postId = $this->getRequest()->getParam('post_id');
            $userId = $this->_customerSession->getId();
            //check login
            if(!$userId) {
                return $this->getResponse()->representJson(
                    self::jsonEncode(['result' => 'not login',
                        'total_like' => 'null'])
                );
            }
            $postId = explode('_', $postId);
            $postId = $postId[1];
            $countLike = $this->likeCollectionFactory->create()
                        ->addFieldToFilter('user_id', ['eq' => $userId])
                        ->addFieldToFilter('post_id', ['eq' => $postId]);
            if($countLike->getSize() === 0) {
                $dataLike = [
                  'user_id' => $userId,
                  'post_id' => $postId
                ];
                $likeFactory = $this->likeFactory->create()->addData($dataLike);
                try {
                    $likeFactory->save();
                    $isLike = 1;
                } catch(Exception $e) {
                    $isLike = 2;
                }
            } else {
                foreach ($countLike as $like) {
                    $likeIdToDelete = $like->getData('id');
                    $likeFactoryDelete = $this->likeFactory->create()->load($likeIdToDelete);
                    try {
                        $likeFactoryDelete->delete();
                        $isLike = 0;
                    } catch(Exception $e) {
                        $isLike = 2;
                    }
                }
            }
            $totalLike = null;
            try {
                $totalLike = $this->likeCollectionFactory->create()
                                        ->addFieldToFilter('post_id', ['eq' => $postId])
                                        ->addFieldToFilter('user_id', ['neq' => $userId])
                                        ->getSize();
            } catch (Exception $e) {

            }
            return $this->getResponse()->representJson(
                self::jsonEncode(['result' => $isLike,
                                    'total_like' => $totalLike])
            );
        }
    }
}
