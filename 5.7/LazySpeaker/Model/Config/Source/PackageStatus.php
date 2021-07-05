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

namespace Mageplaza\LazySpeaker\Model\Config\Source;

use Mageplaza\LazySpeaker\Model\Config\AbstractSource;

/**
 * Class Events
 * @package Mageplaza\ProductFeed\Model\Config\Source
 */
class PackageStatus extends AbstractSource
{
    const NOT_SHARE_PACKAGE  = '0';
    const SHARE_PACKAGE      = '1';

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::NOT_SHARE_PACKAGE => __('Package is private'),
            self::SHARE_PACKAGE   => __('Package is public'),
        ];
    }
}
