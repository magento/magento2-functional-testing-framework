<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module;

use Codeception\Module as CodeceptionModule;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Class MagentoActionProxies
 *
 * Contains all proxy functions whose corresponding MFTF actions need to be accessible for AcceptanceTester $I
 *
 * @package Magento\FunctionalTestingFramework\Module
 */
class MagentoActionProxies extends CodeceptionModule
{
    // TODO: placeholder for proxy functions currently in MagentoWebDriver (MQE-1904)
}
