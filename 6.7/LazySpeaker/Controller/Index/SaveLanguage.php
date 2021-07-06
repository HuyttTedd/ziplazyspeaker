<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
use Mageplaza\LazySpeaker\Model\ResourceModel\Package\CollectionFactory as PackageCollection;

/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class SaveLanguage extends Action
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

    protected $packageCollection;

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
        PackageFactory $packageFactory,
        PackageCollection $packageCollection

    ) {
        $this->packageCollection = $packageCollection;
        $this->packageFactory = $packageFactory;
        $this->packageWordCollection = $packageWordCollection;
        $this->wordCollectionFactory = $wordCollectionFactory;
        $this->_customerSession = $_customerSession;
        $this->wordFactory = $wordFactory;
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
        if($this->getRequest()->isAjax()) {
            $data = $this->getRequest()->getParams();
            $packageFactory = $this->packageFactory->create()->load($data['pack']);
            $isSave = false;
            try {
                $packLang = json_encode($data['lang_data']);
                $packageFactory->setData('package_language', $packLang)->save();
                $isSave = true;
            } catch (Exception $e) {

            }
            return $this->getResponse()->representJson(
                self::jsonEncode(['items' => $isSave])
            );
        }

        return false;
    }

}
