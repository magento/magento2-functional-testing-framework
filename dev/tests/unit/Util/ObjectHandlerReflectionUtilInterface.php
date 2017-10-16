<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

interface ObjectHandlerReflectionUtilInterface
{
    /**
     * Sets an object handler singleton instance equal to the mock handler passed in.
     *
     * @param PHPUnit_Framework_MockObject_MockObject $mockObject
     * @return void
     */
    public static function setupMock($mockObject);

    /**
     * Restores the singleton object handler instance to a null state for re-initialization.
     *
     * @return void
     */
    public static function tearDown();
}