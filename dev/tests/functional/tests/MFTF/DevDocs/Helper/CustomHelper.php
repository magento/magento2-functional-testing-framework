<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MFTF\DevDocs\Helper;

use Magento\FunctionalTestingFramework\Helper\Helper;

class CustomHelper extends Helper
{
    /**
     * Custom helper.
     *
     * @param string $url
     * @param mixed  $test
     * @param bool   $bool
     * @param int    $int
     * @param float  $float
     * @param array  $module
     * @param null   $superBla
     * @param mixed  $bla
     * @param array  $arraysomething
     * @return void
     */
    public function goTo(
        string $url,
        $test,
        bool $bool,
        int $int,
        float $float,
        array $module = [],
        $superBla = null,
        $bla = 'blaValue',
        array $arraysomething = ['key' => 'value', 'test']
    ) {
        print('Hello, this is custom helper which provides an ability to write custom solutions.' . PHP_EOL);
        print('string $url = ' . $url . PHP_EOL);
        print('$test = ' . $test . PHP_EOL);
        print('$bool = ' . $bool . PHP_EOL);
        print('$int = ' . $int . PHP_EOL);
        print('$float = ' . $float . PHP_EOL);
        print('array $module = [' . implode(', ', $module) . ']' . PHP_EOL);
        print('$superBla = ' . $superBla . PHP_EOL);
        print('$bla = ' . $bla . PHP_EOL);
        print('array $arraysomething = [' . implode(', ', $arraysomething) . ']' . PHP_EOL);
    }
}
