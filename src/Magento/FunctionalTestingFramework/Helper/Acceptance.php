<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Helper;

use Magento\FunctionalTestingFramework\Module\MagentoWebDriver;
use Codeception\Module;

/**
 * Class Acceptance
 *
 * Define global actions
 * All public methods declared in helper class will be available in $I
 */
class Acceptance extends Module
{
    /**
     * Reconfig WebDriver.
     *
     * @param string $config
     * @param string $value
     * @return void
     */
    public function changeConfiguration($config, $value)
    {
        $this->getModule(MagentoWebDriver::class)->_reconfigure([$config => $value]);
    }

    /**
     * Get WebDriver configuration.
     *
     * @param string $config
     * @return string
     */
    public function getConfiguration($config)
    {
        return $this->getModule(MagentoWebDriver::class)->_getConfig($config);
    }
}
