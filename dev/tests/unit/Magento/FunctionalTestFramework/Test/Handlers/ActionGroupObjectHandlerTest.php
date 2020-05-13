<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use AspectMock\Test as AspectMock;

use Go\Aop\Aspect;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\ActionGroupArrayBuilder;
use Magento\FunctionalTestingFramework\Test\Parsers\ActionGroupDataParser;

class ActionGroupObjectHandlerTest extends MagentoTestCase
{
    /**
     * getObject should throw exception if test extends from itself
     *
     * @throws \Exception
     */
    public function testGetTestObjectWithInvalidExtends()
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
        $this->setMockParserOutput(['actionGroups' => $actionGroupOne]);

        $handler = ActionGroupObjectHandler::getInstance();

        $this->expectException(\Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException::class);
        $this->expectExceptionMessage("Mftf Action Group can not extend from itself: " . $nameOne);
        $handler->getObject('actionGroupOne');
    }

    /**
     * getAllObjects should throw exception if test extends from itself
     *
     * @throws \Exception
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

        $this->setMockParserOutput(['actionGroups' => array_merge($actionGroupOne, $actionGroupTwo)]);

        $handler = ActionGroupObjectHandler::getInstance();

        $this->expectException(\Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException::class);
        $this->expectExceptionMessage("Mftf Action Group can not extend from itself: " . $nameOne);
        $handler->getAllObjects();
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $data
     * @throws \Exception
     */
    private function setMockParserOutput($data)
    {
        // Clear action group object handler value to inject parsed content
        $property = new \ReflectionProperty(ActionGroupObjectHandler::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = AspectMock::double(ActionGroupDataParser::class, ['readActionGroupData' => $data])->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockDataParser])
            ->make(); // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }
}
