<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\OperationDefinitionParser;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Parsers\ActionGroupDataParser;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\XmlParser\PageParser;
use Magento\FunctionalTestingFramework\XmlParser\SectionParser;

class ObjectHandlerUtil
{
    /**
     * Set up everything required to mock OperationDefinitionObjectHandler::getInstance() with $data value
     * @param array $data
     * @throws \Exception
     */
    public static function mockOperationHandlerWithData($data)
    {
        // Clear OperationDefinitionObjectHandler singleton if already set
        $property = new \ReflectionProperty(
            OperationDefinitionObjectHandler::class,
            'INSTANCE'
        );
        $property->setAccessible(true);
        $property->setValue(null);

        $mockOperationParser = AspectMock::double(
            OperationDefinitionParser::class,
            ["readOperationMetadata" => $data]
        )->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockOperationParser])->make();
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }

    /**
     * Set up everything required to mock DataObjectHandler::getInstance() with $data value
     *
     * @param array $data
     */
    public static function mockDataObjectHandlerWithData($data)
    {
        // Clear DataObjectHandler singleton if already set
        $property = new \ReflectionProperty(DataObjectHandler::class, "INSTANCE");
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataProfileSchemaParser = AspectMock::double(DataProfileSchemaParser::class, [
            'readDataProfiles' => $data
        ])->make();

        $mockObjectManager = AspectMock::double(ObjectManager::class, [
            'create' => $mockDataProfileSchemaParser
        ])->make();

        AspectMock::double(ObjectManagerFactory::class, [
            'getObjectManager' => $mockObjectManager
        ]);
    }

    /**
     * Set up everything required to mock PageObjectHandler::getInstance() with $data value
     *
     * @param array $data
     */
    public static function mockPageObjectHandlerWithData($data)
    {
        // clear section object handler value to inject parsed content
        $property = new \ReflectionProperty(PageObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockSectionParser = AspectMock::double(PageParser::class, ["getData" => $data])->make();
        $instance = AspectMock::double(ObjectManager::class, ['get' => $mockSectionParser])->make();
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }

    /**
     * Set up everything required to mock SectionObjectHandler::getInstance() with $data value
     *
     * @param array $data
     */
    public static function mockSectionObjectHandlerWithData($data)
    {
        // clear section object handler value to inject parsed content
        $property = new \ReflectionProperty(SectionObjectHandler::class, "INSTANCE");
        $property->setAccessible(true);
        $property->setValue(null);

        $mockSectionParser = AspectMock::double(SectionParser::class, ["getData" => $data])->make();
        $instance = AspectMock::double(ObjectManager::class, ["get" => $mockSectionParser])->make();
        AspectMock::double(ObjectManagerFactory::class, ["getObjectManager" => $instance]);
    }

    /**
     * Set up everything required to mock TestObjectHandler::getInstance() with $data value
     *
     * @param array $data
     * @throws \Exception
     */
    public static function mockTestObjectHandlerWitData($data)
    {
        // clear test object handler value to inject parsed content
        $property = new \ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = AspectMock::double(TestDataParser::class, ['readTestData' => $data])->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockDataParser])
            ->make(); // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }

    /**
     * Set up everything required to mock ActionGroupObjectHandler::getInstance() with $data value
     *
     * @param array $data
     * @throws \Exception
     */
    public static function mockActionGroupObjectHandlerWithData($data)
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
