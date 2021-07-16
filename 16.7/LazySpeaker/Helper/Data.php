<?php
namespace Mageplaza\LazySpeaker\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\PostFactory;
use Mageplaza\LazySpeaker\Model\PostPackageFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PostPackage\CollectionFactory as PostPackgeCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Mageplaza\LazySpeaker\Model\LikeFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Like\CollectionFactory as LikeCollectionFactory;



class Data extends AbstractHelper {
    protected $wordFactory;

    protected $wordCollectionFactory;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected $packageWordCollection;

    protected $postPackageCollection;

    protected $packageFactory;

    protected $resultRedirectFactory;

    protected $postCollectionFactory;

    protected $postFactory;

    protected $postPackageFactory;

    protected $likeFactory;

    protected $likeCollectionFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param WordFactory $wordFactory
     * @param \Magento\Customer\Model\Session $_customerSession
     * @param WordCollectionFactory $wordCollectionFactory
     * @param PackageWordCollection $packageWordCollection
     * @param PackageFactory $packageFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param PostFactory $postFactory
     * @param PostPackageFactory $postPackageFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param PostPackgeCollectionFactory $postPackageCollection
     * @param LikeFactory $likeFactory
     * @param LikeCollectionFactory $likeCollectionFactory
     */
    public function __construct(
        Context $context,
        WordFactory $wordFactory,
        \Magento\Customer\Model\Session $_customerSession,
        WordCollectionFactory $wordCollectionFactory,
        PackageWordCollection $packageWordCollection,
        PackageFactory $packageFactory,
        RedirectFactory $resultRedirectFactory,
        PostFactory $postFactory,
        PostPackageFactory $postPackageFactory,
        PostCollectionFactory $postCollectionFactory,
        PostPackgeCollectionFactory $postPackageCollection,
        LikeFactory $likeFactory,
        LikeCollectionFactory $likeCollectionFactory

    ) {
        $this->resultRedirectFactory    = $resultRedirectFactory;
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
        parent::__construct($context);
    }

    public function getAllBelongPackages($wordId)
    {
        $allPackage = $this->packageWordCollection->create()->addFieldToFilter('word_id', ['eq' => $wordId]);
        $strPackage = '';
        foreach ($allPackage as $pack) {
            $packageName = $this->packageFactory->create()
                ->load($pack->getData('package_id'))->getData('name');
            $strPackage == '' ? $strPackage .= $packageName : $strPackage .= "<br>" . $packageName;
        }
        return $strPackage;
    }

    public function getAllWordIdsBelongPackage($packageId) {
        $arrWordId = [];
        $collection = $this->packageWordCollection->create()
                       ->addFieldToFilter('package_id', ['eq' => $packageId])
                        ->addFieldToSelect('word_id')->toArray();
        foreach ($collection["items"] as $item) {
            $arrWordId[] = $item["word_id"];
        }
        return $arrWordId;
    }

    public function deleteAllPackageWord($packageId) {
        $collection = $this->packageWordCollection->create()
            ->addFieldToFilter('package_id', ['eq' => $packageId]);
        return $collection->walk('delete');
    }

    public function returnHomepage() {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('/');
        return $resultRedirect;
    }

    public function checkOwnPackage($userId, $packageId) {
        $checkId = $this->packageFactory->create()->load($packageId)->getData('user_id');
        if($checkId == $userId) {
            return true;
        }
        return false;
    }

    public function checkOwnArrayPackageIds($userId, array $packageIds) {
        if(!$packageIds) {
            return false;
        }
        foreach ($packageIds as $id) {
            $checkId = $this->packageFactory->create()->load($id)->getData('user_id');
            if ($checkId !== $userId) {
                return false;
            }
        }
        return true;
    }

    public function checkOwnWord($userId, $wordId) {
        $checkId = $this->wordFactory->create()->load($wordId)->getData('user_id');
        if($checkId == $userId) {
            return true;
        }
        return false;
    }

