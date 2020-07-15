<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Test\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Config\Dom\ValidationException;
use Magento\FunctionalTestingFramework\Test\Config\ActionGroupDom;
use tests\unit\Util\MagentoTestCase;

class ActionGroupDomTest extends MagentoTestCase
{
    /**
     * Test Action Group duplicate step key validation
     */
    public function testActionGroupDomStepKeyValidation()
    {
        $sampleXml = "<actionGroups>
            <actionGroup name=\"actionGroupWithoutArguments\">
                <wait time=\"1\" stepKey=\"waitForNothing\" />
                <wait time=\"2\" stepKey=\"waitForNothing\" />
            </actionGroup>
         </actionGroups>";

        $exceptionCollector = new ExceptionCollector();
        new ActionGroupDom($sampleXml, 'dupeStepKeyActionGroup.xml', $exceptionCollector);

        $this->expectException(\Exception::class);
        $exceptionCollector->throwException();
    }

    /**
     * Test Action Group invalid XML
     */
    public function testActionGroupDomInvalidXmlValidation()
    {
        $sampleXml = "<actionGroups>
            <actionGroup name=\"sampleActionGroup\">
                <wait>
            </actionGroup>
         </actionGroups>";

        $exceptionCollector = new ExceptionCollector();
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("XML Parse Error: invalid.xml\n");
        new ActionGroupDom($sampleXml, 'invalid.xml', $exceptionCollector);
    }

    /**
     * Test detection of two ActionGroups with the same Name in the same file.
     */
    public function testActionGroupDomDuplicateActionGroupsValidation()
    {
        $sampleXml = '<actionGroups>
            <actionGroup name="actionGroupName">
                <wait time="1" stepKey="key1" />
            </actionGroup>
            <actionGroup name="actionGroupName">
                <wait time="1" stepKey="key1" />
            </actionGroup>
        </actionGroups>';

        $exceptionCollector = new ExceptionCollector();
        new ActionGroupDom($sampleXml, 'dupeNameActionGroup.xml', $exceptionCollector);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/name: actionGroupName is used more than once./");
        $exceptionCollector->throwException();
    }
}
