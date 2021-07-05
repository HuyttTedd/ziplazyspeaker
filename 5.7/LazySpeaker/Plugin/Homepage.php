<?php
namespace Mageplaza\LazySpeaker\Plugin;

use Magento\Cms\Controller\Index\Index;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlInterface;

class Homepage {
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
        $returnLazyHomepage = $this->url->getUrl('lazyspeaker/index', ['_secure' => true]);
        $resultRedirect->setPath($returnLazyHomepage);
        return $resultRedirect;
    }
}
