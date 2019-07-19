<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Vault\AuthenticationStrategies\AbstractAuthenticationStrategy;
use Vault\ResponseModels\Auth;

/**
 * Class VaultTokenAuthStrategy
 */
class VaultTokenAuthStrategy extends AbstractAuthenticationStrategy
{
    /**
     * @var string
     */
    protected $token;

    /**
     * VaultTokenAuthStrategy constructor
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Returns auth for further interactions with Vault
     *
     * @return Auth
     * @throws TestFrameworkException
     */
    public function authenticate()
    {
        try {
            return new Auth(['clientToken' => $this->token]);
        } catch (\Exception $e) {
            throw new TestFrameworkException("Cannot authenticate Vault token.");
        }
    }
}
