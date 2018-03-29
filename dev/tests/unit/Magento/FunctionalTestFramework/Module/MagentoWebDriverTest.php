<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Module;

use PHPUnit\Framework\TestCase;
use Magento\FunctionalTestingFramework\Module\MagentoWebDriver;

class MagentoWebDriverTest extends TestCase
{
    /**
     * Method should return either .env file value or constant value
     */
    public function testGetDefaultWaitTimeout()
    {
        $this->assertEquals(MagentoWebDriver::getDefaultWaitTimeout(), MagentoWebDriver::DEFAULT_WAIT_TIMEOUT);

        $envFile = new \Dotenv\Dotenv(__DIR__ . '/../../../../../../', '.env.example');
        $envFile->load();

        $this->assertEquals(MagentoWebDriver::getDefaultWaitTimeout(), getenv('WAIT_TIMEOUT'));
    }
}
