<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Mageplaza\LazySpeaker\Helper\Data;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class LoadWords extends Action
{
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

    protected $helperData;

    protected $arrWordIdsBelongPack;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param WordFactory $wordFactory
     * @param \Magento\Customer\Model\Session $_customerSession
     * @param WordCollectionFactory $wordCollectionFactory
     * @param PackageWordCollection $packageWordCollection
     * @param PackageFactory $packageFactory
     */
    public function __construct(
        Context $context,
        WordFactory $wordFactory,
        \Magento\Customer\Model\Session $_customerSession,
        WordCollectionFactory $wordCollectionFactory,
        PackageWordCollection $packageWordCollection,
        Data $helperData,
        PackageFactory $packageFactory

    ) {
        $this->packageFactory = $packageFactory;
        $this->packageWordCollection = $packageWordCollection;
        $this->wordCollectionFactory = $wordCollectionFactory;
        $this->_customerSession = $_customerSession;
        $this->wordFactory = $wordFactory;
        $this->helperData = $helperData;
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

    public function getCollectionData($userId) {
        $allWords = $this->wordCollectionFactory->create()->addFieldToFilter('user_id', ['eq' => $userId]);
        $dataCollection = [];
        foreach ($allWords as $word) {
            $arrWord = json_decode(json_encode($word->getData()),1);
            $arrWord['package_belong'] = $this->getAllBelongPackages($word->getId());
            $dataCollection[] = $arrWord;
        }
        return $dataCollection;
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


    //get all words belong package
    public function getWordsBelongPackage($packageId)
    {
        $allWordsOfPack = $this->packageWordCollection->create()
                          ->addFieldToFilter('package_id', ['eq' => $packageId])
                            ->addFieldToSelect('word_id')->toArray();

        $arrWordIds = [];
        $wordCollection = [];
        foreach ($allWordsOfPack['items'] as $item) {
            $arrWordIds[] = $item['word_id'];
        }
        if($arrWordIds) {
            foreach ($arrWordIds as $id) {
                $wordCollection[] = $this->wordFactory->create()->load($id)->toArray();
            }
        }
        $this->arrWordIdsBelongPack = $arrWordIds;
        return $wordCollection;
    }

    //get all words do not belong package
    public function getWordsNotBelongPackage($userId)
    {
        $getIds = $this->wordCollectionFactory->create()
                 ->addFieldToFilter('user_id', ['eq' => $userId])
                    ->addFieldToSelect('id')->toArray();
        $allIds = [];
        $wordCollection = [];

        foreach ($getIds['items'] as $item) {
            $allIds[] = $item['id'];
        }
        $arrNotBelongPackage = array_diff($allIds, $this->arrWordIdsBelongPack);
        if($arrNotBelongPackage) {
            foreach ($arrNotBelongPackage as $id) {
                $wordCollection[] = $this->wordFactory->create()->load($id)->toArray();
            }
        }

        return $wordCollection;
    }


    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $userId = $this->_customerSession->getCustomer()->getId();
            if ($this->getRequest()->getParam('edit_word') == true &&
                $packageId = $this->getRequest()->getParam('pack')) {
                if ($this->helperData->checkOwnPackage($userId, $packageId)) {
                    return $this->getResponse()->representJson(
                        self::jsonEncode(['belong'    => $this->getWordsBelongPackage($packageId),
                                          'notBelong' => $this->getWordsNotBelongPackage($userId)])
                    );
                }
            }

            if ($idToCreatePost = $this->getRequest()->getParam('create_post')) {
                $this->_customerSession->setPackIdsShare($idToCreatePost);
                return $this->getResponse()->representJson(
                    self::jsonEncode(['success' => true ])
                );
            }

            if ($this->getRequest()->getParam('all_words') == true) {
                $dataCollection = $this->getCollectionData($userId);
                return $this->getResponse()->representJson(
                    self::jsonEncode(['items' => $dataCollection])
                );
            }
            return false;
        }
    }

}
