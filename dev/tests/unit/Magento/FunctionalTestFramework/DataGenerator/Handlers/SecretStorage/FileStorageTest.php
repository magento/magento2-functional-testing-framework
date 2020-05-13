<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers\SecretStorage;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\FileStorage;
use tests\unit\Util\MagentoTestCase;
use AspectMock\Test as AspectMock;

class FileStorageTest extends MagentoTestCase
{

    /**
     * Test basic encryption/decryption functionality in FileStorage class.
     */
    public function testBasicEncryptDecrypt()
    {
        $testKey = 'magento/myKey';
        $testValue = 'myValue';

        AspectMock::double(FileStorage::class, [
            'readInCredentialsFile' => ["$testKey=$testValue"]
        ]);

        $fileStorage = new FileStorage();
        $encryptedCred = $fileStorage->getEncryptedValue($testKey);

        // assert the value we've gotten is in fact not identical to our test value
        $this->assertNotEquals($testValue, $encryptedCred);

        $actualValue = $fileStorage->getDecryptedValue($encryptedCred);

        // assert that we are able to successfully decrypt our secret value
        $this->assertEquals($testValue, $actualValue);
    }
}
