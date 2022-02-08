<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers\SecretStorage;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\FileStorage;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use ReflectionClass;
use ReflectionException;
use tests\unit\Util\MagentoTestCase;

class FileStorageTest extends MagentoTestCase
{
    /**
     * Test basic encryption/decryption functionality in FileStorage class.
     * @throws TestFrameworkException|ReflectionException
     */
    public function testBasicEncryptDecrypt(): void
    {
        $testKey = 'magento/myKey';
        $testValue = 'myValue';
        $cred = ["$testKey=$testValue"];

        $fileStorage = new FileStorage();
        $reflection = new ReflectionClass(FileStorage::class);

        // Emulate initialize() function result with the test credentials
        $reflectionMethod = $reflection->getMethod('encryptCredFileContents');
        $reflectionMethod->setAccessible(true);
        $secretData = $reflectionMethod->invokeArgs($fileStorage, [$cred]);

        // Set encrypted test credentials to the private 'secretData' property
        $reflectionProperty = $reflection->getProperty('secretData');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($fileStorage, $secretData);

        $encryptedCred = $fileStorage->getEncryptedValue($testKey);

        // assert the value we've gotten is in fact not identical to our test value
        $this->assertNotEquals($testValue, $encryptedCred);

        $actualValue = $fileStorage->getDecryptedValue($encryptedCred);

        // assert that we are able to successfully decrypt our secret value
        $this->assertEquals($testValue, $actualValue);
    }

    /**
     * Test empty value encryption/decryption functionality in FileStorage class.
     * @return void
     * @throws TestFrameworkException|ReflectionException
     */
    public function testEmptyValueEncryptDecrypt(): void
    {
        $this->expectException(TestFrameworkException::class);

        $testKey = 'magento/myKey';
        $cred = ["$testKey"];

        $fileStorage = new FileStorage();
        $reflection = new ReflectionClass(FileStorage::class);

        // Emulate initialize() function result with the test credentials
        $reflectionMethod = $reflection->getMethod('encryptCredFileContents');
        $reflectionMethod->setAccessible(true);
        $secretData = $reflectionMethod->invokeArgs($fileStorage, [$cred]);

        // Set encrypted test credentials to the private 'secretData' property
        $reflectionProperty = $reflection->getProperty('secretData');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($fileStorage, $secretData);

        $fileStorage->getEncryptedValue($testKey);
    }
}