    public function getPackageData($packageId) {
        return $this->packageFactory->create()->load($packageId);
    }

    public function getWordData($wordId) {
        return $this->wordFactory->create()->load($wordId)->toArray();
    }

    public function getDataWordsToRead() {
        return $this->_customerSession->getWordsToRead();
    }

    public function getLang() {
        return $this->_customerSession->getLang();
    }

    public function getPostsData() {
        //lấy key chung theo name vì vậy package anme không được lặp lại
        $allPosts = $this->postCollectionFactory->create()->addFieldToSelect('*');
        $allPostsData = [];
        foreach ($allPosts as $post) {
            $totalLike = $this->likeCollectionFactory->create()->addFieldToFilter('post_id', ['eq' => $post->getId()])->getSize();
            $allPostsData[$post->getId()]['post_data'] = json_decode(json_encode($post->getData()),1);
            $allPostsData[$post->getId()]['total_like'] = $totalLike;
            $allPackages = $this->postPackageCollection->create()
                          ->addFieldToFilter('post_id', ['eq' => $post->getId()])
                          ->addFieldToSelect('package_id');
            foreach ($allPackages as $pack) {
                $packageName = $this->getPackageData($pack->getData('package_id'))['name'];
                $allWords = $this->packageWordCollection->create()
                    ->addFieldToFilter('package_id', ['eq' => $pack->getData('package_id')])
                    ->addFieldToSelect('word_id');
                foreach ($allWords as $word) {
                    $allPostsData[$post->getId()]['package_data'][$packageName][] = $this->getWordData($word->getData('word_id'));
                }
                    $allPostsData[$post->getId()]['package_data'][$packageName][] = $pack->getData('package_id');
            }
        }

//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info($allPostsData);

        return $allPostsData;
    }

    public function getPostsDataById($postId): array
    {
        //lấy key chung theo name vì vậy package anme không được lặp lại
        $allPosts = $this->postCollectionFactory->create()->addFieldToSelect('*')
                        ->addFieldToFilter('id', ['eq' => $postId]);
        $allPostsData = [];
        foreach ($allPosts as $post) {
            $totalLike = $this->likeCollectionFactory->create()->addFieldToFilter('post_id', ['eq' => $post->getId()])->getSize();
            $allPostsData[$post->getId()]['post_data'] = json_decode(json_encode($post->getData()),1);
            $allPostsData[$post->getId()]['total_like'] = $totalLike;
            $allPackages = $this->postPackageCollection->create()
                ->addFieldToFilter('post_id', ['eq' => $post->getId()])
                ->addFieldToSelect('package_id');
            foreach ($allPackages as $pack) {
                $packageName = $this->getPackageData($pack->getData('package_id'))['name'];
                $allWords = $this->packageWordCollection->create()
                    ->addFieldToFilter('package_id', ['eq' => $pack->getData('package_id')])
                    ->addFieldToSelect('word_id');
                foreach ($allWords as $word) {
                    $allPostsData[$post->getId()]['package_data'][$packageName][] = $this->getWordData($word->getData('word_id'));
                }
                $allPostsData[$post->getId()]['package_data'][$packageName][] = $pack->getData('package_id');
            }
        }

        return $allPostsData;
    }

    public function getCustomerId() {
        return $this->_customerSession->getId();
    }

    public function getAllLikedPostIds($userId) {
        return $this->likeCollectionFactory->create()
            ->addFieldToFilter('user_id', ['eq' => $userId])
            ->addFieldToSelect('post_id')->toArray();
    }

    public function getTotalLike($userId) {
        $allPost =  $this->postCollectionFactory->create()
            ->addFieldToFilter('user_id', ['eq' => $userId])
            ->addFieldToSelect('id')->toOptionArray();
        $allIds = [];
        foreach ($allPost as $id) {
            $allIds[] = $id['value'];
        }
        return $this->likeCollectionFactory->create()
            ->addFieldToFilter('post_id', ['in' => $allIds])->getSize();
    }
}
