<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

class ViewAllWord extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $_customerSession;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $_customerSession
    )
    {
        $this->_customerSession = $_customerSession;
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        if(!$this->_customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You are not logged in. Please log in and try again.'));
            return $this->_customerSession->authenticate();
        }
        return $this->_pageFactory->create();
    }
}
