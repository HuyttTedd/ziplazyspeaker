<?php

namespace Mageplaza\LazySpeaker\Block;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollection;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord\CollectionFactory as PackageWordCollection;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Mageplaza\LazySpeaker\Model\PackageFactory;

class ViewAllPackage extends Template
{

    protected $formKey;

    protected $wordCollection;

    protected $_customerSession;

    protected $resultRedirectFactory;

    protected $packageWordCollection;

    protected $packageFactory;

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
        PackageFactory $packageFactory
    )
    {
        $this->packageFactory = $packageFactory;
        $this->messageManager = $messageManager;
        $this->_customerSession = $_customerSession;
        $this->wordCollection = $wordCollection;
        $this->formKey = $formKey;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->packageWordCollection = $packageWordCollection;
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

}
