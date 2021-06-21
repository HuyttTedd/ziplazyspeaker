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
class SavePackage extends Action
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

        $packageName = trim($allData['package_name']);
        $packageName = preg_replace('/\s+/', ' ', $packageName);

        $returnAddPackage = $this->_url->getUrl('lazyspeaker/index/createnewpackage', ['_secure' => true]);
        if(!$packageName) {
            $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
            $resultRedirect->setPath($returnAddPackage);
            return $resultRedirect;
        }

        // check duplicate package's name
        $allNames = [];
        $allPackageName = $this->packageCollectionFactory->create()
                            ->addFieldToFilter('user_id', ['eq' => $userId])
                            ->addFieldToSelect('name')->toOptionArray();
        foreach ($allPackageName as $name) {
            $allNames[] = $name['label'];
        }
        if(in_array($packageName, $allNames) && !isset($allData['package_id'])) {
            $this->messageManager->addErrorMessage(__('Tên package bị trùng!'));
            $resultRedirect->setPath($returnAddPackage);
            return $resultRedirect;
        }
//        elseif(in_array($packageName, $allNames) && isset($allData['package_id'])) {
//            $this->messageManager->addErrorMessage(__('Tên package bị trùng!'));
//            $returnEdit = $this->_url->getUrl('lazyspeaker/index/editpackage', ['package' => $allData['package_id'], '_current' => true]);
//            return $resultRedirect->setPath($returnEdit);
//        }
//        array (
//            0 =>
//                array (
//                    'value' => NULL,
//                    'label' => 'Environment',
//                ),
//            1 =>
//                array (
//                    'value' => NULL,
//                    'label' => 'Future',
//                ),
//        )
        // end check duplicate package's name


        //edit package
        if(isset($allData['package_id']) && $this->helperData->checkOwnPackage($userId, $allData['package_id'])) {
            $packageId = $allData['package_id'];
            try {
                $this->packageFactory->create()->load($packageId)->setData('name', $packageName)->save();
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
        } else {
            //create new package
            $position = $this->packageCollectionFactory->create()
                ->addFieldToFilter('user_id', ['eq' => $userId])->count();
            $packageData = [
                'name' => $packageName,
                'user_id' => $userId,
                'package_position' => $position + 1
            ];
            $packageFactory = $this->packageFactory->create();
            $packageFactory->addData($packageData);

            try {
                $packageFactory->save();
                $packId = $packageFactory->getId();
                if ($allData['ids'] != '') {
                    $ids = explode(',', $allData['ids']);
                    foreach ($ids as $id) {
                        //check if user own word
                        if ($this->helperData->checkOwnWord($userId, $id)) {
                            $packageWordData = [
                                'package_id' => $packId,
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
        }


        $resultRedirect->setPath($returnAddPackage);
        return $resultRedirect;
    }
}
