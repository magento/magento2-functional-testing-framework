<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\FunctionalTestingFramework\Module\MagentoSequence;

if (!function_exists('msq')) {
    /**
     * Return unique sequence within test.
     *
     * @param int|string|null $id Optional identifier for the sequence.
     * @return string The generated unique sequence.
     */
    function msq(int|string|null $id = null): string
    {
        if ($id !== null && isset(MagentoSequence::$hash[$id])) {
            return MagentoSequence::$hash[$id];
        }

        $prefix = MagentoSequence::$prefix ?? '';
        $sequence = $prefix . uniqid('', true); // Use true for high-entropy ID

        if ($id !== null) {
            MagentoSequence::$hash[$id] = $sequence; // Avoid dynamic properties
        }

        return $sequence;
    }
}

if (!function_exists('msqs')) {
    /**
     * Return unique sequence within suite.
     *
     * @param int|string|null $id Optional identifier for the suite sequence.
     * @return string The generated unique suite sequence.
     */
    function msqs(int|string|null $id = null): string
    {
        if ($id !== null && isset(MagentoSequence::$suiteHash[$id])) {
            return MagentoSequence::$suiteHash[$id];
        }

        $prefix = MagentoSequence::$prefix ?? '';
        $sequence = $prefix . uniqid('', true); // Use true for high-entropy ID

        if ($id !== null) {
            MagentoSequence::$suiteHash[$id] = $sequence; // Avoid dynamic properties
        }

        return $sequence;
    }
}
