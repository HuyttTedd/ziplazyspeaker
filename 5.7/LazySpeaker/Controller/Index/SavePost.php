<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Mageplaza\LazySpeaker\Helper\Data;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Model\PostFactory;
use Mageplaza\LazySpeaker\Model\PostPackageFactory;
use Mageplaza\LazySpeaker\Model\Package\WordFactory as PackageWordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Package\CollectionFactory as PackageCollectionFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class SavePost extends Action
{
    protected $wordFactory;

    protected $packageFactory;

    protected $postFactory;

    protected $postPackageFactory;

    protected $wordCollectionFactory;

    protected $packageWordFactory;

    protected $helperData;

    protected $packageCollectionFactory;

    protected $postCollectionFactory;

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
        PackageWordFactory $packageWordFactory,
        PostFactory $postFactory,
        PostPackageFactory $postPackageFactory,
        PostCollectionFactory $postCollectionFactory

    ) {
        $this->packageWordFactory       = $packageWordFactory;
        $this->packageFactory           = $packageFactory;
        $this->wordCollectionFactory    = $wordCollectionFactory;
        $this->_customerSession         = $_customerSession;
        $this->wordFactory              = $wordFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->helperData               = $helperData;
        $this->postFactory              = $postFactory;
        $this->postPackageFactory       = $postPackageFactory;
        $this->postCollectionFactory    = $postCollectionFactory;
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

        $returnAddPost = $this->_url->getUrl('lazyspeaker/index/viewallpackage', ['_secure' => true]);
        if(!$postTitle) {
            $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
            $resultRedirect->setPath($returnAddPost);
            return $resultRedirect;
        }
        $arrPackIds = explode(',', $allData['package_ids']);
        if(isset($arrPackIds) && $this->helperData->checkOwnArrayPackageIds($userId, $arrPackIds)) {
            //them package
            $postFactory = $this->postFactory->create();
            $postData = [
                'title'         => $postTitle,
                'user_id'       => $userId,
                'post_position' => 999999
            ];
            try {
                $postFactory->addData($postData)->save();
                $postId = $postFactory->getId();
                foreach ($arrPackIds as $id) {
                    $postPackageData = [
                        'package_id' => $id,
                        'post_id'    => $postId
                    ];
                    $this->postPackageFactory->create()->addData($postPackageData)->save();
                }
                $this->messageManager->addSuccessMessage(__('Save successfully!'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
        }

        $resultRedirect->setPath($returnAddPost);
        return $resultRedirect;
    }
}
