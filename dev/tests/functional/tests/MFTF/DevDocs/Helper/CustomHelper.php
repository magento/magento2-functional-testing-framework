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
        float $test,
        array $module = [],
        $superBla = null,
        $bla = 'blaValue',
        array $arraysomething = []
    ) {
        print("this is it: " . $url . PHP_EOL);
        sleep(4);
    }
}
