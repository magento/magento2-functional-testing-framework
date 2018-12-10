<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module;

/**
 * Class MagentoAssert
 *
 * Contains all custom assert functions to be used in tests.
 *
 * @package Magento\FunctionalTestingFramework\Module
 */
class MagentoAssert extends \Codeception\Module
{
    /**
     * Asserts that all items in the array are sorted by given direction. Can be given int, string, double, dates.
     * Converts given date strings to epoch for comparison.
     *
     * @param array  $data
     * @param string $sortOrder
     * @return void
     */
    public function assertArrayIsSorted(array $data, $sortOrder = "asc")
    {
        $elementTotal = count($data);
        $message = null;

        // If value can be converted to a date and it isn't 1.1 number (strtotime is overzealous)
        if (strtotime($data[0]) !== false && !is_numeric($data[0])) {
            $message = "Array of dates converted to unix timestamp for comparison";
            $data = array_map('strtotime', $data);
        } else {
            $data = array_map('strtolower', $data);
        }

        if ($sortOrder == "asc") {
            for ($i = 1; $i < $elementTotal; $i++) {
                // $i >= $i-1
                $this->assertLessThanOrEqual($data[$i], $data[$i-1], $message);
            }
        } else {
            for ($i = 1; $i < $elementTotal; $i++) {
                // $i <= $i-1
                $this->assertGreaterThanOrEqual($data[$i], $data[$i-1], $message);
            }
        }
    }
}
