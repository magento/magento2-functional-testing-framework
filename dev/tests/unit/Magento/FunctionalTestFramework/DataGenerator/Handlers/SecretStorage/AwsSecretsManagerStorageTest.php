<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers\SecretStorage;

use Aws\SecretsManager\SecretsManagerClient;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\AwsSecretsManagerStorage;
use Aws\Result;
use tests\unit\Util\MagentoTestCase;
use ReflectionClass;

class AwsSecretsManagerStorageTest extends MagentoTestCase
{
    /**
     * Test encryption/decryption functionality in AwsSecretsManagerStorage class.
     */
    public function testEncryptAndDecrypt()
    {
        // Setup test data
        $testProfile = 'profile';
        $testRegion = 'region';
        $testLongKey = 'magento/myKey';
        $testShortKey = 'myKey';
        $testValue = 'myValue';
        $data = [
            'Name' => 'mftf/magento/' . $testShortKey,
            'SecretString' => json_encode([$testShortKey => $testValue])
        ];
        /** @var Result */
        $result = new Result($data);

        $mockClient = $this->getMockBuilder(SecretsManagerClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__call'])
            ->getMock();

        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturnCallback(function ($name, $args) use ($result) {
                return $result;
            });

        /** @var SecretsManagerClient */
        $credentialStorage = new AwsSecretsManagerStorage($testRegion, $testProfile);
        $reflection = new ReflectionClass($credentialStorage);
        $reflection_property = $reflection->getProperty('client');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($credentialStorage, $mockClient);

        // Test getEncryptedValue()
        $encryptedCred = $credentialStorage->getEncryptedValue($testLongKey);

        // Assert the value we've gotten is in fact not identical to our test value
        $this->assertNotEquals($testValue, $encryptedCred);

        // Test getDecryptedValue()
        $actualValue = $credentialStorage->getDecryptedValue($encryptedCred);

        // Assert that we are able to successfully decrypt our secret value
        $this->assertEquals($testValue, $actualValue);
    }
}
