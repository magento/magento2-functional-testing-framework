<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Suite\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Suite\Config\SuiteDom;
use tests\unit\Util\MagentoTestCase;

class SuiteDomTest extends MagentoTestCase
{
    /**
     * Suite before hook duplicate stepKey validation
     */
    public function testSuiteBeforeHookStepKeyDuplicateValidation()
    {
        $sampleXml = '<suites>
            <suite name="suiteName">
                <before>
                    <comment userInput="step1" stepKey="key1"/>
                    <comment userInput="step2" stepKey="key1"/>
                </before>
            </suite>
        </suites>';

        $exceptionCollector = new ExceptionCollector();
        new SuiteDom($sampleXml, 'dupeStepKeySuite.xml', $exceptionCollector);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/stepKey: key1 is used more than once. \(Parent: suiteName\\/before\)/");
        $exceptionCollector->throwException();
    }

    /**
     * Suite after hook duplicate stepKey validation
     */
    public function testSuiteAfterHookStepKeyDuplicateValidation()
    {
        $sampleXml = '<suites>
            <suite name="suiteName">
                <after>
                    <comment userInput="step1" stepKey="dupKey"/>
                    <comment userInput="step2" stepKey="dupKey"/>
                </after>
            </suite>
        </suites>';

        $exceptionCollector = new ExceptionCollector();
        new SuiteDom($sampleXml, 'dupeStepKeySuiteAfter.xml', $exceptionCollector);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/stepKey: dupKey is used more than once. \(Parent: suiteName\\/after\)/");
        $exceptionCollector->throwException();
    }
}
