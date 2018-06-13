<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use tests\util\MftfTestCase;
use AspectMock\Test as AspectMock;

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
        AspectMock::double(MftfApplicationConfig::class, ['debugEnabled' => true]);
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
     * After method functionality
     * @return void
     */
    protected function tearDown()
    {
        AspectMock::clean();
    }
}
