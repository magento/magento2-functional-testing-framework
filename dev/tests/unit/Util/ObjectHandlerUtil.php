<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\OperationDefinitionParser;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;

class ObjectHandlerUtil
{
    /**
     * @var ObjectHandlerUtil
     */
    private static $instance;

    /**
     * ObjectHandlerUtil constructor.
     */
    private function __construct()
    {
        // private constructor
    }
    /**
     * Static singleton get function
     *
     * @return ObjectHandlerUtil
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new ObjectHandlerUtil();
        }

        return self::$instance;
    }
    /**
     * Function used to set mock for Operation parser.
     * @param array $data
     * @throws \Exception
     */
    public function setMockOperationParserOutput($data)
    {
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
     * Function which clears the mock handler context from the ObjectHandlerUtil class.
     * Should be run after a test class has executed.
     *
     * @return void
     */
    public function clearMockObjectHandlerUtil()
    {
        AspectMock::clean(ObjectHandlerUtil::class);
    }
}
