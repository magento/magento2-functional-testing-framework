<?php

namespace MFTF\DevDocs\Helper;

use Magento\FunctionalTestingFramework\Helper\Helper;

class CustomHelper extends Helper
{
    public function goTo(string $url, $test, array $module = [], $superBla = null, $bla = 'blaValue', array $arraysomething = [])
    {
        print("this is it: " . $url . PHP_EOL);
    }
}
