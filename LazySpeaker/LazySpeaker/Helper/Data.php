<?php
namespace Mageplaza\LazySpeaker\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;



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

    protected $packageFactory;

    protected $resultRedirectFactory;

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
     */
    public function __construct(
        Context $context,
        WordFactory $wordFactory,
        \Magento\Customer\Model\Session $_customerSession,
        WordCollectionFactory $wordCollectionFactory,
        PackageWordCollection $packageWordCollection,
        PackageFactory $packageFactory,
        RedirectFactory $resultRedirectFactory

    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->wordCollectionFactory = $wordCollectionFactory;
        $this->_customerSession = $_customerSession;
        $this->wordFactory = $wordFactory;
        $this->packageWordCollection = $packageWordCollection;
        $this->packageFactory = $packageFactory;
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
}
