<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tests\unit\Magento\FunctionalTestFramework\Test\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Config\Dom\ValidationException;
use Magento\FunctionalTestingFramework\Test\Config\ActionGroupDom;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;

class DomTest extends MagentoTestCase
{
    /**
     * Test Test duplicate step key validation
     */
    public function testTestStepKeyDuplicateValidation()
    {
        $sampleXml = '<tests>
            <test name="testName">
                <comment userInput="step1" stepKey="key1"/>
                <comment userInput="step2" stepKey="key1"/>
            </test>
        </tests>';

        $exceptionCollector = new ExceptionCollector();
        new ActionGroupDom($sampleXml, 'dupeStepKeyTest.xml', $exceptionCollector);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageRegExp("/stepKey: key1 is used more than once. \(Parent: testName\)/");
        $exceptionCollector->throwException();
    }

    /**
     * Test detection of two Tests with the same Name in the same file.
     */
    public function testTestNameDuplicateValidation()
    {
        $sampleXml = '<tests>
            <test name="testName">
                <comment userInput="step1" stepKey="key1"/>
            </test>
            <test name="testName">
                <comment userInput="step1" stepKey="key1"/>
            </test>
        </tests>';

        $exceptionCollector = new ExceptionCollector();
        new ActionGroupDom($sampleXml, 'dupeTestsTest.xml', $exceptionCollector);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageRegExp("/name: testName is used more than once./");
        $exceptionCollector->throwException();
    }
}
