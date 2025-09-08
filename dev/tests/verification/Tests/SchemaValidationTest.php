<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use ReflectionProperty;
use tests\util\MftfTestCase;

class SchemaValidationTest extends MftfTestCase
{
    /**
     * Test generation of a test referencing an action group with no arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testInvalidTestSchema()
    {
        $config = MftfApplicationConfig::getConfig();
        $property = new ReflectionProperty(MftfApplicationConfig::class, 'debugLevel');
        $property->setAccessible(true);
        $property->setValue($config, MftfApplicationConfig::LEVEL_DEVELOPER);

        $testFile = ['testFile.xml' => "<tests><test name='testName'><annotations>a</annotations></test></tests>"];
        $expectedError = TESTS_MODULE_PATH .
            DIRECTORY_SEPARATOR .
            "TestModule" .
            DIRECTORY_SEPARATOR .
            "Test" .
            DIRECTORY_SEPARATOR .
            "testFile.xml";
        $this->validateSchemaErrorWithTest($testFile, 'Test', $expectedError);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $config = MftfApplicationConfig::getConfig();
        $property = new ReflectionProperty(MftfApplicationConfig::class, 'debugLevel');
        $property->setAccessible(true);
        $property->setValue($config, MftfApplicationConfig::LEVEL_DEFAULT);
    }
}
