<?php
namespace Mageplaza\LazySpeaker\Plugin;

use Magento\Customer\Controller\Account\Index;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlInterface;

class Customer {
    protected $resultForwardFactory;

    protected $resultRedirectFactory;

    protected $url;

    public function __construct(
        RedirectFactory $resultRedirectFactory,
        UrlInterface $url
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->url = $url;
    }
    public function afterExecute(
        Index $subject,
        $result
    )
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $returnLazyCustomer = $this->url->getUrl('lazyspeaker/index/customer', ['_secure' => true]);
        $resultRedirect->setPath($returnLazyCustomer);
        return $resultRedirect;
    }
}
