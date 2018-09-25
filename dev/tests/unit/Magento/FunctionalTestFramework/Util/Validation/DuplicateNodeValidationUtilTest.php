<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Util\Validation\DuplicateNodeValidationUtil;
use Magento\FunctionalTestingFramework\Util\Validation\NameValidationUtil;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;

class DuplicateNodeValidationUtilTest extends MagentoTestCase
{
    /**
     * Validate Util flags duplicates in unique Identifiers
     */
    public function testTestActionValidation()
    {
        // Test Data
        $xml = '<tests>
                    <test name="test">
                        <comment userInput="input1" stepKey="key1"/>
                        <comment userInput="input2" stepKey="key1"/>
                    </test>
                </tests>
                ';
        $uniqueIdentifier = "stepKey";
        $filename = "file";
        $testName = "test";

        // Perform Test
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        $testNode = $dom->getElementsByTagName('test')->item(0);

        $exceptionCollector = new ExceptionCollector();
        $validator = new DuplicateNodeValidationUtil($uniqueIdentifier, $exceptionCollector);
        $validator->validateChildUniqueness(
            $testNode,
            $filename,
            $testName
        );
        $this->expectException(\Exception::class);
        $exceptionCollector->throwException();
    }
}
