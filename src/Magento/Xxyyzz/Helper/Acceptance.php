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
        $this->getModule('\Magento\Xxyyzz\Module\MagentoWebDriver')->_reconfigure(array($config => $value));
    }

    /**
     * Get WebDriver configuration.
     *
     * @param string $config
     * @return string
     */
    public function getConfiguration($config)
    {
        return $this->getModule('\Magento\Xxyyzz\Module\MagentoWebDriver')->_getConfig($config);
    }
}
