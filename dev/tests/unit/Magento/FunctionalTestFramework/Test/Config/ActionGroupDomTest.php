<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tests\unit\Magento\FunctionalTestFramework\Test\Config;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Config\ActionGroupDom;
use PHPUnit\Framework\TestCase;

class ActionGroupDomTest extends TestCase
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

        $actionDom = new ActionGroupDom($sampleXml, 'test.xml');
        $this->expectException(XmlException::class);

        // an exception is only thrown for Action Group files.
        $actionDom->initDom($sampleXml, 'dupeStepKeyActionGroup.xml');
    }
}
