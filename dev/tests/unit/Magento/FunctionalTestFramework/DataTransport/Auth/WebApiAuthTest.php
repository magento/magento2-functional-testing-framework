<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataTransport\Auth;

use tests\unit\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\DataTransport\Auth\WebApiAuth;
use ReflectionClass;

class WebApiAuthTest extends MagentoTestCase
{
    const LOGIN = 'webApiAuthCacheHitTestUser';

    const CACHED_TOKEN = 'cached-admin-token-value';

    /**
     * Original CredentialStore singleton instance, restored in tearDown().
     *
     * @var CredentialStore|null
     */
    private $originalCredentialStoreInstance;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        putenv('MAGENTO_ADMIN_USERNAME=' . self::LOGIN);

        // Seed the cache so hasExistingToken(self::LOGIN) returns true (fresh timestamp, no expiry configured).
        $this->setStaticProperty(WebApiAuth::class, 'adminAuthTokens', [self::LOGIN => self::CACHED_TOKEN]);
        $this->setStaticProperty(WebApiAuth::class, 'adminAuthTokenTimestamps', [self::LOGIN => time()]);

        // Inject a mock CredentialStore whose getSecret()/decryptSecretValue() are expected NEVER to be called.
        // Real CredentialStore::getInstance() runs GPG/openssl decryption in the constructor path (via
        // decryptSecretValue()); the whole point of this test is to prove the cache-hit branch in
        // getAdminToken() short-circuits before that expensive decrypt is ever reached. A partial mock built
        // with disableOriginalConstructor() lets us assert "never called" without needing a real credentials
        // file or vault/AWS configuration in the test environment.
        $credentialStoreMock = $this->getMockBuilder(CredentialStore::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSecret', 'decryptSecretValue'])
            ->getMock();
        $credentialStoreMock->expects($this->never())->method('getSecret');
        $credentialStoreMock->expects($this->never())->method('decryptSecretValue');

        $credentialStoreReflection = new ReflectionClass(CredentialStore::class);
        $instanceProperty = $credentialStoreReflection->getProperty('INSTANCE');
        $instanceProperty->setAccessible(true);
        $this->originalCredentialStoreInstance = $instanceProperty->getValue();
        $instanceProperty->setValue(null, $credentialStoreMock);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        putenv('MAGENTO_ADMIN_USERNAME');

        $this->setStaticProperty(WebApiAuth::class, 'adminAuthTokens', []);
        $this->setStaticProperty(WebApiAuth::class, 'adminAuthTokenTimestamps', []);

        $credentialStoreReflection = new ReflectionClass(CredentialStore::class);
        $instanceProperty = $credentialStoreReflection->getProperty('INSTANCE');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null, $this->originalCredentialStoreInstance);

        parent::tearDown();
    }

    /**
     * A cache hit for the login must return the cached token immediately and must not trigger
     * password decryption via CredentialStore.
     *
     * @return void
     */
    public function testGetAdminTokenReturnsCachedTokenWithoutDecryptingPassword()
    {
        $token = WebApiAuth::getAdminToken();

        self::assertSame(self::CACHED_TOKEN, $token);
    }

    /**
     * Set a private static property's value via reflection.
     *
     * @param string $class
     * @param string $property
     * @param mixed  $value
     * @return void
     */
    private function setStaticProperty(string $class, string $property, $value): void
    {
        $reflection = new ReflectionClass($class);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue(null, $value);
    }
}
