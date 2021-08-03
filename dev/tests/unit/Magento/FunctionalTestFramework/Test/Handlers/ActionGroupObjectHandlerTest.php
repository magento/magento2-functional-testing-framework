<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use Exception;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use tests\unit\Util\ActionGroupArrayBuilder;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\ObjectHandlerUtil;

/**
 * Class ActionGroupObjectHandlerTest
 */
class ActionGroupObjectHandlerTest extends MagentoTestCase
{
    /**
     * Validate getObject should throw exception if test extends from itself.
     *
     * @return void
     * @throws Exception
     */
    public function testGetTestObjectWithInvalidExtends(): void
    {
        // Set up action group data
        $nameOne = 'actionGroupOne';
        $actionGroupOne = (new ActionGroupArrayBuilder())
            ->withName($nameOne)
            ->withExtendedAction($nameOne)
            ->withAnnotations()
            ->withFilename()
            ->withActionObjects()
            ->build();
        ObjectHandlerUtil::mockActionGroupObjectHandlerWithData(['actionGroups' => $actionGroupOne]);

        $handler = ActionGroupObjectHandler::getInstance();

        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage('Mftf Action Group can not extend from itself: ' . $nameOne);
        $handler->getObject('actionGroupOne');
    }

    /**
     * Validate getAllObjects should throw exception if test extends from itself
     *
     * @return void
     * @throws Exception
     */
    public function testGetAllTestObjectsWithInvalidExtends()
    {
        // Set up action group data
        $nameOne = 'actionGroupOne';
        $nameTwo = 'actionGroupTwo';
        $actionGroupOne = (new ActionGroupArrayBuilder())
            ->withName($nameOne)
            ->withExtendedAction($nameOne)
            ->withAnnotations()
            ->withFilename()
            ->withActionObjects()
            ->build();
        $actionGroupTwo = (new ActionGroupArrayBuilder())
            ->withName($nameTwo)
            ->withExtendedAction()
            ->withAnnotations()
            ->withFilename()
            ->withActionObjects()
            ->build();

        ObjectHandlerUtil::mockActionGroupObjectHandlerWithData(
            [
                'actionGroups' => array_merge(
                    $actionGroupOne,
                    $actionGroupTwo
                )
            ]
        );

        $handler = ActionGroupObjectHandler::getInstance();

        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage('Mftf Action Group can not extend from itself: ' . $nameOne);
        $handler->getAllObjects();
    }
}
