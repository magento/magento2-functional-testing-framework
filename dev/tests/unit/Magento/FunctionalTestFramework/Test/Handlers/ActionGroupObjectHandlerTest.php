<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use Exception;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Parsers\ActionGroupDataParser;
use ReflectionProperty;
use tests\unit\Util\ActionGroupArrayBuilder;
use tests\unit\Util\MagentoTestCase;

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
        $this->mockActionGroupObjectHandlerWithData(['actionGroups' => $actionGroupOne]);

        $handler = ActionGroupObjectHandler::getInstance();

        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage('Mftf Action Group can not extend from itself: ' . $nameOne);
        $handler->getObject('actionGroupOne');
    }

    /**
     * Validate getAllObjects should throw exception if test extends from itself.
     *
     * @return void
     * @throws Exception
     */
    public function testGetAllTestObjectsWithInvalidExtends(): void
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

        $this->mockActionGroupObjectHandlerWithData(
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

    /**
     * Create mock action group object handler with data.
     *
     * @param array $mockData
     *
     * @return void
     */
    private function mockActionGroupObjectHandlerWithData(array $mockData): void
    {
        $actionGroupObjectHandlerProperty = new ReflectionProperty(ActionGroupObjectHandler::class, 'instance');
        $actionGroupObjectHandlerProperty->setAccessible(true);
        $actionGroupObjectHandlerProperty->setValue(null);

        $mockOperationParser = $this->createMock(ActionGroupDataParser::class);
        $mockOperationParser
            ->method('readActionGroupData')
            ->willReturn($mockData);
        $objectManager = ObjectManagerFactory::getObjectManager();
        $mockObjectManagerInstance = $this->createMock(ObjectManager::class);
        $mockObjectManagerInstance
            ->method('create')
            ->will(
                $this->returnCallback(
                    function (
                        string $class,
                        array $arguments = []
                    ) use (
                        $objectManager,
                        $mockOperationParser
                    ) {
                        if ($class === ActionGroupDataParser::class) {
                            return $mockOperationParser;
                        }

                        return $objectManager->create($class, $arguments);
                    }
                )
            );

        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue($mockObjectManagerInstance);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $actionGroupObjectHandlerProperty = new ReflectionProperty(ActionGroupObjectHandler::class, 'instance');
        $actionGroupObjectHandlerProperty->setAccessible(true);
        $actionGroupObjectHandlerProperty->setValue(null);

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue(null);
    }
}
