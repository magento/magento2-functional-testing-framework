<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;

class DataObjectHandlerReflectionUtil implements ObjectHandlerReflectionUtilInterface
{
    /**
     * DataObjectHandlerReflectionUtil constructor.
     */
    private function __construct()
    {
        //private constructor
    }

    /**
     * Sets the Data Object Handler singleton instance as the mocked object.
     *
     * @param PHPUnit_Framework_MockObject_MockObject $mockObject
     */
    public static function setupMock($mockObject)
    {
        $setStatic = function ($val) {
            static::$DATA_OBJECT_HANDLER = $val;
        };

        $setMockStatic = $setStatic->bindTo(null, DataObjectHandler::class);
        $setMockStatic($mockObject);
    }

    /**
     * Sets the Data Object Handler Instance to a null value for re-initialization.
     */
    public static function tearDown()
    {
        $resetStatic = function () {
            static::$DATA_OBJECT_HANDLER = null;
        };

        $resetMockStatic = $resetStatic->bindTo(null, DataObjectHandler::class);
        $resetMockStatic();
    }
}