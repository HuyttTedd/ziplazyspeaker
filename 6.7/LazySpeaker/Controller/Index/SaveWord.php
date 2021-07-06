<?php
namespace Mageplaza\LazySpeaker\Controller\Index;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Mageplaza\LazySpeaker\Model\WordFactory;
use Mageplaza\LazySpeaker\Model\ResourceModel\Word\CollectionFactory as WordCollectionFactory;
/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class SaveWord extends Action
{
    protected $wordFactory;

    protected $wordCollectionFactory;

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
     */
    public function __construct(
        Context $context,
        WordFactory $wordFactory,
        \Magento\Customer\Model\Session $_customerSession,
        WordCollectionFactory $wordCollectionFactory

    ) {
        $this->wordCollectionFactory = $wordCollectionFactory;
        $this->_customerSession = $_customerSession;
        $this->wordFactory = $wordFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if(!$this->_customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You are not logged in. Please log in and try again.'));
            return $this->_customerSession->authenticate();
        }

        $getImg = $this->getRequest()->getFiles('image');
        $isImg = false;
        if($getImg['type']) {
            $imgType = ['gif','jpg','jpeg','png'];
            foreach ($imgType as $type) {
                if(stristr($getImg['type'], $type)) {
                    $isImg = true;
                }
            }
        }
        if($getImg['tmp_name'] && $getImg['name'] && $isImg) {
            $filename = $getImg['tmp_name'];
            $client_id = '67fd839d20ce847';
            $handle = fopen($filename, 'r');
            $data = fread($handle, filesize($filename));
            $pvars = array('image' => base64_encode($data));
            $timeout = 30;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
            $out = curl_exec($curl);
            curl_close($curl);
            $pms = json_decode($out, true);
            $url = $pms['data']['link'];
        }
        $dataRequest = $this->getRequest()->getParams();

//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info($dataRequest);
//        die;
        $userId = $this->_customerSession->getId();
        $wordCollection = $this->wordCollectionFactory->create();
        $lastPosition = $wordCollection->addFieldToFilter('user_id', ['eq' => $userId])->count();
        $word = $this->wordFactory->create();
        $arrData = [
            'word' => trim($dataRequest['word']),
            'word_type' => NULL,
            'status' => 1,
            'word_class' => $dataRequest['word_class'],
            'image' => NULL,
            'meaning' => $dataRequest['meaning'],
            'sentence_example' => $dataRequest['sentence_example'],
            'sentence_meaning' => $dataRequest['sentence_meaning'],
            'note' => $dataRequest['note'],
            'youtube_link' => NULL,
            'word_position' => $lastPosition + 1,
            'user_id' => (int)$userId
        ];
        $resultRedirect = $this->resultRedirectFactory->create();

        if(isset($dataRequest['word_id'])) {
            $wordId = $dataRequest['word_id'];
            $returnWordManager = $this->_url->getUrl('lazyspeaker/index/editword', ['word' => $wordId, '_current' => true]);
            if($word->load($wordId)->getData()) {
                $word->addData($arrData);
            } else {
                $returnWordManager = $this->_url->getUrl('lazyspeaker/index/', ['_current' => true]);
                $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
                $resultRedirect->setPath($returnWordManager);
                return $resultRedirect;
            }
        } else {
            $returnWordManager = $this->_url->getUrl('lazyspeaker/index/createnewword', ['_secure' => true]);
            $word->addData($arrData);
        }

        try{
            $word->save();
            $this->messageManager->addSuccessMessage(__('Save successfully!'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Somthing went wrong! Please try again.'));
        }

        $resultRedirect->setPath($returnWordManager);
        return $resultRedirect;
    }
}
