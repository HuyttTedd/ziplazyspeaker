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
use Mageplaza\LazySpeaker\Model\ResourceModel\Package\CollectionFactory as PackageCollectionFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
use Mageplaza\LazySpeaker\Model\Package\WordFactory as PackageWordFactory;
/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class MakePackageMine extends Action
{
    protected $wordFactory;

    protected $wordCollectionFactory;

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

    protected $arrWordIdsBelongPack;


    protected $packageName;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param WordFactory $wordFactory
     * @param \Magento\Customer\Model\Session $_customerSession
     * @param WordCollectionFactory $wordCollectionFactory
     * @param PackageWordCollection $packageWordCollection
     * @param Data $helperData
     * @param PackageFactory $packageFactory
     * @param PackageCollectionFactory $packageCollectionFactory
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
        PackageWordFactory $packageWordFactory

    ) {
        $this->packageFactory           = $packageFactory;
        $this->packageWordCollection    = $packageWordCollection;
        $this->wordCollectionFactory    = $wordCollectionFactory;
        $this->_customerSession         = $_customerSession;
        $this->wordFactory              = $wordFactory;
        $this->helperData               = $helperData;
        $this->packageWordFactory       = $packageWordFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
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

    public function isNotDuplicateNamePackage($packageId, $userId): bool
    {
        $allName = [];
        $packName = $this->packageFactory->create()->load($packageId)->getName();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($packName);
        if(!$packName) {
            return false;
        }
        $collection = $this->packageCollectionFactory->create()
            ->addFieldToFilter('user_id', ['eq' => $userId])
            ->addFieldToSelect('name')->toArray();
        foreach ($collection['items'] as $item) {
            $allName[] = $item['name'];
        }

        if(!in_array($packName, $allName)) {
            $this->packageName = $packName;
            return true;
        }

        return false;
    }

    //get all words do not belong package

    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $result = false;
            try {
                $userId = $this->_customerSession->getId();
                $packageId = $this->getRequest()->getParam('package_id');
                $flag = $this->isNotDuplicateNamePackage($packageId, $userId);
                if ($flag) {
                    $dataNewPack = [
                        'name' => $this->packageName,
                        'user_id' => $userId,
                        'package_position' => 99999
                    ];
                    $wordCollection = $this->getWordsBelongPackage($packageId);
                    $newPack = $this->packageFactory->create()->addData($dataNewPack)->save();
                    $newPackId = $newPack->getId();
                    foreach ($wordCollection as $word) {
                        array_shift($word);
                        $word['user_id'] = $userId;
                        $newWord = $this->wordFactory->create()->addData($word)->save();
                        $wordId = $newWord->getId();
                        $dataPackWord = [
                            'package_id'            => $newPackId,
                            'word_id'               => $wordId,
                            'package_word_position' => 9999
                        ];
                        $this->packageWordFactory->create()->addData($dataPackWord)->save();
                    }
                    $result = true;
                }
            } catch (\Exception $e) {

            }
            return $this->getResponse()->representJson(
                self::jsonEncode(['result' => $result])
            );
        }
    }

}
