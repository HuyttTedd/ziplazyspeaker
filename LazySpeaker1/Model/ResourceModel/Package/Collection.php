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

namespace Mageplaza\LazySpeaker\Model\ResourceModel\Package;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\LazySpeaker\Model\Package;
use Mageplaza\LazySpeaker\Model\ResourceModel\Package as PackageResource;

/**
 * Class Collection
 * @package Mageplaza\LazySpeaker\Model\ResourceModel\Package
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Package::class, PackageResource::class);
    }
}
