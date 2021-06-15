<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Mageplaza\LazySpeaker\Helper\Data;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\Package\WordFactory as PackageWordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Package\CollectionFactory as PackageCollectionFactory;
/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class SavePost extends Action
{
    protected $wordFactory;

    protected $packageFactory;

    protected $wordCollectionFactory;

    protected $packageWordFactory;

    protected $helperData;

    protected $packageCollectionFactory;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param WordFactory $wordFactory
     * @param \Magento\Customer\Model\Session $_customerSession
     * @param WordCollectionFactory $wordCollectionFactory
     * @param PackageCollectionFactory $packageCollectionFactory
     * @param PackageFactory $packageFactory
     * @param Data $helperData
     * @param PackageWordFactory $packageWordFactory
     */
    public function __construct(
        Context $context,
        WordFactory $wordFactory,
        \Magento\Customer\Model\Session $_customerSession,
        WordCollectionFactory $wordCollectionFactory,
        PackageCollectionFactory $packageCollectionFactory,
        PackageFactory $packageFactory,
        Data $helperData,
        PackageWordFactory $packageWordFactory

    ) {
        $this->packageWordFactory       = $packageWordFactory;
        $this->packageFactory           = $packageFactory;
        $this->wordCollectionFactory    = $wordCollectionFactory;
        $this->_customerSession         = $_customerSession;
        $this->wordFactory              = $wordFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->helperData               = $helperData;
        parent::__construct($context);
    }

    public function execute()
    {
        if(!$this->_customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You are not logged in. Please log in and try again.'));
            return $this->_customerSession->authenticate();
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        $userId = $this->_customerSession->getId();
        $allData = $this->getRequest()->getParams();

        $postTitle = trim($allData['post_title']);
        $postTitle = preg_replace('/\s+/', ' ', $postTitle);

        $returnAddPost = $this->_url->getUrl('lazyspeaker/index/createpost', ['_secure' => true]);
        if(!$postTitle) {
            $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
            $resultRedirect->setPath($returnAddPost);
            return $resultRedirect;
        }
        $arrPackIds = explode(',', $allData['package_ids']);
        if(isset($arrPackIds) && $this->helperData->checkOwnArrayPackageIds($userId, $arrPackIds)) {
            $packageId = $allData['package_id'];
            try {
                $this->packageFactory->create()->load($packageId)->setData('name', $postTitle);
                $this->helperData->deleteAllPackageWord($packageId);

                if ($allData['ids'] != '') {
                    $ids = explode(',', $allData['ids']);
                    foreach ($ids as $id) {
                        //check if user own word
                        if ($this->helperData->checkOwnWord($userId, $id)) {
                            $packageWordData = [
                                'package_id' => $packageId,
                                'word_id' => $id,
                                'package_word_position' => 0
                            ];
                            $this->packageWordFactory->create()->addData($packageWordData)->save();
                        }
                    }
                }
                $this->messageManager->addSuccessMessage(__('Save successfully!'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
            }
            $returnEdit = $this->_url->getUrl('lazyspeaker/index/editpackage', ['package' => $packageId, '_current' => true]);
            return $resultRedirect->setPath($returnEdit);
        }

        $resultRedirect->setPath($returnAddPost);
        return $resultRedirect;
    }
}
