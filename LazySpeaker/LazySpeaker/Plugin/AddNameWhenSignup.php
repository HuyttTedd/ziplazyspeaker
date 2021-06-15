<?php
namespace Mageplaza\LazySpeaker\Plugin;

use Magento\Customer\Controller\Account\CreatePost;

class AddNameWhenSignup extends CreatePost {
    public function beforeExecute(
        CreatePost $subject
    )
    {
        // because we omit name at 'create account' page => add name to valid form
        $info = $subject->getRequest()->getParams();
        $info['firstname'] = 'undefined_name';
        $info['lastname'] = 'undefined_name';
        $subject->getRequest()->setParams($info);
        return $subject;
    }
}
