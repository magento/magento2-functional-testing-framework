<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use AspectMock\Test as AspectMock;

class CredentialStoreTest extends MagentoTestCase
{

    /**
     * Test basic encryption/decryption functionality in CredentialStore class.
     */
    public function testBasicEncryptDecrypt()
    {
        $testKey = 'myKey';
        $testValue = 'myValue';

        AspectMock::double(CredentialStore::class, [
            'readInCredentialsFile' => ["$testKey=$testValue"]
        ]);

        $encryptedCred = CredentialStore::getInstance()->getSecret($testKey);

        // assert the value we've gotten is in fact not identical to our test value
        $this->assertNotEquals($testValue, $encryptedCred);

        $actualValue = CredentialStore::getInstance()->decryptSecretValue($encryptedCred);

        // assert that we are able to successfully decrypt our secret value
        $this->assertEquals($testValue, $actualValue);
    }
}
