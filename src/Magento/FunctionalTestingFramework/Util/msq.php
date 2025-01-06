<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
use Magento\FunctionalTestingFramework\Module\MagentoSequence;

if (!function_exists('msq')) {
    /**
     * Return unique sequence within test.
     *
     * @param null $id
     * @return string
     */
    function msq(?string $id = null)
    {
        if ($id and isset(MagentoSequence::$hash[$id])) {
            return MagentoSequence::$hash[$id];
        }
        $prefix = MagentoSequence::$prefix;
        $sequence = $prefix . uniqid();
        if ($id) {
            MagentoSequence::$hash[$id] = $sequence;
        }
        return $sequence;
    }
}

if (!function_exists('msqs')) {
    /**
     * Return unique sequence within suite.
     *
     * @param null $id
     * @return string
     */
    function msqs(?string $id = null)
    {
        if ($id and isset(MagentoSequence::$suiteHash[$id])) {
            return MagentoSequence::$suiteHash[$id];
        }
        $prefix = MagentoSequence::$prefix;
        $sequence = $prefix . uniqid();
        if ($id) {
            MagentoSequence::$suiteHash[$id] = $sequence;
        }
        return $sequence;
    }
}
