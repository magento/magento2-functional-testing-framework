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
     * @param float  $test
     * @param array  $module
     * @param null   $superBla
     * @param string $bla
     * @param array  $arraysomething
     * @return void
     */
    public function goTo(
        string $url,
        $test,
        array $module = [],
        $superBla = null,
        $bla = 'blaValue',
        array $arraysomething = []
    ) {
        print('Hello, this is custom helper which gives an ability to write custom solutions without usage of <executeInSelenium /> and <performOn /> actions.');
        print('string $url = ' . $url . PHP_EOL);
        print('$test = ' . $test . PHP_EOL);
        print('array $module = [' . implode(', ', $module) . ']' . PHP_EOL);
        print('$superBla = ' . $superBla . PHP_EOL);
        print('$bla = ' . $url . PHP_EOL);
        print('array $arraysomething = [' . implode(', ', $arraysomething) . ']' . PHP_EOL);
    }
}
