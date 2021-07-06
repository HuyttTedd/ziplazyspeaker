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
class UpdatePosition extends Action
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

    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $userId = $this->_customerSession->getCustomer()->getId();
            $data = $this->getRequest()->getParams();
            $count = 1;
            try {
                if(isset($data['update_word'])) {
                    foreach ($data['update_word'] as $id) {
                        if ($this->helperData->checkOwnWord($userId, $id)) {
                            $this->wordFactory->create()->load($id)->setData('word_position', $count)->save();
                            $count++;
                        }
                    }
                } elseif (isset($data['update_package'])) {
                    foreach ($data['update_package'] as $id) {
                        if ($this->helperData->checkOwnPackage($userId, $id)) {
                            $this->packageFactory->create()->load($id)->setData('package_position', $count)->save();
                            $count++;
                        }
                    }
                }
            } catch (Exception $e) {
                return $this->getResponse()->representJson(
                    self::jsonEncode(['result' => 0])
                );
            }
                return $this->getResponse()->representJson(
                    self::jsonEncode(['result' => 1])
                );
        }
    }

}
