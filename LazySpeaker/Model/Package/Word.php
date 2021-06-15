<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LazySpeaker\Model\Package;

use Magento\Framework\Model\AbstractModel;
use Mageplaza\LazySpeaker\Model\ResourceModel\PackageWord as PackageWordResource;


/**
 * Class Word
 * @package Mageplaza\LazySpeaker\Model
 * @method getWord()
 * @method getWordClass()
 * @method getWordType()
 * @method getStatus()
 * @method getImage()
 * @method getMeaning()
 * @method getSentenceExample()
 * @method getSentenceMeaning()
 * @method getNote()
 */
class Word extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(PackageWordResource::class);
    }

}
