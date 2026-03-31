<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Suite\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Suite\Config\SuiteDom;
use tests\unit\Util\MagentoTestCase;

class SuiteDomTest extends MagentoTestCase
{
    /**
     * Suite before hook duplicate stepKey validation
     *
     * @return void
     */
    public function testSuiteBeforeHookStepKeyDuplicateValidation(): void
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
     *
     * @return void
     */
    public function testSuiteAfterHookStepKeyDuplicateValidation(): void
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

    /**
     * Suite with unique stepKeys in before and after hooks collects no validation errors
     *
     * @return void
     */
    public function testSuiteHooksWithUniqueStepKeysValidateSuccessfully(): void
    {
        $sampleXml = '<suites>
            <suite name="happyPathSuite">
                <before>
                    <comment userInput="step1" stepKey="beforeStep1"/>
                    <comment userInput="step2" stepKey="beforeStep2"/>
                </before>
                <after>
                    <comment userInput="step3" stepKey="afterStep1"/>
                    <comment userInput="step4" stepKey="afterStep2"/>
                </after>
            </suite>
        </suites>';

        $exceptionCollector = new ExceptionCollector();
        new SuiteDom($sampleXml, 'uniqueStepKeysSuite.xml', $exceptionCollector);

        $this->assertEmpty($exceptionCollector->getErrors());
        $exceptionCollector->throwException();
    }

    /**
     * Same stepKey may appear in before and after; uniqueness is enforced per hook only
     *
     * @return void
     */
    public function testSuiteSameStepKeyInBeforeAndAfterIsValid(): void
    {
        $sampleXml = '<suites>
            <suite name="crossHookSuite">
                <before>
                    <comment userInput="inBefore" stepKey="sharedKey"/>
                </before>
                <after>
                    <comment userInput="inAfter" stepKey="sharedKey"/>
                </after>
            </suite>
        </suites>';

        $exceptionCollector = new ExceptionCollector();
        new SuiteDom($sampleXml, 'crossHookStepKeySuite.xml', $exceptionCollector);

        $this->assertEmpty($exceptionCollector->getErrors());
        $exceptionCollector->throwException();
    }

    /**
     * Suite with only a before hook and unique stepKeys validates successfully
     *
     * @return void
     */
    public function testSuiteWithOnlyBeforeHookValidatesSuccessfully(): void
    {
        $sampleXml = '<suites>
            <suite name="beforeOnlySuite">
                <before>
                    <comment userInput="a" stepKey="keyA"/>
                    <comment userInput="b" stepKey="keyB"/>
                </before>
            </suite>
        </suites>';

        $exceptionCollector = new ExceptionCollector();
        new SuiteDom($sampleXml, 'beforeOnlySuite.xml', $exceptionCollector);

        $this->assertEmpty($exceptionCollector->getErrors());
        $exceptionCollector->throwException();
    }

    /**
     * Suite with only an after hook and unique stepKeys validates successfully
     *
     * @return void
     */
    public function testSuiteWithOnlyAfterHookValidatesSuccessfully(): void
    {
        $sampleXml = '<suites>
            <suite name="afterOnlySuite">
                <after>
                    <comment userInput="x" stepKey="keyX"/>
                    <comment userInput="y" stepKey="keyY"/>
                </after>
            </suite>
        </suites>';

        $exceptionCollector = new ExceptionCollector();
        new SuiteDom($sampleXml, 'afterOnlySuite.xml', $exceptionCollector);

        $this->assertEmpty($exceptionCollector->getErrors());
        $exceptionCollector->throwException();
    }

    /**
     * Suite with no before/after hooks still passes single-suite and stepKey checks
     *
     * @return void
     */
    public function testSuiteWithNoHooksValidatesSuccessfully(): void
    {
        $sampleXml = '<suites>
            <suite name="minimalSuite">
            </suite>
        </suites>';

        $exceptionCollector = new ExceptionCollector();
        new SuiteDom($sampleXml, 'minimalSuite.xml', $exceptionCollector);

        $this->assertEmpty($exceptionCollector->getErrors());
        $exceptionCollector->throwException();
    }
}
