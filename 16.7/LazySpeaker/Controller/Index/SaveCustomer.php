<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Api\CustomerRepositoryInterface;
/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class SaveCustomer extends Action
{

    protected $customerRepository;

    protected $_customerSession;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */


    /**
     * Save constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $_customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $_customerSession,
        CustomerRepositoryInterface $customerRepository

    ) {
        $this->customerRepository = $customerRepository;
        $this->_customerSession   = $_customerSession;
        parent::__construct($context);
    }

    public function execute()
    {
        if(!$this->_customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You are not logged in. Please log in and try again.'));
            return $this->_customerSession->authenticate();
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $data = $this->getRequest()->getParams();
            $customerId = $this->_customerSession->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);
            $customer->setFirstname($data['name']);
            $this->customerRepository->save($customer);
            $this->messageManager->addSuccessMessage(__('Save successfully!'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
        }
        $returnCustomerPage = $this->_url->getUrl('lazyspeaker/index/customer', ['_current' => true]);
        $resultRedirect->setPath($returnCustomerPage);
        return $resultRedirect;
    }
}
