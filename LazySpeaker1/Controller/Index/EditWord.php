<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

use Magento\Framework\App\Action\Action;
use Mageplaza\LazySpeaker\Model\WordFactory;

class EditWord extends Action
{
    protected $_pageFactory;

    protected $_customerSession;

    protected $_registry;

    protected $wordFactory;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $_customerSession,
        \Magento\Framework\Registry $registry,
        WordFactory $wordFactory
    )
    {
        $this->_registry = $registry;
        $this->wordFactory = $wordFactory;
        $this->_customerSession = $_customerSession;
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function returnHomepage() {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('/');
        return $resultRedirect;
    }

    public function execute()
    {
        //check if it is my package or not
        if(!$this->_customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You are not logged in. Please log in and try again.'));
            return $this->_customerSession->authenticate();
        }
        if($id = $this->getRequest()->getParam('word')) {
            $userId = $this->_customerSession->getId();
            $checkUserIdOfWord = $this->wordFactory->create()
                ->load($id)->getData('user_id');
            if($userId == $checkUserIdOfWord) {
                return $this->_pageFactory->create();
            } else {
                $this->returnHomepage();
            }
        }
        $this->returnHomepage();
    }
}
