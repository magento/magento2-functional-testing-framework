<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Util\ActionMergeUtil;
use tests\unit\Util\MagentoTestCase;

class ActionMergeUtilTest extends MagentoTestCase
{
    /**
     * Test Exception Handler for merging actions
     *
     * @throws \Exception
     */
    public function testMergeActionsException()
    {
        $testActionMergeUtil = new ActionMergeUtil(null, null);

        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $this->expectExceptionMessage("Could not resolve entity reference \"{{someEntity.entity}}\" " .
            "in Action with stepKey \"fakeAction\".\n" .
            "Exception occurred parsing action at StepKey \"fakeAction\"");

        $testActionMergeUtil->resolveActionSteps(["merge123" => $actionObject]);
    }
}
