<?php
namespace Mageplaza\LazySpeaker\Block;

use Magento\Framework\Data\Form\FormKey;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;

class ReadWord extends Template
{

    protected $formKey;

    public function __construct(
        Context $context,
        FormKey $formKey
    )
    {
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
