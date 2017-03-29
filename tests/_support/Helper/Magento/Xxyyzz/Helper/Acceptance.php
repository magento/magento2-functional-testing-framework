<?php
namespace Magento\Xxyyzz\Helper;

/**
 * Class Acceptance
 *
 * Define global actions
 * All public methods declared in helper class will be available in $I
 */
class Acceptance extends \Codeception\Module
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
        $this->getModule('WebDriver')->_reconfigure(array($config => $value));
    }

    /**
     * Get config value for a given $configGroup by $configKey.
     *
     * @param string $configGroup
     * @param string $configKey
     * @return string | null
     */
    public function getConfiguration($configGroup, $configKey)
    {
        return isset($this->config[$configGroup][$configKey]) ? $this->config[$configGroup][$configKey] : null;
    }
}
