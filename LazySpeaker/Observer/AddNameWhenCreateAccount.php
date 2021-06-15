<?php

namespace Mageplaza\LazySpeaker\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddNameWhenCreateAccount implements ObserverInterface {

    public function execute(Observer $observer)
    {
        return $this;
    }
}
