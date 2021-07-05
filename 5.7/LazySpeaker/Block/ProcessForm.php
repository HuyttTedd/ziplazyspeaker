<?php

namespace Mageplaza\LazySpeaker\Block;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollection;
use Mageplaza\LazySpeaker\Model\ResourceModel\Package\CollectionFactory as PackageCollection;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;
use Mageplaza\LazySpeaker\Helper\Data;

class ProcessForm extends Template
{

    protected $formKey;

    protected $wordCollection;

    protected $_customerSession;

    protected $resultRedirectFactory;

    protected $packageWordCollection;

    protected $packageCollection;

    protected $packageFactory;

    public $helperData;

    protected $_registry;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        Context $context,
        FormKey $formKey,
        WordCollection $wordCollection,
        \Magento\Customer\Model\Session $_customerSession,
        MessageManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        PackageWordCollection $packageWordCollection,
        PackageFactory $packageFactory,
        Data $helperData,
        PackageCollection $packageCollection,
        \Magento\Framework\Registry $registry
    )
    {
        $this->packageCollection = $packageCollection;
        $this->packageFactory = $packageFactory;
        $this->messageManager = $messageManager;
        $this->_customerSession = $_customerSession;
        $this->wordCollection = $wordCollection;
        $this->formKey = $formKey;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->packageWordCollection = $packageWordCollection;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    public function getWordFormAction()
    {
        return $this->getUrl('lazyspeaker/index/saveword', ['_secure' => true]);
    }

    public function getPackageFormAction()
    {
        return $this->getUrl('lazyspeaker/index/savepackage', ['_secure' => true]);
    }

    public function getPostFormAction()
    {
        return $this->getUrl('lazyspeaker/index/savepost', ['_secure' => true]);
    }

    public function editWord($id)
    {
        return $this->getUrl('lazyspeaker/index/edit/word/'.$id, ['_secure' => true]);
    }

    public function createPost()
    {
        return $this->getUrl('lazyspeaker/index/createpost', ['_secure' => true]);
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getOptionWordClass()
    {
        $arrOption = [
            ['label' => __('~ Select Type ~'), 'value' => 'none'],
            ['label' => __('Nouns'), 'value' => 'n'],
            ['label' => __('Verb'), 'value' => 'v'],
            ['label' => __('Adjective'), 'value' => 'adj'],
            ['label' => __('Adverb'), 'value' => 'adv'],
            ['label' => __('Prepositions'), 'value' => 'pre'],
            ['label' => __('Others'), 'value' => 'others'],
        ];

        return $arrOption;
    }


    //from viewallpackage set data to session by ajax, if success -> redirect
    public function getShareDataPackage() {
        $userId = $this->_customerSession->getCustomer()->getId();
        //block hacker
        $resultRedirect = $this->resultRedirectFactory->create();
        $returnViewPacakge = $this->getUrl('lazyspeaker/index/viewallpackage', ['_secure' => true]);
        $resultRedirect->setPath($returnViewPacakge);
        //block hacker
        // important!
        $packageIds = $this->_customerSession->getPackIdsShare();
        $this->_customerSession->unsPackIdsShare();
        if(!$packageIds) {
            $this->messageManager->addErrorMessage(__('Something went wrong. Please try again.'));
            return $resultRedirect;
        }
        $data = [];
        foreach ($packageIds as $id) {
            if($this->helperData->checkOwnPackage($userId, $id)) {

                //lấy key chung theo name vì vậy package anme không được lặp lại
                $packageName = $this->helperData->getPackageData($id)['name'];
                $allWords = $this->packageWordCollection->create()
                    ->addFieldToFilter('package_id', ['eq' => $id])
                    ->addFieldToSelect('word_id');
                foreach ($allWords as $word) {
                $data[$packageName][] = $this->helperData->getWordData($word->getData('word_id'));
                }
                $data[$packageName][] = $id;
                $data[$packageName][] = strtolower($packageName).$id;
                $data[$packageName][] = $packageName;
            } else {
                $this->messageManager->addErrorMessage(__('Something went wrong. Please try again.'));
                return $resultRedirect;
            }
        }
        return $data;
    }

    public function getAllLanguagesForWords($packId) {
        return $this->packageFactory->create()->load($packId)->getData('package_language');
    }


    public function getAllWords()
    {
        try {
            $customerId = $this->_customerSession->getCustomer()->getId();
            if ($customerId) {
                return $this->wordCollection->create()->addFieldToFilter('user_id', ['eq' => $customerId]);
            }
            return [];
        } catch (\Exception $e) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $returnHomePage = $this->getUrl('lazyspeaker/index/createnewword', ['_secure' => true]);
            $this->messageManager->addErrorMessage(__('Something went wrong. Please try again.'));
            $resultRedirect->setPath($returnHomePage);
            return $resultRedirect;
        }
    }

    public function getAllPackages()
    {
        try {
            $customerId = $this->_customerSession->getCustomer()->getId();
            if ($customerId) {
                return $this->packageCollection->create()->addFieldToFilter('user_id', ['eq' => $customerId]);
            }
            return [];
        } catch (\Exception $e) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $returnHomePage = $this->getUrl('lazyspeaker/index/createnewword', ['_secure' => true]);
            $this->messageManager->addErrorMessage(__('Something went wrong. Please try again.'));
            $resultRedirect->setPath($returnHomePage);
            return $resultRedirect;
        }
    }
}
